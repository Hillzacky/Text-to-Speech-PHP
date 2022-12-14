<?php

namespace App\Services;

use App\Traits\ConsumesExternalServiceTrait;
use App\Http\Controllers\User\PromocodeController;
use Illuminate\Support\Facades\Storage;
use App\Events\PaymentReferrerBonus;
use Illuminate\Http\Request;
use App\Events\PaymentProcessed;
use App\Models\Payment;
use App\Models\PrepaidPlan;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;

class BankTransferService 
{
    use ConsumesExternalServiceTrait;


    protected $promocode;


    public function __construct()
    {
        $this->promocode = new PromocodeController();
    }


    public function handlePaymentSubscription(Request $request, Plan $id)
    {   
        if (session()->has('bank_order_id')) {
            $orderID = session()->get('bank_order_id');
            session()->forget('bank_order_id');
        }

        $duration = ($id->pricing_plan == 'monthly') ? 30 : 365;

        $subscription = Subscription::create([
            'active_until' => now()->addDays($duration),
            'user_id' => auth()->user()->id,
            'plan_id' => $id->id,
            'status' => 'Pending',
            'created_at' => now(),
            'gateway' => 'BankTransfer',
            'characters' => $id->characters,
            'bonus'=> $id->bonus,
            'subscription_id' => $orderID,
        ]);

        $tax_value = (config('payment.payment_tax') > 0) ? $tax = $id->cost * config('payment.payment_tax') / 100 : 0;
        $total_price = $tax_value + $id->cost;

        $record_payment = new Payment();
        $record_payment->user_id = auth()->user()->id;
        $record_payment->plan_id = $id->id;
        $record_payment->discount = 0;
        $record_payment->order_id = $orderID;
        $record_payment->plan_type = $id->plan_type;
        $record_payment->plan_name = $id->plan_name;
        $record_payment->amount = $total_price;
        $record_payment->currency = $id->currency;
        $record_payment->gateway = 'BankTransfer';
        $record_payment->status = 'Pending';
        $record_payment->characters = $id->characters + $id->bonus;
        $record_payment->save();      

        event(new PaymentProcessed(auth()->user()));

        $bank_information = ['bank_requisites'];
        $bank = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $bank_information)) {
                $bank[$row['name']] = $row['value'];
            }
        }

        return view('user.balance.subscriptions.banktransfer-success', compact('id', 'orderID', 'bank', 'total_price'));
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

        if (session()->has('bank_order_id')) {
            $orderID = session()->get('bank_order_id');
            session()->forget('bank_order_id');
        }

        $record_payment = new Payment();
        $record_payment->user_id = auth()->user()->id;
        $record_payment->order_id = $orderID;
        $record_payment->plan_id = $id->id;
        $record_payment->discount = $total_discount;
        $record_payment->plan_type = $id->plan_type;
        $record_payment->plan_name = $id->plan_name;
        $record_payment->amount = $final_price;
        $record_payment->currency = $id->currency;
        $record_payment->gateway = 'BankTransfer';
        $record_payment->status = 'Pending';
        $record_payment->characters = $id->characters + $id->bonus;
        $record_payment->save();
             
        event(new PaymentProcessed(auth()->user()));

        $bank_information = ['bank_requisites'];
        $bank = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $bank_information)) {
                $bank[$row['name']] = $row['value'];
            }
        }

        return view('user.balance.subscriptions.banktransfer-success', compact('id', 'orderID', 'bank', 'final_price'));
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