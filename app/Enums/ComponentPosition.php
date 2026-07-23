<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * Where a Component sits in a Design's crossover topology.
 * Stored on the component_design pivot.
 */
enum ComponentPosition: string
{
    use HasValues;

    case LF = 'LF';
    case LMF = 'LMF';
    case MF = 'MF';
    case HMF = 'HMF';
    case HF = 'HF';
    case Other = 'Other';

    public function label(): string
    {
        return match ($this) {
            self::LF => 'Low Frequency (LF)',
            self::LMF => 'Low-Mid Frequency (LMF)',
            self::MF => 'Mid Frequency (MF)',
            self::HMF => 'High-Mid Frequency (HMF)',
            self::HF => 'High Frequency (HF)',
            self::Other => 'Other',
        };
    }
}
