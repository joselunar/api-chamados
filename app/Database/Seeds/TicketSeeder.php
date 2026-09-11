<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $now       = date('Y-m-d H:i:s');
        $usuario   = $this->db->table('users')->where('email', 'usuario@chamados.local')->get()->getRowArray();
        $atendente = $this->db->table('users')->where('email', 'atendente@chamados.local')->get()->getRowArray();

        $this->db->table('tickets')->insert([
            'protocol'     => 'CH-' . date('Ymd') . '-0001',
            'title'        => 'Não consigo acessar o painel',
            'description'  => 'Após a troca de senha, o login retorna erro 500.',
            'priority'     => 'alta',
            'status'       => 'em_andamento',
            'requester_id' => $usuario['id'],
            'assignee_id'  => $atendente['id'],
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $ticketId = $this->db->insertID();

        $this->db->table('ticket_events')->insertBatch([
            [
                'ticket_id'  => $ticketId,
                'user_id'    => $usuario['id'],
                'field'      => 'status',
                'old_value'  => null,
                'new_value'  => 'aberto',
                'created_at' => $now,
            ],
            [
                'ticket_id'  => $ticketId,
                'user_id'    => $atendente['id'],
                'field'      => 'assignee_id',
                'old_value'  => null,
                'new_value'  => (string) $atendente['id'],
                'created_at' => $now,
            ],
            [
                'ticket_id'  => $ticketId,
                'user_id'    => $atendente['id'],
                'field'      => 'status',
                'old_value'  => 'aberto',
                'new_value'  => 'em_andamento',
                'created_at' => $now,
            ],
        ]);

        $this->db->table('ticket_comments')->insert([
            'ticket_id'  => $ticketId,
            'user_id'    => $atendente['id'],
            'body'       => 'Estou verificando o log do servidor de autenticação.',
            'created_at' => $now,
        ]);
    }
}
