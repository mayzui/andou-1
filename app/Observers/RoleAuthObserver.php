<?php
namespace App\Observers;

use Cache;

class RoleAuthObserver
{
    public function saving()
    {
        return Cache::tags('rbac')->flush();
    }

    public function deleting()
    {
        return Cache::tags('rbac')->flush();
    }
}