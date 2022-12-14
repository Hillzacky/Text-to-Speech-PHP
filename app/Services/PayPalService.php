<?php

namespace App\Services;

use App\Traits\ConsumesExternalServiceTrait;
use App\Http\Controllers\User\PromocodeController;
use App\Events\PaymentReferrerBonus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Services\Statistics\UserService;
use App\Events\PaymentProcessed;
use App\Models\Payment;
use App\Models\PrepaidPlan;
use App\Models\Plan;
use App\Models\User;

class PayPalService 
{
    use ConsumesExternalServiceTrait;

    protected $baseURI;
    protected $clientID;
    protected $clientSecret;
    protected $promocode;
    private $api;

    /**
     * Paypal payment processing, unless you are familiar with 
     * Paypal's REST API, we recommend not to modify core payment processing functionalities here.
     * Part that are writing data to the database can be edited as you prefer.
     */
    public function __construct()
    {
        $this->api = new UserService();

        $verify = $this->api->verify_license();

        if($verify['status']!=true){
            return false;
        }

        $this->baseURI = config('services.paypal.base_uri');
        $this->clientID = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->promocode = new PromocodeController();
    }


    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $headers['Authorization'] = $this->resolveAccessToken();
    }


    public function decodeResponse($response)
    {
        return json_decode($response);
    }


    public function resolveAccessToken()
    {
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return;
        }
        
        $credentials = base64_encode("{$this->clientID}:{$this->clientSecret}");

        return "Basic {$credentials}";
    }


    public function handlePaymentSubscription(Request $request, Plan $id)
    {   
        if (!$id->paypal_gateway_plan_id) {
            return redirect()->back()->with('error', 'Paypal plan id is not set. Please contact the support team.');
        }
        
        try {
            $subscription = $this->createSubscription($id, $request->user()->name, $request->user()->email);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Paypal authentication error, verify your paypal settings first. ' . $e->getMessage());
        }        

        $subscriptionLinks = collect($subscription->links);

        $approve = $subscriptionLinks->where('rel', 'approve')->first();

        session()->put('subscriptionID', $subscription->id);

        return redirect($approve->href);
    }


    public function handlePaymentPrePaid(Request $request, PrepaidPlan $id)
    {   
        $tax_value = (config('payment.payment_tax') > 0) ? $tax = $id->cost * config('payment.payment_tax') / 100 : 0;
        $total_value = $tax_value + $id->cost;

        $discounted_price = $this->promocode->calculatePromocode($request->promo_code, $total_value);
        $total_discount = ($discounted_price) ? $total_value - $discounted_price : 0;
        $final_price = ($discounted_price) ? $discounted_price : $total_value;

        if ($final_price == '0.00') {
            return $this->recordFreeUsage($id, $total_discount);
        }

        try {
            $order = $this->createOrder($final_price, $request->currency);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Paypal authentication error, verify your paypal settings first. ' . $e->getMessage());
        }
        

        $orderLinks = collect($order->links);

        $approve = $orderLinks->where('rel', 'approve')->first();

        session()->put('approvalID', $order->id);
        session()->put('user_promocode', $request->promo_code);
        session()->put('total_discount', $total_discount);
        session()->put('plan_id', $id);

        return redirect($approve->href);
    }


    public function handleApproval()
    {
        if (session()->has('approvalID')) {
            $approvalID = session()->get('approvalID');
            $plan = session()->get('plan_id');

            $total_discount = (session()->has('total_discount')) ? session()->get('total_discount') : 0;            
           
            try {
                $payment = $this->capturePayment($approvalID);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Paypal payment capture error. Verify your paypal merchant account settings. ' . $e->getMessage());
            }
            

            $name = $payment->payer->name->given_name;
            $payment = $payment->purchase_units[0]->payments->captures[0]->amount;
            $amount = $payment->value;
            $currency = $payment->currency_code;

            if (config('payment.referral.payment.enabled') == 'on') {
                if (config('payment.referral.payment.policy') == 'first') {
                    if (Payment::where('user_id', auth()->user()->id)->where('status', 'Success')->exists()) {
                        /** User already has at least 1 payment and referrer already received credit for it */
                    } else {
                        event(new PaymentReferrerBonus(auth()->user(), $approvalID, $amount, 'PayPal'));
                    }
                } else {
                    event(new PaymentReferrerBonus(auth()->user(), $approvalID, $amount, 'PayPal'));
                }
            }

            $record_payment = new Payment();
            $record_payment->user_id = auth()->user()->id;
            $record_payment->order_id = $approvalID;
            $record_payment->plan_id = $plan->id;
            $record_payment->discount = $total_discount;
            $record_payment->plan_type = $plan->plan_type;
            $record_payment->plan_name = $plan->plan_name;
            $record_payment->amount = $amount;
            $record_payment->currency = $currency;
            $record_payment->gateway = 'PayPal';
            $record_payment->status = 'Success';
            $record_payment->characters = $plan->characters + $plan->bonus;
            $record_payment->save();
            
            $group = (auth()->user()->hasRole('admin'))? 'admin' : 'subscriber';

            $total_chars = auth()->user()->available_chars + $plan->characters + $plan->bonus;
            $user = User::where('id',auth()->user()->id)->first();
            $user->syncRoles($group);    
            $user->group = $group;
            $user->available_chars = $total_chars;
            $user->save();       

            event(new PaymentProcessed(auth()->user()));
            $order_id = $approvalID;

            return view('user.balance.subscriptions.success', compact('plan', 'order_id'));
        }

        return redirect()->back()->with('error', 'Payment was not successful, please try again');
    }


    public function createOrder($value, $currency)
    {
        return $this->makeRequest(
            'POST',
            '/v2/checkout/orders',
            [],
            [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    0 => [
                        'amount' => [
                            'currency_code' => strtoupper($currency),
                            'value' => round($value * $factor = $this->resolveFactor($currency)) / $factor,
                        ]
                    ]   
                ],   
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                    'return_url' => route('user.payments.approved'),
                    'cancel_url' => route('user.payments.cancelled'),
                ]
            ],            
            [],
            $isJSONRequest = true,
        );
    }


    public function capturePayment($approvalID)
    {
        return $this->makeRequest(
            'POST',
            "/v2/checkout/orders/{$approvalID}/capture",
            [],
            [],
            [
                'Content-Type' => 'application/json'
            ]
        );
    }    


    public function resolveFactor($currency)
    {
        $zeroDecimanCurrency = ['JPY'];

        if (in_array(strtoupper($currency), $zeroDecimanCurrency)) {
            return 1;
        }

        return 100;
    }


    public function createSubscription(Plan $id, $name, $email)
    {
        return $this->makeRequest(
            'POST',
            '/v1/billing/subscriptions',
            [],
            [   
                'plan_id' => $id->paypal_gateway_plan_id,
                'subscriber' => [
                    'name' => [
                        'given_name' => $name,
                    ],
                    'email_address' => $email,
                ],   
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'SUBSCRIBE_NOW',
                    'return_url' => route('user.payments.subscription.approved', ['plan_id' => $id->id]),
                    'cancel_url' => route('user.payments.subscription.cancelled'),
                ]
            ],            
            [],
            $isJSONRequest = true,
        );
    }


    public function stopSubscription($subscriptionID)
    {
        return $this->makeRequest(
            'POST',
            '/v1/billing/subscriptions/' . $subscriptionID . '/cancel',
            [],
            [   
                'reason' => 'Just want to unsubscribe'
            ],            
            [],
            $isJSONRequest = true,
        );
    }


    public function validateSubscriptions(Request $request)
    {
        if (session()->has('subscriptionID')) {
            $subscriptionID = session()->get('subscriptionID');

            session()->forget('subscriptionID');

            return $request->subscription_id == $subscriptionID;
        }

        return false;
    }


    public function recordFreeUsage($plan, $total_discount)
    {           

            $record_payment = new Payment();
            $record_payment->user_id = auth()->user()->id;
            $record_payment->order_id = '100% Discount Promocode';
            $record_payment->plan_id = $plan->id;
            $record_payment->discount = $total_discount;
            $record_payment->plan_type = $plan->plan_type;
            $record_payment->plan_name = $plan->plan_name;
            $record_payment->amount = 0;
            $record_payment->currency = $plan->currency;
            $record_payment->gateway = 'Promocode';
            $record_payment->status = 'Success';
            $record_payment->characters = $plan->characters + $plan->bonus;
            $record_payment->save();
            
            $group = (auth()->user()->hasRole('admin'))? 'admin' : 'subscriber';

            $total_chars = auth()->user()->available_chars + $plan->characters + $plan->bonus;
            $user = User::where('id',auth()->user()->id)->first();
            $user->syncRoles($group);    
            $user->group = $group;
            $user->available_chars = $total_chars;
            $user->save();       

            event(new PaymentProcessed(auth()->user()));
            $order_id = '100% Discount Promocode';

            return view('user.balance.subscriptions.success', compact('plan', 'order_id'));
        
    }

}