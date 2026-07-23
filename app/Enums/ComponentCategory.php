<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * The type of driver/part a Component represents. Values are display-ready.
 */
enum ComponentCategory: string
{
    use HasValues;

    case Subwoofer = 'Subwoofer';
    case Woofer = 'Woofer';
    case Tweeter = 'Tweeter';
    case CompressionDriver = 'Compression Driver';
    case Exciter = 'Exciter';
    case Other = 'Other';
}
