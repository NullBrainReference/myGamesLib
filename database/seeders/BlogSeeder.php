<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Blog;
use App\Models\User;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'alice@example.com')->first();

        Blog::create([
            'user_id' => $user->id,
            'title' => 'My first game review',
            'content' => 'I loved playing Elden Ring. The world is huge and mysterious!',
        ]);

        Blog::create([
            'user_id' => $user->id,
            'title' => 'Top 5 RPGs of all time',
            'content' => 'In my opinion, Dark Souls and The Witcher 3 are untouchable classics.',
        ]);
    }
}
