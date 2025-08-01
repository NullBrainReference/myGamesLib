<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Game;
use App\Models\Blog;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $bob = User::where('email', 'bob@example.com')->first();
        $alice = User::where('email', 'alice@example.com')->first();

        $game = Game::where('title', 'Elden Ring')->first();
        $blog = Blog::where('title', 'My first game review')->first();

        Comment::create([
            'user_id' => $bob->id,
            'content' => 'Totally agree, Elden Ring is wild!',
            'commentable_type' => Game::class,
            'commentable_id' => $game->game_id,
        ]);

        Comment::create([
            'user_id' => $alice->id,
            'content' => 'Thanks for sharing your review!',
            'commentable_type' => Blog::class,
            'commentable_id' => $blog->id,
        ]);

        Comment::create([
            'user_id' => $bob->id,
            'content' => 'I’d add Skyrim to that RPG list!',
            'commentable_type' => Blog::class,
            'commentable_id' => $blog->id,
        ]);

        Comment::create([
            'user_id' => $alice->id,
            'content' => 'Glad someone remembers Dark Souls 🖤',
            'commentable_type' => Game::class,
            'commentable_id' => $game->game_id,
        ]);

        Comment::create([
            'user_id' => $bob->id,
            'content' => 'This blog is awesome!',
            'commentable_type' => Blog::class,
            'commentable_id' => $blog->id,
        ]);
    }
}
