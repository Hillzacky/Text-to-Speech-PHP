<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'gateway',
        'characters',
        'bonus',
        'subscription_id',
        'active_until',
    ];


    protected $dates = [
        'active_until',
    ];


    /**
     * Subscription belongs to a single user
     *
     * 
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Plan belongs to a single user
     *
     * 
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }


    /**
     * Check if subscription is active
     *
     * 
     */
    public function isActive()
    {
        return $this->active_until->gt(now());
    }
}
