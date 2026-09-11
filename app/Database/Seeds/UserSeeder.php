<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $this->db->table('users')->insertBatch([
            [
                'name'       => 'Carla Usuária',
                'email'      => 'usuario@chamados.local',
                'password'   => password_hash('Usuario@123', PASSWORD_DEFAULT),
                'role'       => 'usuario',
                'api_token'  => bin2hex(random_bytes(32)),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Bruno Atendente',
                'email'      => 'atendente@chamados.local',
                'password'   => password_hash('Atendente@123', PASSWORD_DEFAULT),
                'role'       => 'atendente',
                'api_token'  => bin2hex(random_bytes(32)),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Ana Admin',
                'email'      => 'admin@chamados.local',
                'password'   => password_hash('Admin@123', PASSWORD_DEFAULT),
                'role'       => 'admin',
                'api_token'  => bin2hex(random_bytes(32)),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
