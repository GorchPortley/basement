<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * The type of speaker a Design represents. Values are display-ready.
 */
enum DesignCategory: string
{
    use HasValues;

    case Subwoofer = 'Subwoofer';
    case FullRange = 'Full-Range';
    case TwoWay = 'Two-Way';
    case ThreeWay = 'Three-Way';
    case FourWayPlus = 'Four-Way+';
    case Portable = 'Portable';
    case Esoteric = 'Esoteric';
    case System = 'System';
}
