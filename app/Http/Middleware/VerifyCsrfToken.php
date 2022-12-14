<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $addHttpCookie = true;
    
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'webhooks/*',
        'user/payments/approved/razorpay',        
        'user/payments/subscription/razorpay',  
        'user/payments/approved/braintree', 
        'public/install/*',    
        'install/*',    
    ];
}
