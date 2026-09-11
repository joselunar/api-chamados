<?php

namespace App\Services;

use App\Domain\DomainException;
use App\Domain\Role;
use App\Domain\TicketPolicy;
use App\Domain\TicketPriority;
use App\Domain\TicketStatus;
use App\Models\CommentModel;
use App\Models\TicketEventModel;
use App\Models\TicketModel;
use App\Models\UserModel;

class TicketService
{
    public function __construct(
        private readonly TicketModel $tickets,
        private readonly CommentModel $comments,
        private readonly TicketEventModel $events,
        private readonly UserModel $users,
        private readonly TicketPolicy $policy = new TicketPolicy(),
    ) {
    }

    /**
     * @param array<string, mixed> $actor
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $actor, array $payload): array
    {
        $title       = trim((string) ($payload['title'] ?? ''));
        $description = trim((string) ($payload['description'] ?? ''));
        $priority    = strtolower(trim((string) ($payload['priority'] ?? TicketPriority::MEDIUM)));

        if ($title === '' || $description === '') {
            throw new DomainException('Informe título e descrição.', 422);
        }

        if (! TicketPriority::isValid($priority)) {
            throw new DomainException('Prioridade inválida.', 422);
        }

        $this->tickets->insert([
            'protocol'     => $this->tickets->nextProtocol(),
            'title'        => $title,
            'description'  => $description,
            'priority'     => $priority,
            'status'       => TicketStatus::OPEN,
            'requester_id' => (int) $actor['id'],
        ]);

        $ticket = $this->tickets->find($this->tickets->getInsertID());
        $this->record((int) $ticket['id'], (int) $actor['id'], 'status', null, TicketStatus::OPEN);

        return $this->present($ticket);
    }

    /**
     * @param array<string, mixed> $actor
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function search(array $actor, array $filters): array
    {
        $perPage = min(50, max(1, (int) ($filters['per_page'] ?? 10)));
        $query   = $this->tickets->orderBy('tickets.id', 'DESC');

        if (! $this->policy->canListAll($actor)) {
            $query->where('requester_id', (int) $actor['id']);
        } elseif (! empty($filters['requester_id'])) {
            $query->where('requester_id', (int) $filters['requester_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (! empty($filters['assignee_id'])) {
            $query->where('assignee_id', (int) $filters['assignee_id']);
        }

        $term = trim((string) ($filters['q'] ?? ''));
        if ($term !== '') {
            $query->groupStart()
                ->like('title', $term)
                ->orLike('protocol', $term)
                ->orLike('description', $term)
                ->groupEnd();
        }

        $rows = $query->paginate($perPage);

        return [
            'data' => array_map(fn (array $row): array => $this->present($row), $rows),
            'pager' => [
                'page'     => $this->tickets->pager->getCurrentPage(),
                'per_page' => $perPage,
                'total'    => $this->tickets->pager->getTotal(),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $actor
     * @return array<string, mixed>
     */
    public function show(array $actor, int $id): array
    {
        $ticket = $this->requireVisible($actor, $id);
        $ticket['comments'] = $this->comments->byTicket($id);
        $ticket['history']  = $this->events->byTicket($id);

        return $this->present($ticket);
    }

