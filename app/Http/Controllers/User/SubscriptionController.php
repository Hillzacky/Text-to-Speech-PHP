<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use Illuminate\Support\Str;
use App\Models\PaymentPlatform;
use App\Models\PrepaidPlan;
use App\Models\Setting;
use App\Models\Plan;

class SubscriptionController extends Controller
{   
    private $api;

    public function __construct()
    {
        $this->api = new LicenseController();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $monthly = Plan::where('status', 'active')->where('pricing_plan', 'monthly')->count();
        $yearly = Plan::where('status', 'active')->where('pricing_plan', 'yearly')->count();
        $prepaid = PrepaidPlan::where('status', 'active')->count();

        $monthly_subscriptions = Plan::where('status', 'active')->where('pricing_plan', 'monthly')->get();
        $yearly_subscriptions = Plan::where('status', 'active')->where('pricing_plan', 'yearly')->get();
        $prepaids = PrepaidPlan::where('plan_type', 'prepaid')->where('status', 'active')->get();

        return view('user.balance.subscriptions.index', compact('monthly', 'yearly', 'prepaid', 'monthly_subscriptions', 'yearly_subscriptions', 'prepaids'));
    }


    /**
     * Checkout for Pre Paid plans only.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkout(PrepaidPlan $id)
    {   
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return redirect()->back();
        }

        $payment_platforms = PaymentPlatform::where('enabled', 1)->get();
        
        $tax_value = (config('payment.payment_tax') > 0) ? $tax = $id->cost * config('payment.payment_tax') / 100 : 0;

        $total_value = $tax_value + $id->cost;
        $currency = $id->currency;

        $bank_information = ['bank_instructions', 'bank_requisites'];
        $bank = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $bank_information)) {
                $bank[$row['name']] = $row['value'];
            }
        }

        $bank_order_id = 'BT-' . strtoupper(Str::random(15));
        session()->put('bank_order_id', $bank_order_id);
        
        return view('user.balance.subscriptions.prepaid-checkout', compact('id', 'payment_platforms', 'tax_value', 'total_value', 'currency', 'bank', 'bank_order_id'));
    }


    /**
     * Checkout for Subscription plans only.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function subscribe(Plan $id)
    {   
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return redirect()->back();
        }
        
        $payment_platforms = PaymentPlatform::where('subscriptions_enabled', 1)->get();

        $tax_value = (config('payment.payment_tax') > 0) ? $tax = $id->cost * config('payment.payment_tax') / 100 : 0;

        $total_value = $tax_value + $id->cost;
        $currency = $id->currency;
        $gateway_plan_id = $id->gateway_plan_id;

        $bank_information = ['bank_instructions', 'bank_requisites'];
        $bank = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $bank_information)) {
                $bank[$row['name']] = $row['value'];
            }
        }

        $bank_order_id = 'BT-' . strtoupper(Str::random(15));
        session()->put('bank_order_id', $bank_order_id);

        return view('user.balance.subscriptions.subscribe-checkout', compact('id', 'payment_platforms', 'tax_value', 'total_value', 'currency', 'gateway_plan_id', 'bank', 'bank_order_id'));
    } 
}
