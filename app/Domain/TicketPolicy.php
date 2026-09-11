<?php

namespace App\Domain;

class TicketPolicy
{
    /**
     * @param array<string, mixed> $user
     * @param array<string, mixed>|null $ticket
     */
    public function canView(array $user, ?array $ticket): bool
    {
        if ($ticket === null) {
            return false;
        }

        if (Role::isStaff($user['role'])) {
            return true;
        }

        return (int) $ticket['requester_id'] === (int) $user['id'];
    }

    /**
     * @param array<string, mixed> $user
     */
    public function canListAll(array $user): bool
    {
        return Role::isStaff($user['role']);
    }

    /**
     * @param array<string, mixed> $user
     * @param array<string, mixed> $ticket
     */
    public function canComment(array $user, array $ticket): bool
    {
        if (TicketStatus::isClosed($ticket['status'])) {
            return Role::isAdmin($user['role']);
        }

        return $this->canView($user, $ticket);
    }

    /**
     * @param array<string, mixed> $user
     */
    public function canChangeStatus(array $user): bool
    {
        return Role::isStaff($user['role']);
    }

    /**
     * @param array<string, mixed> $user
     */
    public function canAssign(array $user): bool
    {
        return Role::isStaff($user['role']);
    }

    /**
     * @param array<string, mixed> $user
     */
    public function canAssignAnyone(array $user): bool
    {
        return Role::isAdmin($user['role']);
    }

    /**
     * @param array<string, mixed> $user
     */
    public function canChangePriority(array $user): bool
    {
        return Role::isStaff($user['role']);
    }
}
