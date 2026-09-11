<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketEventModel extends Model
{
    protected $table         = 'ticket_events';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['ticket_id', 'user_id', 'field', 'old_value', 'new_value', 'created_at'];

    /**
     * @return list<array<string, mixed>>
     */
    public function byTicket(int $ticketId): array
    {
        return $this->select('ticket_events.*, users.name as author_name')
            ->join('users', 'users.id = ticket_events.user_id')
            ->where('ticket_id', $ticketId)
            ->orderBy('ticket_events.id', 'ASC')
            ->findAll();
    }
}
