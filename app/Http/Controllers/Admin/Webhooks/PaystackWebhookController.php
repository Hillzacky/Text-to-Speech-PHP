<?php

namespace App\Http\Controllers\Admin\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;

class PaystackWebhookController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = new LicenseController();
    }

    /**
     * Stripe Webhook processing, unless you are familiar with 
     * Stripe's PHP API, we recommend not to modify it
     */
    public function handlePaystack(Request $request)
    {
        if ($this->api->api_url != 'https://license.berkine.space/') {
            die();
        }

        // only a post with paystack signature header gets our attention
        if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) || !array_key_exists('x-paystack-signature', $_SERVER) ) {
            exit();
        }
            
        // Retrieve the request's body
        $input = @file_get_contents("php://input");

        define('PAYSTACK_SECRET_KEY', config('services.paystack.api_secret'));

        // validate event do all at once to avoid timing attack
        if($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, PAYSTACK_SECRET_KEY)) {
            exit();
        }
            
        http_response_code(200);

        // parse event (which is json string) as object
        // Do something - that will not take long - with $event
        $event = json_decode($input);

        switch ($event->event) {
            case 'subscription.disable': 
                $subscription = Subscription::where('subscription_id', $event->data->subscription_code)->firstOrFail();
                $subscription->update(['status'=>'Cancelled', 'active_until' => now()]);
                
                $user = User::where('id', $subscription->user_id)->firstOrFail();
                $user->update(['plan_id' => null]);
           
                break;
            case 'charge.success':
                $subscription = Subscription::where('subscription_id', $event->data->subscription_code)->where('status', 'Expired')->firstOrFail();

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
