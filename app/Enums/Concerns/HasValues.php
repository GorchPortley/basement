<?php

namespace App\Enums\Concerns;

/**
 * Shared helpers for string-backed enums.
 *
 * Enums are stored as plain strings in the database (sqlite-friendly) and cast
 * back to the enum on the model. These helpers give us validation lists and
 * ready-made option arrays for maryUI's <x-select> / <x-choices> components.
 */
trait HasValues
{
    /**
     * All backing values, e.g. for `Rule::in(DesignCategory::values())`.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case): string => $case->value, self::cases());
    }

    /**
     * maryUI select options: [['id' => value, 'name' => label], ...].
     *
     * @return array<int, array{id: string, name: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $case): array => ['id' => $case->value, 'name' => $case->label()],
            self::cases(),
        );
    }

    /**
     * Human readable label. Override per-enum when the value isn't display-ready.
     */
    public function label(): string
    {
        return $this->value;
    }
}
