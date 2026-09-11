<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table         = 'ticket_comments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['ticket_id', 'user_id', 'body', 'created_at'];

    /**
     * @return list<array<string, mixed>>
     */
    public function byTicket(int $ticketId): array
    {
        return $this->select('ticket_comments.*, users.name as author_name, users.role as author_role')
            ->join('users', 'users.id = ticket_comments.user_id')
            ->where('ticket_id', $ticketId)
            ->orderBy('ticket_comments.id', 'ASC')
            ->findAll();
    }
}
