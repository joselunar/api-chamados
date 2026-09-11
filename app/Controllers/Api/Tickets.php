<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;

class Tickets extends BaseApiController
{
    public function index(): ResponseInterface
    {
        return $this->ok(service('ticketService')->search(service('currentUser')->require(), [
            'status'       => $this->request->getGet('status'),
            'priority'     => $this->request->getGet('priority'),
            'assignee_id'  => $this->request->getGet('assignee_id'),
            'requester_id' => $this->request->getGet('requester_id'),
            'q'            => $this->request->getGet('q'),
            'per_page'     => $this->request->getGet('per_page'),
        ]));
    }

    public function show(int $id): ResponseInterface
    {
        return $this->handle(
            fn () => $this->ok(service('ticketService')->show(service('currentUser')->require(), $id)),
        );
    }

    public function create(): ResponseInterface
    {
        return $this->handle(function () {
            $ticket = service('ticketService')->create(
                service('currentUser')->require(),
                $this->request->getJSON(true) ?? [],
            );

            return $this->ok($ticket, 201);
        });
    }

    public function update(int $id): ResponseInterface
    {
        return $this->handle(
            fn () => $this->ok(service('ticketService')->update(
                service('currentUser')->require(),
                $id,
                $this->request->getJSON(true) ?? [],
            )),
        );
    }

    public function comment(int $id): ResponseInterface
    {
        return $this->handle(function () use ($id) {
            $result = service('ticketService')->comment(
                service('currentUser')->require(),
                $id,
                (string) $this->request->getJsonVar('body'),
            );

            return $this->ok($result, 201);
        });
    }

    public function history(int $id): ResponseInterface
    {
        return $this->handle(
            fn () => $this->ok(service('ticketService')->history(service('currentUser')->require(), $id)),
        );
    }
}
