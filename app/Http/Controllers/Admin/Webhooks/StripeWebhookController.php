<?php

namespace App\Http\Controllers\Admin\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class StripeWebhookController extends Controller
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
    public function handleStripe(Request $request)
    {
        if ($this->api->api_url != 'https://license.berkine.space/') {
            die();
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.client_id'));

        $endpoint_secret = config('services.stripe.webhook_secret');

       
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;


        try {

            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

        } catch(\UnexpectedValueException $e) {
            
            exit();

        } catch(\Stripe\Exception\SignatureVerificationException $e) {

            exit();

        }


        switch ($event->type) {
            case 'customer.subscription.deleted': 
                $subscription = Subscription::where('subscription_id', $event->data->object->id)->firstOrFail();
                $subscription->update(['status'=>'Cancelled', 'active_until' => now()]);
                
                $user = User::where('id', $subscription->user_id)->firstOrFail();
                $user->update(['plan_id' => null]);
           
                break;
            case 'invoice.payment_failed':
                $subscription = Subscription::where('subscription_id', $event->data->object->id)->firstOrFail();
                $subscription->update(['status'=>'Expired', 'active_until' => now()]);
                
                $user = User::where('id', $subscription->user_id)->firstOrFail();
                $user->update(['plan_id' => null]);
          
                break;
            case 'invoice.paid':

                $subscription = Subscription::where('subscription_id', $event->data->object->id)->where('status', 'Expired')->firstOrFail();

                if ($subscription) {
                    $plan = Plan::where('id', $subscription->plan_id)->firstOrFail();
                    $duration = ($plan->pricing_plan == 'monthly') ? 30 : 365;

                    $subscription->update(['status'=>'Active', 'active_until' => Carbon::now()->addDays($duration)]);
                    
                    $user = User::where('id', $subscription->user_id)->firstOrFail();
                    $total_chars = $user->available_chars + $plan->characters + $plan->bonus;
                    $user->update(['plan_id' => $subscription->plan_id, 'available_chars' => $total_chars]);
                }
                
            
                break;
        }
    }
}
