<?php

namespace App\Http\Controllers\Admin\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;

class RazorpaykWebhookController extends Controller
{
    /**
     * Stripe Webhook processing, unless you are familiar with 
     * Stripe's PHP API, we recommend not to modify it
     */
    public function handleRazorpay(Request $request)
    {
        $input = file_get_contents('php://input');
        $webhook_signature = $request->header('x-razorpay-signature');
        
        $body = json_decode($input, true);            

        $generated_signature = hash_hmac('sha256', $input, config('services.razorpay.webhook_secret'));

        if($generated_signature != $webhook_signature ) {
            exit();
        }

        switch ($body->event) {
            case 'subscription.cancelled': 
                $subscription = Subscription::where('subscription_id', $body->payload->subscription->id)->firstOrFail();
                $subscription->update(['status'=>'Cancelled', 'active_until' => now()]);
                
                $user = User::where('id', $subscription->user_id)->firstOrFail();
                $user->update(['plan_id' => null]);
                
                break;
            case 'subscription.charged':
                $subscription = Subscription::where('paystack_customer_code', $body->payload->subscription->id)->where('status', 'Expired')->firstOrFail();

                if ($subscription) {
                    $plan = Plan::where('id', $subscription->plan_id)->firstOrFail();
                    $duration = ($plan->pricing_plan == 'monthly') ? 30 : 365;

                    $subscription->update([
                        'status' => 'Active', 
                        'active_until' => Carbon::now()->addDays($duration)
                    ]);
                    
                    $user = User::where('id', $subscription->user_id)->firstOrFail();
                    $total_chars = $user->available_chars + $plan->characters + $plan->bonus;
                    $user->update(['plan_id' => $subscription->plan_id, 'available_chars' => $total_chars]);
                }                
          
                break;
        }

        
    }
}
