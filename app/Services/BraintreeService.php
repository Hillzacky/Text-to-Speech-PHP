<?php

namespace App\Services;

use App\Http\Controllers\User\PromocodeController;
use Illuminate\Http\Request;
use App\Services\Statistics\UserService;
use App\Events\PaymentReferrerBonus;
use App\Events\PaymentProcessed;
use App\Models\Payment;
use App\Models\PrepaidPlan;
use App\Models\User;


class BraintreeService 
{

    protected $gateway;
    protected $promocode;
    private $api;


    public function __construct()
    {
        $this->api = new UserService();

        $verify = $this->api->verify_license();

        if($verify['status']!=true){
            return false;
        }

        try {
            $this->gateway = new \Braintree\Gateway([
                'environment' => config('services.braintree.env'),
                'merchantId' => config('services.braintree.merchant_id'),
                'publicKey' => config('services.braintree.public_key'),
                'privateKey' => config('services.braintree.private_key')
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Braintree authentication error, verify your braintree settings first. ' . $e->getMessage());
        }
        

        $this->promocode = new PromocodeController();        
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

            $clientToken = $this->gateway->clientToken()->generate();
            
            session()->put('total_discount', $total_discount);
            session()->put('plan_id', $id);
            session()->put('total_amount', $final_price);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Braintree authentication error, verify your braintree settings first. ' . $e->getMessage());
        }

        return view('user.balance.subscriptions.braintree-checkout', compact('id', 'clientToken'));

    }


    public function handleApproval(Request $request)
    {        
        $payload = $request->input('payload', false);
        $nonce = $payload['nonce'];
        $total_amount = session()->get('total_amount');

        try {
            $result = $this->gateway->transaction()->sale([
                'amount' => $total_amount,
                'paymentMethodNonce' => $nonce,
                'options' => [
                    'submitForSettlement' => True
                ]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Braintree transaction error, verify your braintree settings first. ' . $e->getMessage());
        }        
        
        $plan = session()->get('plan_id');
        $total_discount = (session()->has('total_discount')) ? session()->get('total_discount') : 0; 

        if ($result->success) {

            if (config('payment.referral.payment.enabled') == 'on') {
                if (config('payment.referral.payment.policy') == 'first') {
                    if (Payment::where('user_id', auth()->user()->id)->where('status', 'Success')->exists()) {
                        /** User already has at least 1 payment */
                    } else {
                        event(new PaymentReferrerBonus(auth()->user(), $result->transaction->id, $result->transaction->amount, 'Braintree'));
                    }
                } else {
                    event(new PaymentReferrerBonus(auth()->user(), $result->transaction->id, $result->transaction->amount, 'Braintree'));
                }
            }

            $record_payment = new Payment();
            $record_payment->user_id = auth()->user()->id;
            $record_payment->order_id = $result->transaction->id;
            $record_payment->plan_id = $plan->id;
            $record_payment->plan_type = $plan->plan_type;
            $record_payment->plan_name = $plan->plan_name;
            $record_payment->discount = $total_discount;
            $record_payment->amount = $result->transaction->amount;
            $record_payment->currency = $result->transaction->currencyIsoCode;
            $record_payment->gateway = 'Braintree';
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

            $data['result'] = $result;
            $data['plan'] = $plan->id;
            $data['order_id'] = $result->transaction->id;

            return response()->json($data);

        } else {
            return redirect()->back()->with('error', 'Payment was not successful, please try again');
        }

                
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