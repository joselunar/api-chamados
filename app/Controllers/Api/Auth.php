<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseApiController
{
    public function login(): ResponseInterface
    {
        $email    = trim((string) $this->request->getJsonVar('email'));
        $password = (string) $this->request->getJsonVar('password');
        $users    = model(UserModel::class);
        $user     = $users->findByEmail($email);

        if ($user === null || ! password_verify($password, $user['password'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 401,
                'error'   => 'unauthorized',
                'message' => 'Credenciais inválidas.',
            ]);
        }

        $token = $user['api_token'] ?: $users->issueApiToken((int) $user['id']);

        return $this->ok([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'    => (int) $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'role'  => $user['role'],
            ],
        ]);
    }
}
