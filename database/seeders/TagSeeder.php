<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use App\Models\Game;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create the Tags and save them to variables
        $openWorld = Tag::create([
            'title' => 'Open World',
            'description' => 'Games featuring vast, explorable landscapes.',
            'is_r18' => false,
        ]);

        $rpg = Tag::create([
            'title' => 'RPG',
            'description' => 'Role-playing games featuring character progression and deep lore.',
            'is_r18' => false,
        ]);

        $sandbox = Tag::create([
            'title' => 'Sandbox',
            'description' => 'Games focused on creativity, building, and player freedom.',
            'is_r18' => false,
        ]);

        $darkFantasy = Tag::create([
            'title' => 'Dark Fantasy',
            'description' => 'Grim, brutal, and mature fantasy settings.',
            'is_r18' => false,
        ]);

        // 2. Attach tags to your seeded games using their IDs (1 through 5)

        // Zelda (ID: 1) -> Open World
        if ($game = Game::find(1)) {
            $game->tags()->attach([$openWorld->tag_id]);
        }

        // Red Dead Redemption 2 (ID: 2) -> Open World, Dark Fantasy (for mature content)
        if ($game = Game::find(2)) {
            $game->tags()->attach([$openWorld->tag_id, $darkFantasy->tag_id]);
        }

        // Minecraft (ID: 3) -> Open World, Sandbox
        if ($game = Game::find(3)) {
            $game->tags()->attach([$openWorld->tag_id, $sandbox->tag_id]);
        }

        // Elden Ring (ID: 4) -> Open World, RPG, Dark Fantasy
        if ($game = Game::find(4)) {
            $game->tags()->attach([$openWorld->tag_id, $rpg->tag_id, $darkFantasy->tag_id]);
        }

        // Dark Souls (ID: 5) -> RPG, Dark Fantasy
        if ($game = Game::find(5)) {
            $game->tags()->attach([$rpg->tag_id, $darkFantasy->tag_id]);
        }
    }
}
