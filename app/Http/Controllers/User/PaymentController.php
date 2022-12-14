<?php

namespace App\Http\Controllers\User;

use App\Traits\InvoiceGeneratorTrait;
use App\Http\Controllers\Controller;
use App\Events\PaymentReferrerBonus;
use App\Services\PaymentPlatformResolverService;
use App\Events\PaymentProcessed;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PrepaidPlan;
use App\Models\PaymentPlatform;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;


class PaymentController extends Controller
{   
    use InvoiceGeneratorTrait;

    protected $paymentPlatformResolver;

    
    public function __construct(PaymentPlatformResolverService $paymentPlatformResolver)
    {
        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pay(Request $request, Plan $id)
    {
        if ($id->free) {

            $order_id = $this->registerFreeSubscription($id);
            $plan = Plan::where('id', $id->id)->first();

            return view('user.balance.subscriptions.success', compact('plan', 'order_id'));

        } else {

            $rules = [
                'payment_platform' => ['required', 'exists:payment_platforms,id'],
            ];

            $request->validate($rules);

            $paymentPlatform = $this->paymentPlatformResolver->resolveService($request->payment_platform);

            session()->put('subscriptionPlatformID', $request->payment_platform);
            session()->put('gatewayID', $request->payment_platform);
            
            return $paymentPlatform->handlePaymentSubscription($request, $id);
        }
    }


    /**
     * Process prepaid plan request
     */
    public function payPrePaid(Request $request, PrepaidPlan $id)
    {
        $rules = [
            'payment_platform' => ['required', 'exists:payment_platforms,id'],
        ];

        $request->validate($rules);


        $paymentPlatform = $this->paymentPlatformResolver->resolveService($request->payment_platform);
           

        session()->put('paymentPlatformID', $request->payment_platform);
    
        return $paymentPlatform->handlePaymentPrePaid($request, $id);       
    }


    /**
     * Process approved prepaid plan requests
     */
    public function approved(Request $request)
    {   
        if (session()->has('paymentPlatformID')) {
            $paymentPlatform = $this->paymentPlatformResolver->resolveService(session()->get('paymentPlatformID'));

            return $paymentPlatform->handleApproval($request);
        }

        return redirect()->back()->with('error', 'There was an error while retrieving payment gateway. Please try again');
    }


    /**
     * Process approved prepaid plan request for Razorpay
     */
    public function approvedRazorpayPrepaid(Request $request)
    {   
        if (session()->has('paymentPlatformID')) {
            $paymentPlatform = $this->paymentPlatformResolver->resolveService(session()->get('paymentPlatformID'));

            return $paymentPlatform->handleApproval($request);
        }

        return redirect()->back()->with('error', 'There was an error while retrieving payment gateway. Please try again');
    }


    /**
     * Process approved prepaid plan request for Braintree
     */
    public function braintreeSuccess(Request $request)
    {
        $plan = PrepaidPlan::where('id', $request->plan)->first();
        $order_id = request('amp;order');
        
        return view('user.balance.subscriptions.success', compact('plan', 'order_id'));
    }


    /**
     * Process cancelled prepaid plan requests
     */
    public function cancelled()
    {
        return redirect()->route('user.subscriptions')->with('error', 'You cancelled the payment process. Would like to try again?');
    }


    /**
     * Process approved subscription plan requests
     */
    public function approvedSubscription(Request $request)
    {   
        if (session()->has('subscriptionPlatformID')) {
            $paymentPlatform = $this->paymentPlatformResolver->resolveService(session()->get('subscriptionPlatformID'));

            if (session()->has('subscriptionID')) {
                $subscriptionID = session()->get('subscriptionID');
            }

            if ($paymentPlatform->validateSubscriptions($request)) {

                $plan = Plan::where('id', $request->plan_id)->firstOrFail();
                $user = $request->user();

                $gateway_id = session()->get('gatewayID');
                $gateway = PaymentPlatform::where('id', $gateway_id)->firstOrFail();
                $duration = ($plan->pricing_plan == 'monthly') ? 30 : 365;

                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'status' => 'Active',
                    'created_at' => now(),
                    'gateway' => $gateway->name,
                    'characters' => $plan->characters,
                    'bonus'=> $plan->bonus,
                    'subscription_id' => $subscriptionID,
                    'active_until' => Carbon::now()->addDays($duration),
                ]);       

                // Only for Paystack
                if ($gateway_id == 4) {
                    $reference = $paymentPlatform->addPaystackFields($request->reference, $subscription->id);
                }

                // Only for Mollie
                if ($gateway_id == 7) {
                    $reference = $paymentPlatform->addMollieFields($plan, $subscription->id);
                }

                session()->forget('gatewayID');

                $this->registerSubscriptionPayment($plan, $user, $subscriptionID, $gateway->name);               
                $order_id = $subscriptionID;

                return view('user.balance.subscriptions.success', compact('plan', 'order_id'));
            }
        }

