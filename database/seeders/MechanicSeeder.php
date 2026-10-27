<?php

namespace Database\Seeders;

use App\Models\Mechanic;
use Illuminate\Database\Seeder;

class MechanicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mechanic::insert([
            // --- Specific Game Mechanics ---
            [
                'title' => 'Stamina & Free Climbing',
                'content' => 'Allows players to climb almost any vertical surface, with movement speed and height limited by a depleting stamina meter.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 1, // Breath of the Wild
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => 'Dead Eye Targeting',
                'content' => 'Slows down time to allow players to manually mark multiple targets before automatically firing a rapid sequence of shots.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 2, // Red Dead Redemption 2
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => 'Voxel Grid Crafting',
                'content' => 'Players gather environmental resources and arrange items on a grid to craft tools, weapons, and building materials.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 3, // Minecraft
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => 'Spirit Ash Summons',
                'content' => 'Summons AI companions using Focus Points (FP) to assist during boss encounters and dense enemy zones.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 4, // Elden Ring
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => 'Bonfire Healing & World Reset',
                'content' => 'Resting restores health and replenishes items, but causes non-boss enemies in the world to respawn.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 5, // Dark Souls
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => 'Soul Focus & Healing',
                'content' => 'Landing melee attacks accumulates Soul, which can be spent to cast offensive spells or channel to restore HP.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 11, // Hollow Knight
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => 'Momentum-Preserving Portals',
                'content' => 'Linking entrance and exit portals on conductive surfaces allows players to convert fall velocity into horizontal distance.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 15, // Portal 2
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => 'Divine Boon Synergies',
                'content' => 'Randomized rogue-like upgrades awarded by gods during runs, modifying primary attacks with elemental effects and status ailments.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 16, // Hades
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => 'Environmental Surface Reactions',
                'content' => 'Elements interact dynamically with terrain and weather (e.g., igniting oil, electrocuting water, or melting ice).',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 19, // Baldur's Gate 3
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => '8-Directional Air Dash',
                'content' => 'A precise mid-air movement vector that resets upon landing on solid ground or touching refill crystals.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 24, // Celeste
                'project_id' => null,
                'parent_id' => null,
            ],

            // --- Generic & System Mechanics ---
            [
                'title' => 'Posture Deflection & Stagger',
                'content' => 'Timed blocks damage the enemy posture bar, exposing them to lethal deathblow attacks once maxed out.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 17, // Sekiro
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => 'Weight Encumbrance Limit',
                'content' => 'Exceeding inventory weight thresholds slows down movement, increases stamina costs, and disables fast travel.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 23, // Skyrim
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => 'Glory Kills & Resource Dropping',
                'content' => 'Executing staggered enemies with melee finishers forces them to drop health, armor, or ammunition pickups.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 22, // DOOM Eternal
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => 'Branching Skill Trees',
                'content' => 'Character progression system where skill points are spent to unlock active abilities and passive perk branches.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 10, // Cyberpunk 2077
                'project_id' => null,
                'parent_id' => null,
            ],
            [
                'title' => 'Fast Travel Network',
                'content' => 'Discovered points of interest unlock instant travel shortcuts across large world maps.',
                'approved' => true,
                'user_id' => 1,
                'comment_id' => null,
                'game_id' => 9, // The Witcher 3
                'project_id' => null,
                'parent_id' => null,
            ],
        ]);
    }
}
