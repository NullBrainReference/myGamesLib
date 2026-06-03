<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Thread;

class ThreadSeeder extends Seeder
{
    public function run()
    {
        // 1. Global Announcement (Pinned)
        $thread1 = Thread::create([
            'user_id' => 1,
            'title' => 'Welcome to the Official Game Forum!',
            'content' => 'Please read the community guidelines before posting. Be respectful, helpful, and have fun!',
            'is_locked' => false,
            'is_pinned' => true,
        ]);
        $thread1->comments()->createMany([
            ['user_id' => 1, 'content' => 'Glad to be here! Looking forward to discussing the latest patches.'],
            ['user_id' => 1, 'content' => 'Awesome addition to the site. Thanks admin.'],
        ]);

        // 2. Gameplay discussion
        $thread2 = Thread::create([
            'user_id' => 1,
            'title' => 'What is the best build for the final boss?',
            'content' => 'I am struggling on phase 2. Should I stack agility or go pure tank?',
            'is_locked' => false,
            'is_pinned' => false,
        ]);
        $thread2->comments()->createMany([
            ['user_id' => 1, 'content' => 'Definitely stack agility. If you time your dodges right, phase 2 becomes a cakewalk.'],
            ['user_id' => 1, 'content' => 'Tank builds work too, but the fight takes twice as long. Good luck!'],
        ]);

        // 3. Game suggestions
        $thread3 = Thread::create([
            'user_id' => 1,
            'title' => 'Suggestions for the upcoming multiplayer update',
            'content' => 'We really need a dedicated match history log and custom lobbies for tournaments.',
            'is_locked' => false,
            'is_pinned' => false,
        ]);
        $thread3->comments()->createMany([
            ['user_id' => 1, 'content' => 'Custom lobbies would be huge for the community scene.'],
        ]);

        // 4. Important Patch thread (Pinned)
        $thread4 = Thread::create([
            'user_id' => 1,
            'title' => 'Patch Notes v1.04 - Balance Adjustments & Bug Fixes',
            'content' => 'We have optimized loading screens and resolved the audio crash occurring in level 3.',
            'is_locked' => false,
            'is_pinned' => true,
        ]);
        $thread4->comments()->createMany([
            ['user_id' => 1, 'content' => 'Finally, that level 3 crash was driving me crazy. Thanks for the quick fix!'],
        ]);

        // 5. Retro gaming discussion
        $thread5 = Thread::create([
            'user_id' => 1,
            'title' => 'What classic roguelike mechanic should return?',
            'content' => 'Personally, I miss strict grid-based turn resolution. Real-time hybrid systems feel too chaotic sometimes.',
            'is_locked' => false,
            'is_pinned' => false,
        ]);
        $thread5->comments()->createMany([
            ['user_id' => 1, 'content' => 'Agreed. Pure turn-based gives you actual time to think strategically.'],
        ]);

        // 6. Archived/Closed Thread (Locked)
        $thread6 = Thread::create([
            'user_id' => 1,
            'title' => '[Archived] Open Beta Bug Reporting Thread',
            'content' => 'The open beta has officially concluded. Thank you all for your telemetry submissions!',
            'is_locked' => true,
            'is_pinned' => false,
        ]);
        // Even locked threads can have existing comments seeded
        $thread6->comments()->createMany([
            ['user_id' => 1, 'content' => 'Found a UI clipping bug when resolution is set to ultrawide.'],
            ['user_id' => 1, 'content' => 'Beta was super smooth, can\'t wait for full release.'],
        ]);
    }
}
