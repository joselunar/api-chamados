<?php

namespace App\Models;

use App\Domain\Role;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'email', 'password', 'role', 'api_token'];

    /**
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first() ?: null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByToken(string $token): ?array
    {
        return $this->where('api_token', $token)->first() ?: null;
    }

    public function issueApiToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update($userId, ['api_token' => $token]);

        return $token;
    }

    public function isStaffUser(?array $user): bool
    {
        return $user !== null && Role::isStaff((string) $user['role']);
    }
}
