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
                'img_src' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTTbvEycJxlpEdRQOxUQaHdkZdW63veWvFdA&s'
            ],
            [
                'game_id' => 2,
                'title' => 'Red Dead Redemption 2',
                'description' => 'A western-themed open-world game with a deep narrative and immersive gameplay.',
                'img_src' => 'https://image.api.playstation.com/gs2-sec/appkgo/prod/CUSA08519_00/12/i_3da1cf7c41dc7652f9b639e1680d96436773658668c7dc3930c441291095713b/i/icon0.png'
            ],
            [
                'game_id' => 3,
                'title' => 'Minecraft',
                'description' => 'A sandbox game where players build, explore, and survive in a blocky world.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/ru/f/f4/Minecraft_Cover_Art.png'
            ],
            [
                'game_id' => 4,
                'title' => 'Elden Ring',
                'description' => 'An action RPG set in a vast open world with deep lore and challenging combat.',
                'img_src' => 'https://gamestorecolombia.com/files/images/productos/1639688027-elden-ring-ps5-pre-orden.jpg'
            ],
            [
                'game_id' => 5,
                'title' => 'Dark Souls',
                'description' => 'The Legend',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/8/8d/Dark_Souls_Cover_Art.jpg'
            ],
        ]);
    }
}
