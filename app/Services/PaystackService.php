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
use App\Models\Subscription;
use App\Models\User;


class PaystackService 
{
    use ConsumesExternalServiceTrait;

    protected $baseURI;
    protected $apiSecret;
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

        $this->baseURI = config('services.paystack.base_uri');
        $this->apiSecret = config('services.paystack.api_secret');
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

        return "Bearer {$this->apiSecret}";
    }


    public function handlePaymentSubscription(Request $request, Plan $id)
    {   
        if (!$id->paystack_gateway_plan_id) {
            return redirect()->back()->with('error', 'Paystack plan id is not set. Please contact the support team.');
        } 

        $price = intval($id->cost * 100);

        try {
            $subscription = $this->createSubscription($id, $request->paystack_email, $price);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Paystack authentication error, verify your paystack settings first. ' . $e->getMessage());
        }
        

        if ($subscription->status == true) {

            session()->put('subscriptionID', $subscription->data->reference);

            return redirect($subscription->data->authorization_url);
        } else {
            return redirect()->back()->with('error', 'There was an error with Paystack connection, please try again.');
        }

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

        $price = intval($final_price * 100);

        try {
            $order = $this->initializeTransaction($price, $request->currency, $request->paystack_email);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Paystack authentication error, verify your paystack settings first. ' . $e->getMessage());
        }
        

        if ($order->status == true) {

            session()->put('order_reference', $order->data->reference);
            session()->put('user_promocode', $request->promo_code);
            session()->put('total_discount', $total_discount);
            session()->put('plan_id', $id);

            return redirect($order->data->authorization_url);
        } else {
            return redirect()->back()->with('error', 'There was an error with Paystack connection, please try again.');
        }

    }


    public function handleApproval()
    {
        if (session()->has('order_reference')) {
            $approvalID = session()->get('order_reference');
            $plan = session()->get('plan_id');

            $total_discount = (session()->has('total_discount')) ? session()->get('total_discount') : 0;            

            try {
                $reference = $this->verifyTransaction($approvalID);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Paystack transaction verication failed, please try again or contact support. ' . $e->getMessage());
            }            

            if ($reference->data->status == 'success') {

                $amount = $reference->data->amount / 100;
                $currency = $reference->data->currency;

                if (config('payment.referral.payment.enabled') == 'on') {
                    if (config('payment.referral.payment.policy') == 'first') {
                        if (Payment::where('user_id', auth()->user()->id)->where('status', 'Success')->exists()) {
                            /** User already has at least 1 payment and referrer already received credit for it */
                        } else {
                            event(new PaymentReferrerBonus(auth()->user(), $approvalID, $amount, 'Paystack'));
                        }
                    } else {
                        event(new PaymentReferrerBonus(auth()->user(), $approvalID, $amount, 'Paystack'));
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
                $record_payment->gateway = 'Paystack';
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
            
            } else {              

                return redirect()->back()->with('error', 'Your payment was not successful or was cancelled from your side');
            }
            
        }

        return redirect()->back()->with('error', 'Payment was not successful, please try again');
    }


    public function addPaystackFields($reference, $subscription_id)
    {
        $order = $this->verifyTransaction($reference);
        $customer_code = $order->data->customer->customer_code;

        $authorization_code = '';
        $subscription_code = '';
        $email_token = '';

        try {
            $paystack_subscriptions = $this->listSubscriptions();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Paystack list subscriptions failed, please try again or contact support. ' . $e->getMessage());
        }
                

        if ($paystack_subscriptions->status == true) {
            foreach ($paystack_subscriptions->data as $value) {
                if($value->customer->customer_code == $customer_code) {
                    $authorization_code = $value->authorization->authorization_code;
                    $subscription_code = $value->subscription_code;
                    $email_token = $value->email_token;
                    break;
                }
            }
        }

        Subscription::where('id', $subscription_id)->update([
            'paystack_customer_code' => $customer_code,
            'paystack_authorization_code' => $authorization_code,
            'paystack_email_token' => $email_token,
            'subscription_id' => $subscription_code,
        ]);        
        
    }


    public function initializeTransaction($value, $currency, $user_email)
    {
        return $this->makeRequest(
            'POST',
            '/transaction/initialize',
            [],
            [           
                'email' => $user_email,
                'amount' => $value, 
                'callback_url' => route('user.payments.approved'),
                "metadata" => [
                    "cancel_action" => route('user.payments.cancelled'),
                ],
            ],            
            [],
            $isJSONRequest = true,
        );
    }


    public function verifyTransaction($reference)
    {
        return $this->makeRequest(
            'GET',
            "/transaction/verify/{$reference}",
            [],
            [],            
            [],
            $isJSONRequest = true,
        );
    }


    public function createSubscription(Plan $id, $user_email, $price)
    {
        return $this->makeRequest(
            'POST',
            '/transaction/initialize',
            [],
            [           
                'email' => $user_email,
                'amount' => $price, 
                'plan' => $id->paystack_gateway_plan_id,
                'callback_url' => route('user.payments.subscription.approved', ['plan_id' => $id->id]),
                "metadata" => [
                    "cancel_action" => route('user.payments.subscription.cancelled'),
                ],
            ],            
            [],
            $isJSONRequest = true,
        );

    }


    public function stopSubscription($subscription_id)
    {
        $subscription = Subscription::where('subscription_id', $subscription_id)->firstOrFail();
        $token = $subscription->paystack_email_token;

        return $this->makeRequest(
            'POST',
            '/subscription/disable',
            [],
            [   
                'code' => $subscription_id,
                'token' => $token,
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

            return $request->reference == $subscriptionID;
        }

        return false;
    }


    public function listSubscriptions()
    {
        return $this->makeRequest(
            'GET',
            "/subscription",
            [],
            [],            
            [],
            $isJSONRequest = true,
        );
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