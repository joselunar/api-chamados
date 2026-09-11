<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Domain\DomainException;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseApiController extends BaseController
{
    protected function ok(mixed $data, int $status = 200): ResponseInterface
    {
        return $this->response->setStatusCode($status)->setJSON([
            'status' => $status,
            'data'   => $data,
        ]);
    }

    protected function handle(\Closure $callback): ResponseInterface
    {
        try {
            return $callback();
        } catch (DomainException $exception) {
            $code = $exception->getCode() >= 400 ? $exception->getCode() : 422;

            return $this->response->setStatusCode($code)->setJSON([
                'status'  => $code,
                'error'   => 'domain_error',
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
