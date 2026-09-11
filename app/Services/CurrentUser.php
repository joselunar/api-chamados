<?php

namespace App\Services;

class CurrentUser
{
    /**
     * @var array<string, mixed>|null
     */
    private ?array $user = null;

    /**
     * @param array<string, mixed> $user
     */
    public function set(array $user): void
    {
        $this->user = $user;
    }

    /**
     * @return array<string, mixed>
     */
    public function require(): array
    {
        if ($this->user === null) {
            throw new \RuntimeException('Usuário não autenticado.');
        }

        return $this->user;
    }

    public function id(): int
    {
        return (int) $this->require()['id'];
    }

    public function role(): string
    {
        return (string) $this->require()['role'];
    }
}
