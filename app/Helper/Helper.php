<?php

namespace App\Helper;

use Illuminate\Support\Str;

class Helper
{
    public static function generateOrderCode()
    {
        return 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
    }

    public static function generateOrderToken()
    {
        return Str::random(32);
    }
}
