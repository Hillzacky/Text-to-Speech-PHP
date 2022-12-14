<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'paypal_gateway_plan_id',
        'stripe_gateway_plan_id',
        'paystack_gateway_plan_id',
        'razorpay_gateway_plan_id',
        'plan_type',
        'status',
        'plan_name',
        'cost',
        'currency',
        'characters',
        'bonus',
        'pricing_plan',
        'featured',
        'free',
        'primary_heading', 
        'secondary_heading',
        'plan_features', 
        'duration_in_days',
    ];

    /**
     * Plan can have many subscribers
     * 
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
