<?php

namespace App\Services;

class HelperService 
{
    public static function getTotalCharsFormatted()
    {   

        $value = self::formatTotalChars(auth()->user()->available_chars);

        return $value;
    }


    public static function formatTotalChars($total)
    {
        $units = ['', 'K', 'M', 'B', 'T'];
        for ($i = 0; $total >= 1000; $i++) {
            $total /= 1000;
        }
        return round($total, 1) . $units[$i];
    }


}