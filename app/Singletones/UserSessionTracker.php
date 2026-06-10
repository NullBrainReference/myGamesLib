<?php

namespace App\Singletones;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserSessionTracker
{
    private static ?UserSessionTracker $instance = null;

    private function __construct()
    {

    }

    private function __clone() {}

    public static function getInstance(): UserSessionTracker
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function track(int $userId): void
    {
        // Store an active timestamp key in the cache provider for this user
        Cache::put('user-active-' . $userId, true, now()->addMinutes(5));
    }

    public function getActiveCount(): int
    {
        return User::where('updated_at', '>=', now()->subMinutes(5))->count();
    }
}
