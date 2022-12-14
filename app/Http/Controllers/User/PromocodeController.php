<?php

namespace App\Http\Controllers\User;

use Gabievi\Promocodes\Facades\Promocodes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrepaidPlan;
use App\Models\Promocode;
use App\Models\Plan;

class PromocodeController extends Controller
{   
    
    /**
     * Apply promocode for prepaid plans
     */
    public function applyPromocodesPrepaid(Request $request, PrepaidPlan $id)
    {   
        if ($request->ajax()) {

            if (request('promo_code')) {

                $tax_value = (config('payment.payment_tax') > 0) ? $tax = $id->cost * config('payment.payment_tax') / 100 : 0;
                $total_value = $tax_value + $id->cost;
                $code = request('promo_code');

                if (Promocodes::check($code)) {

                    $promocode = Promocodes::check($code);

                    if ($promocode->data['status'] == 'invalid') {
                        return [ 'error' => 'This promocode is not valid anymore'];
                    }

                    if ($promocode->data['type'] == 'percentage') {

                        $discount_value = ($promocode->reward * $total_value) / 100;
                        $new_value = $total_value - $discount_value;
                        $discount = '-' . $promocode->reward . '%';

                        return ['total' => number_format((float)$new_value, 2, '.', ''), 'discount' => $discount];

                    } else {

                        $new_value = $total_value - $promocode->reward;
                        $discount = '$' . $promocode->reward . ' ' . $id->currency;

                        return ['total' => number_format((float)$new_value, 2, '.', ''), 'discount' => $discount];
                    }
                    
                } 
                
                return [ 'error' => 'Invalid promocode'];
                
            } 

            return [ 'error' => 'Enter a valid promocode'];
        }
    }


    /**
     * Calculate promocode discount value
     */
    public function calculatePromocode($code, $total_value)
    {
        if (Promocodes::check($code)) {

            $promocode = Promocodes::check($code);

            if ($promocode->data['status'] == 'invalid') {
                return false;
            }

            if ($promocode->data['type'] == 'percentage') {
                
                $discount_value = ($promocode->reward * $total_value) / 100;
                $new_value = $total_value - $discount_value;

                Promocodes::redeem($code);

                return number_format((float)$new_value, 2, '.', '');

            } else {

                $new_value = $total_value - $promocode->reward;
                
                Promocodes::redeem($code);

                return number_format((float)$new_value, 2, '.', '');
            }
        }

        return false;
    }
}