        return redirect()->back()->with('error', 'There was an error while checking your subscription. Please try again');
    }


    /**
     * Process approved razorpay subscription plan requests
     */
    public function approvedRazorpaySubscription(Request $request)
    {   
        if (session()->has('subscriptionPlatformID')) {
            $paymentPlatform = $this->paymentPlatformResolver->resolveService(session()->get('subscriptionPlatformID'));

            if (session()->has('subscriptionID')) {
                $subscriptionID = session()->get('subscriptionID');
            }

            if ($paymentPlatform->validateSubscriptions($request)) {

                $plan = Plan::where('id', $request->plan_id)->firstOrFail();

                $gateway_id = session()->get('gatewayID');
                $gateway = PaymentPlatform::where('id', $gateway_id)->firstOrFail();
                $duration = ($plan->pricing_plan == 'monthly') ? 30 : 365;

                $subscription = Subscription::create([
                    'user_id' => auth()->user()->id,
                    'plan_id' => $plan->id,
                    'status' => 'Active',
                    'created_at' => now(),
                    'gateway' => $gateway->name,
                    'characters' => $plan->characters,
                    'bonus'=> $plan->bonus,
                    'subscription_id' => $subscriptionID,
                    'active_until' => Carbon::now()->addDays($duration),
                ]);       

                session()->forget('gatewayID');

                $this->registerSubscriptionPayment($plan, auth()->user(), $subscriptionID, $gateway->name);               
                $order_id = $subscriptionID;

                return view('user.balance.subscriptions.success', compact('plan', 'order_id'));
            }
        }

        return redirect()->route('user.subscriptions')->with('error', 'There was an error with payment verification. Please try again or contact support.');
    }


    /**
     * Process cancelled subscription plan requests
     */
    public function cancelledSubscription()
    {
        return redirect()->route('user.subscriptions')->with('error', 'You cancelled the payment process. Would like to try again?');
    }


    /**
     * Register subscription payment in DB
     */
    private function registerSubscriptionPayment(Plan $plan, User $user, $subscriptionID, $gateway)
    {
        $tax_value = (config('payment.payment_tax') > 0) ? $tax = $plan->cost * config('payment.payment_tax') / 100 : 0;
        $total_price = $tax_value + $plan->cost;

        if (config('payment.referral.payment.enabled') == 'on') {
            if (config('payment.referral.payment.policy') == 'first') {
                if (Payment::where('user_id', $user->id)->where('status', 'Success')->exists()) {
                    /** User already has at least 1 payment */
                } else {
                    event(new PaymentReferrerBonus(auth()->user(), $subscriptionID, $total_price, $gateway));
                }
            } else {
                event(new PaymentReferrerBonus(auth()->user(), $subscriptionID, $total_price, $gateway));
            }
        }

        $record_payment = new Payment();
        $record_payment->user_id = $user->id;
        $record_payment->plan_id = $plan->id;
        $record_payment->discount = 0;
        $record_payment->order_id = $subscriptionID;
        $record_payment->plan_type = $plan->plan_type;
        $record_payment->plan_name = $plan->plan_name;
        $record_payment->amount = $total_price;
        $record_payment->currency = $plan->currency;
        $record_payment->gateway = $gateway;
        $record_payment->status = 'Success';
        $record_payment->characters = $plan->characters + $plan->bonus;
        $record_payment->save();
        
        $group = ($user->hasRole('admin'))? 'admin' : 'subscriber';

        $total_chars = $user->available_chars + $plan->characters + $plan->bonus;
        $user = User::where('id', $user->id)->first();
        $user->syncRoles($group);    
        $user->group = $group;
        $user->plan_id = $plan->id;
        $user->available_chars = $total_chars;
        $user->save();       

        event(new PaymentProcessed(auth()->user()));
   
    }   
    
    
    /**
     * Generate Invoice after payment
     */
    public function generatePaymentInvoice($order_id)
    {      
        $promocode = (session()->has('user_promocode')) ? session()->get('user_promocode') : false;
        
        $this->generateInvoice($order_id, $promocode);
    }


    /**
     * Bank Transfer Invoice
     */
    public function bankTransferPaymentInvoice($order_id)
    {
        $this->bankTransferInvoice($order_id);
    }


    /**
     * Show invoice for past payments
     */
    public function showPaymentInvoice(Payment $id)
    {   
        if ($id->gateway == 'BankTransfer' && $id->status != 'Success') {
            $this->bankTransferInvoice($id->order_id);
        } else {          
            $this->showInvoice($id);
        }
    }


    /**
     * Cancel active subscription
     */
    public function stopSubscription(Subscription $id)
    {   
        if ($id->status == 'Cancelled') {
            return redirect()->back()->with('success', 'This subscription is already cancelled');
        } elseif ($id->status == 'Suspended') {
            return redirect()->back()->with('error', 'Subscription has been suspended due to failed renewal payment');
        } elseif ($id->status == 'Expired') {
            return redirect()->back()->with('error', 'Subscription has been expired, please create a new one');
        }
        
        switch ($id->gateway) {
            case 'PayPal':
                $platformID = 1;
                break;
            case 'Stripe':
                $platformID = 2;
                break;
            case 'BankTransfer':
                $platformID = 3;
                break;
            case 'Paystack':
                $platformID = 4;
                break;
            case 'Razorpay':
                $platformID = 5;
                break;
            case 'Mollie':
                $platformID = 7;
                break;
            case 'MyBalance':
                $platformID = 10;
                break;
            case 'FREE':
                $platformID = 99;
                break;
            default:
                $platformID = 1;
                break;
        }


        if ($id->gateway == 'PayPal' || $id->gateway == 'Stripe' || $id->gateway == 'Paystack' || $id->gateway == 'Razorpay' || $id->gateway == 'Mollie') {
            $paymentPlatform = $this->paymentPlatformResolver->resolveService($platformID);

            $status = $paymentPlatform->stopSubscription($id->subscription_id);

            if ($platformID == 2) {
                if ($status->cancel_at) {
                    $id->update(['status'=>'Cancelled', 'active_until' => now()]);
                    $user = User::where('id', $id->user_id)->firstOrFail();
                    $user->update(['plan_id' => null]);
                }
            } elseif ($platformID == 4) {
                if ($status->status) {
                    $id->update(['status'=>'Cancelled', 'active_until' => now()]);
                    $user = User::where('id', $id->user_id)->firstOrFail();
                    $user->update(['plan_id' => null]);
                }
            } elseif ($platformID == 5) {
                if ($status->status == 'cancelled') {
                    $id->update(['status'=>'Cancelled', 'active_until' => now()]);
                    $user = User::where('id', $id->user_id)->firstOrFail();
                    $user->update(['plan_id' => null]);
                }
            } elseif ($platformID == 7) {
                if ($status->status == 'canceled') {
                    $id->update(['status'=>'Cancelled', 'active_until' => now()]);
                    $user = User::where('id', $id->user_id)->firstOrFail();
                    $user->update(['plan_id' => null]);
                }
            } elseif ($platformID == 99) { 
                $id->update(['status'=>'Cancelled', 'active_until' => now()]);
                $user = User::where('id', $id->user_id)->firstOrFail();
                $user->update(['plan_id' => null]);
            } else {
                if (is_null($status)) {
                    $id->update(['status'=>'Cancelled', 'active_until' => now()]);
                    $user = User::where('id', $id->user_id)->firstOrFail();
                    $user->update(['plan_id' => null]);
                }
            }
        } else {
            $id->update(['status'=>'Cancelled', 'active_until' => now()]);
            $user = User::where('id', $id->user_id)->firstOrFail();
            $user->update(['plan_id' => null]);
        }


        return redirect()->back()->with('success', 'Your subscription has been successfully cancelled');
        
    }


    /**
     * Register free subscription
     */
    private function registerFreeSubscription(Plan $plan)
    {
        $order_id = Str::random(10);
        $subscription = Str::random(10);
        $duration = ($plan->pricing_plan == 'monthly') ? 30 : 365;

        $record_payment = new Payment();
        $record_payment->user_id = auth()->user()->id;
        $record_payment->plan_id = $plan->id;
        $record_payment->discount = 0;
        $record_payment->order_id = $order_id;
        $record_payment->plan_type = $plan->plan_type;
        $record_payment->plan_name = $plan->plan_name;
        $record_payment->amount = 0;
        $record_payment->currency = $plan->currency;
        $record_payment->gateway = 'FREE';
        $record_payment->status = 'Success';
        $record_payment->characters = $plan->characters + $plan->bonus;
        $record_payment->save();

        $subscription = Subscription::create([
            'user_id' => auth()->user()->id,
            'plan_id' => $plan->id,
            'status' => 'Active',
            'created_at' => now(),
            'gateway' => 'FREE',
            'characters' => $plan->characters,
            'bonus'=> $plan->bonus,
            'subscription_id' => $subscription,
            'active_until' => Carbon::now()->addDays($duration),
        ]); 
        
        $group = (auth()->user()->hasRole('admin'))? 'admin' : 'subscriber';

        $total_chars = auth()->user()->available_chars + $plan->characters + $plan->bonus;
        $user = User::where('id', auth()->user()->id)->first();
        $user->syncRoles($group);    
        $user->group = $group;
        $user->plan_id = $plan->id;
        $user->available_chars = $total_chars;
        $user->save();       
        
        return $order_id;
    }  

}
