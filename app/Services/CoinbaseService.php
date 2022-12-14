<?php

namespace App\Services;

use App\Http\Controllers\User\PromocodeController;
use Illuminate\Http\Request;
use App\Services\Statistics\UserService;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\BadResponseException;
use App\Events\PaymentProcessed;
use App\Models\Payment;
use App\Models\PrepaidPlan;
use App\Models\User;


class CoinbaseService 
{

    protected $client;
    protected $promocode;
    private $api;

    /**
     * Stripe payment processing, unless you are familiar with 
     * Stripe's REST API, we recommend not to modify core payment processing functionalities here.
     * Part that are writing data to the database can be edited as you prefer.
     */
    public function __construct()
    {
        $this->api = new UserService();

        $verify = $this->api->verify_license();

        if($verify['status']!=true){
            return false;
        }

        $this->client = new HttpClient();
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
            $coinbase_request = $this->client->request('POST', 'https://api.commerce.coinbase.com/charges', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-CC-Api-Key' => config('services.coinbase.api_key'),
                        'X-CC-Version' => '2018-03-22',
                    ],
                    'body' => json_encode(array_merge_recursive([
                        'name' => 'Prepaid Plan Name: '. $id->plan_name,
                        'description' => 'Included Characters: '. number_format($id->characters),
                        'local_price' => [
                            'amount' => $final_price,
                            'currency' => $request->currency,
                        ],
                        'pricing_type' => 'fixed_price',
                        'metadata' => [
                            'user' => $request->user()->id,
                            'plan_id' => $id->id,
                            'amount' => $final_price,
                            'currency' => $request->currency,
                            'total_discount' => $total_discount,
                        ],
                        'redirect_url' => route('user.payments.approved'),
                        'cancel_url' => route('user.payments.cancelled'),
                    ]))
                ]
            );


            $coinbase = json_decode($coinbase_request->getBody()->getContents());

            $this->recordPayment($coinbase->data->code, $total_discount, $id, $final_price, $request->currency);

            session()->put('order_coinbase', $coinbase->data->code);
            session()->put('plan_coinbase', $id);
          
        } catch (BadResponseException $e) {
            return back()->with('error', 'Coinbase authentication error.' . $e->getResponse()->getBody()->getContents());
        }

        return redirect($coinbase->data->hosted_url);
    }


    public function recordPayment($payment_id, $total_discount, $plan, $amount, $currency)
    {        
        $record_payment = new Payment();
        $record_payment->user_id = auth()->user()->id;
        $record_payment->order_id = $payment_id;
        $record_payment->plan_id = $plan->id;
        $record_payment->plan_type = $plan->plan_type;
        $record_payment->plan_name = $plan->plan_name;
        $record_payment->discount = $total_discount;
        $record_payment->amount = $amount;
        $record_payment->currency = $currency;
        $record_payment->gateway = 'Coinbase';
        $record_payment->status = 'Pending';
        $record_payment->characters = $plan->characters + $plan->bonus;
        $record_payment->save();
    }


    public function handleApproval()
    {
        $order_id = session()->get('order_coinbase');
        $plan = session()->get('plan_coinbase');

        return view('user.balance.subscriptions.success', compact('plan', 'order_id'));
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