<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $table         = 'tickets';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'protocol',
        'title',
        'description',
        'priority',
        'status',
        'requester_id',
        'assignee_id',
    ];

    public function nextProtocol(): string
    {
        $prefix = 'CH-' . date('Ymd') . '-';
        $last   = $this->like('protocol', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $seq = 1;
        if ($last !== null) {
            $seq = ((int) substr((string) $last['protocol'], -4)) + 1;
        }

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
