<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

/**
 * How a Design's files are gated.
 *
 *  - Free:  fully open, no payment.
 *  - Tip:   open access, but accepts optional payment ("tip mode").
 *  - Gated: files unlock only after purchase.
 *
 * Payment processing is intentionally not implemented yet — this enum captures
 * the designer's intent so the gating UI and a future checkout can build on it.
 */
enum DesignAccess: string
{
    use HasValues;

    case Free = 'free';
    case Tip = 'tip';
    case Gated = 'gated';

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Free',
            self::Tip => 'Free (tips accepted)',
            self::Gated => 'Paid / Gated',
        };
    }

    /** Whether files are openly downloadable without a purchase. */
    public function isOpen(): bool
    {
        return $this !== self::Gated;
    }
}
