<?php

namespace App\Singletones;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserSessionTracker
{
    // 1. Storage for the single structural instance reference
    private static ?UserSessionTracker $instance = null;

    // 2. Private constructor prevents direct initialization with 'new' outside the class
    private function __construct()
    {
        // This runs exactly once when the singleton is first initialized
    }

    // 3. Prevent cloning or duplicating the instance
    private function __clone() {}

    // 4. Global single point of access method
    public static function getInstance(): UserSessionTracker
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Mark a user as active right now (caches their status for 5 minutes).
     */
    public function track(int $userId): void
    {
        // Store an active timestamp key in the cache provider for this user
        Cache::put('user-active-' . $userId, true, now()->addMinutes(5));
    }

    /**
     * Count how many unique users have been active recently.
     */
    public function getActiveCount(): int
    {
        // In a real database, we look for recently active users.
        // For your exam, we query users who touched the system in the last 5 minutes.
        return User::where('updated_at', '>=', now()->subMinutes(5))->count();
    }
}
