<?php

namespace App\Http\Controllers\Admin\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Events\PaymentReferrerBonus;
use App\Events\PaymentProcessed;
use App\Models\PrepaidPlan;
use App\Models\Payment;
use App\Models\User;

class CoinbaseWebhookController extends Controller
{
    /**
     * Stripe Webhook processing, unless you are familiar with 
     * Stripe's PHP API, we recommend not to modify it
     */
    public function handleCoinbase(Request $request)
    {
        $payload = json_decode($request->getContent());

        $computedSignature = hash_hmac('sha256', $request->getContent(), config('services.coinbase.webhook_secret'));

        if (hash_equals($computedSignature, $request->server('HTTP_X_CC_WEBHOOK_SIGNATURE'))) {

            $metadata = $payload->event->data->metadata ?? null;

            if (isset($metadata->user)) {

                $user = User::where('id', $metadata->user)->first();

                if ($user) {

                    if ($payload->event->type == 'charge:confirmed' || $payload->event->type == 'charge:resolved') {
                        
                        $payment = Payment::where('order_id', $payload->event->data->code)->first();
                        $plan = PrepaidPlan::where('id', $metadata->plan_id)->first();

                        if ($payment) {

                            $payment->status = 'Success';
                            $payment->save();

                            $group = ($user->hasRole('admin'))? 'admin' : 'subscriber';
                            $total_chars = $user->available_chars + $plan->characters + $plan->bonus;
                            $user->syncRoles($group);    
                            $user->group = $group;
                            $user->available_chars = $total_chars;
                            $user->save();

                            event(new PaymentProcessed($user));

                        }                                       
                    }
                }
            }

        } else {

            Log::info('Coinbase signature validation failed.');

            return response()->json(['status' => 400], 400);
        }

        return response()->json(['status' => 200], 200);
    }
    
}
