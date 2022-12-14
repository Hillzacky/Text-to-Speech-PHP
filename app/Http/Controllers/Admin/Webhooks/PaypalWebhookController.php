<?php

namespace App\Http\Controllers\Admin\Webhooks;

use App\Traits\ConsumesExternalServiceTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\User;

class PaypalWebhookController extends Controller
{
    use ConsumesExternalServiceTrait;

    protected $baseURI;
    protected $clientID;
    protected $clientSecret;

    /**
     * Paypal Webhook processing, unless you are familiar with 
     * Paypal's REST API, we recommend not to modify it
     */
    public function __construct()
    {
        $this->baseURI = config('services.paypal.base_uri');
        $this->clientID = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $headers['Authorization'] = $this->resolveAccessToken();
    }


    public function resolveAccessToken()
    {
        $credentials = base64_encode("{$this->clientID}:{$this->clientSecret}");

        return "Basic {$credentials}";
    }
    

    public function handlePaypal(Request $request)
    {
        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_UPPER);
        $webhook_body = json_decode(file_get_contents('php://input'));


        $status = $this->makeRequest(
            'POST',
            '/v1/notifications/verify-webhook-signature',
            [],
            [   
                'transmission_id' => $headers['PAYPAL-TRANSMISSION-ID'],
                'transmission_time' => $headers['PAYPAL-TRANSMISSION-TIME'],
                'cert_url' => $headers['PAYPAL-CERT-URL'],
                'auth_algo' => $headers['PAYPAL-AUTH-ALGO'],
                'transmission_sig' => $headers['PAYPAL-TRANSMISSION-SIG'],
                'webhook_id' => config('services.paypal.webhook_id'),
                'webhook_event' => $webhook_body
            ],            
            [],
            $isJSONRequest = true,
        );

        $result = json_decode($status, true);

        if ($result['verification_status'] == "SUCCESS") {

            switch ($webhook_body->event_type) {
                case 'BILLING.SUBSCRIPTION.CANCELLED':
                        $subscription = Subscription::where('subscription_id', $webhook_body->resource->id)->firstOrFail();
                        $subscription->update(['status'=>'Cancelled', 'active_until' => now()]);
                        
                        $user = User::where('id', $subscription->user_id)->firstOrFail();
                        $user->update(['plan_id' => null]);
                        break;
                
                case 'BILLING.SUBSCRIPTION.SUSPENDED':
                        $subscription = Subscription::where('subscription_id', $webhook_body->resource->id)->firstOrFail();
                        $subscription->update(['status'=>'Expired', 'active_until' => now()]);
                        
                        $user = User::where('id', $subscription->user_id)->firstOrFail();
                        $user->update(['plan_id' => null]);
                        break;

                case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
                        $subscription = Subscription::where('subscription_id', $webhook_body->resource->id)->firstOrFail();
                        $subscription->update(['status'=>'Expired', 'active_until' => now()]);
                        
                        $user = User::where('id', $subscription->user_id)->firstOrFail();
                        $user->update(['plan_id' => null]);
                        break;

                case 'BILLING.SUBSCRIPTION.EXPIRED':
                        $subscription = Subscription::where('subscription_id', $webhook_body->resource->id)->firstOrFail();
                        $subscription->update(['status'=>'Expired', 'active_until' => now()]);
                        
                        $user = User::where('id', $subscription->user_id)->firstOrFail();
                        $user->update(['plan_id' => null]);
                        break;
            }
        }

        

    }
}
