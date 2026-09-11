<?php

namespace App\Controllers;

use App\Domain\DomainException;

class Tickets extends BaseController
{
    public function index()
    {
        $result = service('ticketService')->search(service('currentUser')->require(), [
            'status'   => $this->request->getGet('status'),
            'priority' => $this->request->getGet('priority'),
            'q'        => $this->request->getGet('q'),
        ]);

        return view('tickets/index', [
            'title'   => 'Chamados',
            'tickets' => $result['data'],
            'pager'   => $result['pager'],
            'filters' => $this->request->getGet(),
        ]);
    }

    public function show(int $id)
    {
        try {
            $ticket = service('ticketService')->show(service('currentUser')->require(), $id);
        } catch (DomainException $exception) {
            return redirect()->to('/')->with('error', $exception->getMessage());
        }

        return view('tickets/show', [
            'title'  => $ticket['protocol'],
            'ticket' => $ticket,
        ]);
    }

    public function create()
    {
        try {
            $ticket = service('ticketService')->create(service('currentUser')->require(), $this->request->getPost());
        } catch (DomainException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->to('chamados/' . $ticket['id'])->with('success', 'Chamado aberto.');
    }

    public function update(int $id)
    {
        try {
            service('ticketService')->update(service('currentUser')->require(), $id, $this->request->getPost());
        } catch (DomainException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->to('chamados/' . $id)->with('success', 'Chamado atualizado.');
    }

    public function comment(int $id)
    {
        try {
            service('ticketService')->comment(
                service('currentUser')->require(),
                $id,
                (string) $this->request->getPost('body'),
            );
        } catch (DomainException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->to('chamados/' . $id)->with('success', 'Comentário adicionado.');
    }
}
