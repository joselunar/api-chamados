<?php

use App\Domain\Role;
use App\Domain\TicketPolicy;
use App\Domain\TicketStatus;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class TicketRulesTest extends CIUnitTestCase
{
    public function testUserSeesOnlyOwnTicket(): void
    {
        $policy = new TicketPolicy();
        $user   = ['id' => 1, 'role' => Role::USER];
        $own    = ['requester_id' => 1, 'status' => TicketStatus::OPEN];
        $other  = ['requester_id' => 2, 'status' => TicketStatus::OPEN];

        $this->assertTrue($policy->canView($user, $own));
        $this->assertFalse($policy->canView($user, $other));
        $this->assertFalse($policy->canListAll($user));
        $this->assertFalse($policy->canChangeStatus($user));
    }

    public function testAgentCanManageTicketsButNotAssignAnyone(): void
    {
        $policy = new TicketPolicy();
        $agent  = ['id' => 3, 'role' => Role::AGENT];

        $this->assertTrue($policy->canListAll($agent));
        $this->assertTrue($policy->canAssign($agent));
        $this->assertFalse($policy->canAssignAnyone($agent));
    }

    public function testResolvedCanCloseOrReopen(): void
    {
        $this->assertTrue(TicketStatus::canTransition(TicketStatus::RESOLVED, TicketStatus::CLOSED));
        $this->assertTrue(TicketStatus::canTransition(TicketStatus::RESOLVED, TicketStatus::IN_PROGRESS));
        $this->assertFalse(TicketStatus::canTransition(TicketStatus::CLOSED, TicketStatus::RESOLVED));
        $this->assertFalse(TicketStatus::canTransition(TicketStatus::OPEN, TicketStatus::RESOLVED));
    }
}
