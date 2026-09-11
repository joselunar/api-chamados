<?php

namespace App\Domain;

class TicketPriority
{
    public const LOW    = 'baixa';
    public const MEDIUM = 'media';
    public const HIGH   = 'alta';
    public const URGENT = 'urgente';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [self::LOW, self::MEDIUM, self::HIGH, self::URGENT];
    }

    public static function isValid(string $priority): bool
    {
        return in_array($priority, self::all(), true);
    }
}
