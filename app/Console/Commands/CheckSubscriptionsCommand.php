<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Plan;
use Carbon\Carbon;

class CheckSubscriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if subsciptions are not expired';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $free_subscriptions = Subscription::where('status', 'Active')->where('gateway', 'FREE')->get();

        foreach($free_subscriptions as $subscription) {
            if ($subscription->active_until < now()) {

                $user = User::where('id', $subscription->user_id)->first();
                $plan = Plan::where('id', $subscription->plan_id)->first();

                $duration = ($plan->pricing_plan == 'monthly') ? 30 : 365;

                # Check if user still exists
                if ($user !== null) {
                    
                    # Check if plan still exits
                    if ($plan !== null) {

                        $subscription->update(['active_until' => Carbon::now()->addDays($duration)]);

                        $user->available_chars = $subscription->characters + $subscription->bonus;
                        $user->save();      
                    }

                }        
                 
            }            
        }
        
    }
}
