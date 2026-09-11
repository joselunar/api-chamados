<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');
        $token  = preg_match('/Bearer\s+(\S+)/i', $header, $matches) === 1 ? $matches[1] : '';
        $user   = $token !== '' ? model(UserModel::class)->findByToken($token) : null;

        if ($user === null) {
            return service('response')->setStatusCode(401)->setJSON([
                'status'  => 401,
                'error'   => 'unauthorized',
                'message' => 'Token Bearer inválido.',
            ]);
        }

        service('currentUser')->set($user);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
