<?php

namespace App\Support;

class DisplayNumber
{
    public static function format(int $number): string
    {
        return str_pad((string) max(0, $number), 3, '0', STR_PAD_LEFT);
    }
}
