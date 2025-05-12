<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Game::insert([
            [
                'game_id' => 1,
                'title' => 'The Legend of Zelda: Breath of the Wild',
                'description' => 'An open-world action-adventure game where you explore the vast lands of Hyrule.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/8/8d/Dark_Souls_Cover_Art.jpg'
            ],
            [
                'game_id' => 2,
                'title' => 'Red Dead Redemption 2',
                'description' => 'A western-themed open-world game with a deep narrative and immersive gameplay.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/8/8d/Dark_Souls_Cover_Art.jpg'
            ],
            [
                'game_id' => 3,
                'title' => 'Minecraft',
                'description' => 'A sandbox game where players build, explore, and survive in a blocky world.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/8/8d/Dark_Souls_Cover_Art.jpg'
            ],
            [
                'game_id' => 4,
                'title' => 'Elden Ring',
                'description' => 'An action RPG set in a vast open world with deep lore and challenging combat.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/8/8d/Dark_Souls_Cover_Art.jpg'
            ],
        ]);
    }
}
