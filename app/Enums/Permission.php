<?php

namespace App\Enums;

enum Permission: string
{
    case Viewer = 'viewer';
    case Manager = 'manager';
    case Admin = 'admin';

    public function includes(self $permission): bool
    {
        $order = self::cases();

        return array_search($this, $order) >= array_search($permission, $order);
    }
}
