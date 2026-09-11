<?php

namespace App\Domain;

class Role
{
    public const USER      = 'usuario';
    public const AGENT     = 'atendente';
    public const ADMIN     = 'admin';

    public static function isStaff(string $role): bool
    {
        return in_array($role, [self::AGENT, self::ADMIN], true);
    }

    public static function isAdmin(string $role): bool
    {
        return $role === self::ADMIN;
    }
}
