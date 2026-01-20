<?php

namespace App\Helper;

use Illuminate\Support\Str;

class Helper
{
    public static function generateOrderCode()
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }

    public static function generateOrderToken()
    {
        return Str::random(32);
    }
}
