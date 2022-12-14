<?php

namespace App\Listeners;

use App\Events\RegistrationReferrerBonus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Models\Referral;


class AddBonusCharactersListener
{

    /**
     * Handle the event.
     *
     * @param  ReferrerBonus  $event
     * @return void
     */
    public function handle(RegistrationReferrerBonus $event)
    {
        if ($event->user->referred_by !== '') {
            $referrer = User::where('id', $event->user->referred_by)->firstOrFail();
            $total = $referrer->available_chars + config('payment.referral.registration.credits');
            $referrer->available_chars = $total;
            $referrer->save();

            Referral::create([
                'referrer_id' => $referrer->id,
                'referrer_email' => $referrer->email,
                'referred_id' => $event->user->id,
                'referred_email' => $event->user->email,
                'credits' => config('payment.referral.registration.credits'),
            ]);  

        }  
    }
}
