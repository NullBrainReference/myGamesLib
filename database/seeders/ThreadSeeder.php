<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Thread;

class ThreadSeeder extends Seeder
{
    public function run()
    {
        // Thread::factory()->count(10)->create();

        // Or manually create sample data
        Thread::create([
            'user_id' => 1,
            'title' => 'Welcome to the forum',
            'content' => 'This is the first thread!',
            'is_locked' => false,
            'is_pinned' => true,
        ]);

        Thread::create([
            'user_id' => 1,
            'title' => 'Test 1',
            'content' => 'This is the first thread!',
            'is_locked' => false,
            'is_pinned' => false,
        ]);

        Thread::create([
            'user_id' => 1,
            'title' => 'Test 2',
            'content' => 'This is the first thread!',
            'is_locked' => false,
            'is_pinned' => false,
        ]);

        Thread::create([
            'user_id' => 1,
            'title' => 'Test 3',
            'content' => 'This is the first thread!',
            'is_locked' => false,
            'is_pinned' => true,
        ]);
        Thread::create([
            'user_id' => 1,
            'title' => 'Welcome to the forum',
            'content' => 'This is the first thread!',
            'is_locked' => false,
            'is_pinned' => true,
        ]);
        Thread::create([
            'user_id' => 1,
            'title' => 'Welcome to the forum',
            'content' => 'This is the first thread!',
            'is_locked' => false,
            'is_pinned' => true,
        ]);
    }
}
