<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Subscription;

class Unsubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (optional($request->user())->hasActiveSubscription()) {
            
            return redirect()->route('user.tts')->with('success', 'You already have active subscription plan');
            
        }

        $subscription = Subscription::where('user_id', $request->user()->id)->where('status', 'Active')->first();
            
        if ($subscription) {
            return redirect()->route('user.tts')->with('success', 'You already have active subscription plan');
        }

        return $next($request);
    }
}
