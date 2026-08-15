<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// 用户私有通知频道：仅本人可订阅
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// 商家通知频道：拥有该商家权限的用户可订阅
Broadcast::channel('merchant-notifications.{merchantId}', function ($user, $merchantId) {
    // 超级管理员和管理员可订阅所有商家频道
    if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
        return true;
    }

    // 运营专员可订阅关联商家
    if ($user->hasRole('operator') || $user->hasRole('operator_manager')) {
        return true;
    }

    // 财务角色可订阅
    if ($user->hasRole('finance') || $user->hasRole('cashier') || $user->hasRole('finance_manager')) {
        return true;
    }

    return false;
});