    /**
     * @param array<string, mixed> $actor
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function update(array $actor, int $id, array $payload): array
    {
        $ticket = $this->requireVisible($actor, $id);

        if (isset($payload['status'])) {
            $this->changeStatus($actor, $ticket, (string) $payload['status']);
            $ticket = $this->tickets->find($id);
        }

        if (isset($payload['priority'])) {
            $this->changePriority($actor, $ticket, (string) $payload['priority']);
            $ticket = $this->tickets->find($id);
        }

        if (array_key_exists('assignee_id', $payload)) {
            $this->changeAssignee($actor, $ticket, $payload['assignee_id'] !== null ? (int) $payload['assignee_id'] : null);
        }

        return $this->present($this->tickets->find($id));
    }

    /**
     * @param array<string, mixed> $actor
     * @return array<string, mixed>
     */
    public function comment(array $actor, int $id, string $body): array
    {
        $ticket = $this->requireVisible($actor, $id);
        $body   = trim($body);

        if ($body === '') {
            throw new DomainException('O comentário não pode ser vazio.', 422);
        }

        if (! $this->policy->canComment($actor, $ticket)) {
            throw new DomainException('Chamado fechado. Somente admin pode comentar.', 403);
        }

        $this->comments->insert([
            'ticket_id'  => $id,
            'user_id'    => (int) $actor['id'],
            'body'       => $body,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if (
            $ticket['status'] === TicketStatus::WAITING
            && (int) $ticket['requester_id'] === (int) $actor['id']
        ) {
            $this->tickets->update($id, ['status' => TicketStatus::IN_PROGRESS]);
            $this->record($id, (int) $actor['id'], 'status', TicketStatus::WAITING, TicketStatus::IN_PROGRESS);
        }

        return [
            'comment' => $this->comments->find($this->comments->getInsertID()),
            'ticket'  => $this->present($this->tickets->find($id)),
        ];
    }

    /**
     * @param array<string, mixed> $actor
     * @return list<array<string, mixed>>
     */
    public function history(array $actor, int $id): array
    {
        $this->requireVisible($actor, $id);

        return $this->events->byTicket($id);
    }

    /**
     * @param array<string, mixed> $actor
     * @param array<string, mixed> $ticket
     */
    private function changeStatus(array $actor, array $ticket, string $status): void
    {
        if (! $this->policy->canChangeStatus($actor)) {
            throw new DomainException('Somente atendente ou admin altera o status.', 403);
        }

        if ($status === TicketStatus::CLOSED && ! Role::isAdmin($actor['role']) && $ticket['status'] !== TicketStatus::RESOLVED) {
            throw new DomainException('Somente admin fecha chamado que ainda não foi resolvido.', 403);
        }

        if (! TicketStatus::canTransition($ticket['status'], $status)) {
            throw new DomainException('Transição de status inválida: ' . $ticket['status'] . ' → ' . $status, 409);
        }

        $this->tickets->update((int) $ticket['id'], ['status' => $status]);
        $this->record((int) $ticket['id'], (int) $actor['id'], 'status', $ticket['status'], $status);
    }

    /**
     * @param array<string, mixed> $actor
     * @param array<string, mixed> $ticket
     */
    private function changePriority(array $actor, array $ticket, string $priority): void
    {
        if (! $this->policy->canChangePriority($actor)) {
            throw new DomainException('Somente atendente ou admin altera a prioridade.', 403);
        }

        $priority = strtolower($priority);
        if (! TicketPriority::isValid($priority)) {
            throw new DomainException('Prioridade inválida.', 422);
        }

        if ($priority === $ticket['priority']) {
            return;
        }

        $this->tickets->update((int) $ticket['id'], ['priority' => $priority]);
        $this->record((int) $ticket['id'], (int) $actor['id'], 'priority', $ticket['priority'], $priority);
    }

    /**
     * @param array<string, mixed> $actor
     * @param array<string, mixed> $ticket
     */
    private function changeAssignee(array $actor, array $ticket, ?int $assigneeId): void
    {
        if (! $this->policy->canAssign($actor)) {
            throw new DomainException('Somente atendente ou admin atribui o chamado.', 403);
        }

        if ($assigneeId !== null) {
            $assignee = $this->users->find($assigneeId);
            if ($assignee === null || ! Role::isStaff((string) $assignee['role'])) {
                throw new DomainException('O responsável precisa ser atendente ou admin.', 422);
            }

            if (! $this->policy->canAssignAnyone($actor) && $assigneeId !== (int) $actor['id']) {
                throw new DomainException('Atendente só pode atribuir o chamado a si mesmo.', 403);
            }
        }

        $old = $ticket['assignee_id'] !== null ? (string) $ticket['assignee_id'] : null;
        $new = $assigneeId !== null ? (string) $assigneeId : null;

        if ($old === $new) {
            return;
        }

        $this->tickets->update((int) $ticket['id'], ['assignee_id' => $assigneeId]);
        $this->record((int) $ticket['id'], (int) $actor['id'], 'assignee_id', $old, $new);
    }

    /**
     * @param array<string, mixed> $actor
     * @return array<string, mixed>
     */
    private function requireVisible(array $actor, int $id): array
    {
        $ticket = $this->tickets->find($id);

        if ($ticket === null) {
            throw new DomainException('Chamado não encontrado.', 404);
        }

        if (! $this->policy->canView($actor, $ticket)) {
            throw new DomainException('Você não tem acesso a este chamado.', 403);
        }

        return $ticket;
    }

    private function record(int $ticketId, int $userId, string $field, ?string $old, ?string $new): void
    {
        $this->events->insert([
            'ticket_id'  => $ticketId,
            'user_id'    => $userId,
            'field'      => $field,
            'old_value'  => $old,
            'new_value'  => $new,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @param array<string, mixed> $ticket
     * @return array<string, mixed>
     */
    private function present(array $ticket): array
    {
        $requester = $this->users->find((int) $ticket['requester_id']);
        $assignee  = ! empty($ticket['assignee_id']) ? $this->users->find((int) $ticket['assignee_id']) : null;

        $ticket['requester'] = $requester ? ['id' => (int) $requester['id'], 'name' => $requester['name']] : null;
        $ticket['assignee']  = $assignee ? ['id' => (int) $assignee['id'], 'name' => $assignee['name']] : null;

        return $ticket;
    }
}
