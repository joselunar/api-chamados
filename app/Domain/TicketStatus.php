<?php

namespace App\Domain;

class TicketStatus
{
    public const OPEN        = 'aberto';
    public const IN_PROGRESS = 'em_andamento';
    public const WAITING     = 'aguardando_usuario';
    public const RESOLVED    = 'resolvido';
    public const CLOSED      = 'fechado';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [self::OPEN, self::IN_PROGRESS, self::WAITING, self::RESOLVED, self::CLOSED];
    }

    /**
     * @return list<string>
     */
    public static function allowedTransitions(string $from): array
    {
        return match ($from) {
            self::OPEN        => [self::IN_PROGRESS, self::CLOSED],
            self::IN_PROGRESS => [self::WAITING, self::RESOLVED, self::CLOSED],
            self::WAITING     => [self::IN_PROGRESS, self::RESOLVED],
            self::RESOLVED    => [self::CLOSED, self::IN_PROGRESS],
            self::CLOSED      => [self::IN_PROGRESS],
            default           => [],
        };
    }

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::allowedTransitions($from), true);
    }

    public static function isClosed(string $status): bool
    {
        return $status === self::CLOSED;
    }
}
