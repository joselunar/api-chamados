<?php

namespace Config;

use App\Services\CurrentUser;
use App\Services\TicketService;
use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function currentUser(bool $getShared = true): CurrentUser
    {
        if ($getShared) {
            return static::getSharedInstance('currentUser');
        }

        return new CurrentUser();
    }

    public static function ticketService(bool $getShared = true): TicketService
    {
        if ($getShared) {
            return static::getSharedInstance('ticketService');
        }

        return new TicketService(
            model(\App\Models\TicketModel::class),
            model(\App\Models\CommentModel::class),
            model(\App\Models\TicketEventModel::class),
            model(\App\Models\UserModel::class),
        );
    }
}
