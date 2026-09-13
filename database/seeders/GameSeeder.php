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
            [
                'game_id' => 9,
                'title' => 'The Witcher 3: Wild Hunt',
                'description' => 'An open-world RPG following Geralt of Rivia on a quest to find his adopted daughter in a war-torn world.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/0/0c/Witcher_3_cover_art.jpg'
            ],
            [
                'game_id' => 10,
                'title' => 'Cyberpunk 2077',
                'description' => 'An open-world action-adventure story set in Night City, a megalopolis obsessed with power and modification.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Cyberpunk_2077_box_art.jpg'
            ],
            [
                'game_id' => 11,
                'title' => 'Hollow Knight',
                'description' => 'A challenging 2D action-adventure Metroidvania set in the ruined underground kingdom of Hallownest.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/0/04/Hollow_Knight_first_cover_art.webp'
            ],
            [
                'game_id' => 12,
                'title' => 'God of War (2018)',
                'description' => 'Kratos and his son Atreus embark on a mythic journey through the Norse realms to scatter his wife’s ashes.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/a/a7/God_of_War_4_cover.jpg'
            ],
            [
                'game_id' => 13,
                'title' => 'Grand Theft Auto V',
                'description' => 'An action-adventure game following three criminals as they commit heists in the fictional state of San Andreas.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/a/a5/Grand_Theft_Auto_V_cover_art.jpg'
            ],
            [
                'game_id' => 14,
                'title' => 'Stardew Valley',
                'description' => 'An open-ended country-life RPG where you inherit your grandfather’s old farm plot and build a new life.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/f/fd/Stardew_Valley_cover_art.png'
            ],
            [
                'game_id' => 15,
                'title' => 'Portal 2',
                'description' => 'A first-person puzzle-platform game featuring spatial puzzles solved using spatial portals.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/f/f9/Portal2cover.jpg'
            ],
            [
                'game_id' => 16,
                'title' => 'Hades',
                'description' => 'A rogue-like dungeon crawler where you defy the god of the dead as you battle out of the Underworld.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/Hades_cover_art.jpg'
            ],
            [
                'game_id' => 17,
                'title' => 'Sekiro: Shadows Die Twice',
                'description' => 'An action-adventure game focusing on stealth, exploration, and intense sword combat in Sengoku Japan.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/6/6e/Sekiro_art.jpg'
            ],
            [
                'game_id' => 18,
                'title' => 'Terraria',
                'description' => 'A 2D sandbox adventure game focused on exploration, crafting, building, and fighting monsters.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/7/76/Terraria_cover_art.jpg'
            ],
            [
                'game_id' => 19,
                'title' => 'Baldur\'s Gate 3',
                'description' => 'A party-based RPG set in the Dungeons & Dragons universe with deep narrative choices and tactical combat.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/1/12/Baldur%27s_Gate_3_cover_art.jpg'
            ],
            [
                'game_id' => 20,
                'title' => 'Super Mario Odyssey',
                'description' => 'A 3D platformer where Mario travels across various worlds collecting Power Moons to rescue Princess Peach.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/8/8d/Super_Mario_Odyssey.jpg'
            ],
            [
                'game_id' => 21,
                'title' => 'Persona 5 Royal',
                'description' => 'A turn-based RPG following high school students who lead secret lives as supernatural vigilantes.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/b/b0/Persona_5_cover_art.jpg'
            ],
            [
                'game_id' => 22,
                'title' => 'DOOM Eternal',
                'description' => 'A fast-paced first-person shooter where you slay hordes of demons across dimensions as the Doom Slayer.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/9/9d/Cover_Art_of_Doom_Eternal.png'
            ],
            [
                'game_id' => 23,
                'title' => 'The Elder Scrolls V: Skyrim',
                'description' => 'An open-world fantasy RPG where you play as the Dragonborn, prophesied to defeat the dragon Alduin.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/1/15/The_Elder_Scrolls_V_Skyrim_cover.png'
            ],
            [
                'game_id' => 24,
                'title' => 'Celeste',
                'description' => 'A precision platformer about climbing a mountain while facing internal struggles and overcoming challenges.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/commons/6/62/Celeste_box_art.png'
            ],
            [
                'game_id' => 25,
                'title' => 'Monster Hunter: World',
                'description' => 'An action RPG focused on tracking, hunting, and crafting equipment from formidable giant monsters.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/1/1b/Monster_Hunter_World_cover_art.jpg'
            ],
            [
                'game_id' => 26,
                'title' => 'Resident Evil 4 (Remake)',
                'description' => 'A survival horror game following agent Leon S. Kennedy on a mission to rescue the President\'s daughter.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/d/df/Resident_Evil_4_remake_cover_art.jpg'
            ],
            [
                'game_id' => 27,
                'title' => 'Death Stranding',
                'description' => 'An open-world action game set in an apocalyptic world focused on reconnecting isolated cities.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/2/22/Death_Stranding.jpg'
            ],
            [
                'game_id' => 28,
                'title' => 'Mass Effect Legendary Edition',
                'description' => 'A remastered sci-fi RPG trilogy following Commander Shepard in an epic space opera to save the galaxy.',
                'img_src' => 'https://upload.wikimedia.org/wikipedia/en/7/7b/Mass_Effect_Legendary_Edition_cover_art.jpg'
            ],
        ]);
    }
}
