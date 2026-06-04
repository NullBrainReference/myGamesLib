<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;

class ProjectTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create specific test accounts if they don't exist yet
        $adminOwner = User::firstOrCreate(
            ['email' => 'lead@example.com'],
            [
                'name' => 'Alex Leadman',
                'password' => Hash::make('password123'),
            ]
        );

        $testEditor = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Jordan Editor',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Generate 15 randomized users for lookup roster variation
        $poolUsers = User::factory()->count(15)->create();

        // Gather all users into a unified collection
        $allUsers = User::all();
        $projects = Project::all();

        if ($projects->isEmpty()) {
            $this->command->warn('No projects found! Please run your ProjectSeeder first.');
            return;
        }

        // 3. Loop through projects and attach team nodes randomly
        foreach ($projects as $project) {

            // Assign at least one Owner if the project doesn't have one
            if ($project->owners()->count() === 0) {
                // Mix between our explicit lead account and random pool users
                $project->owners()->attach([
                    $adminOwner->id,
                    $poolUsers->random()->id
                ]);
            }

            // Grab a random subset of users for Editors (2 to 4 users)
            $randomEditors = $allUsers->random(rand(2, 4))->pluck('id')->toArray();
            // Filter out existing owners to keep database unique constraints happy
            $existingOwners = $project->owners()->pluck('users.id')->toArray();
            $editorIds = array_diff($randomEditors, $existingOwners);

            if (!empty($editorIds)) {
                $project->editors()->syncWithoutDetaching($editorIds);
            }

            // Grab a random subset of users for Participants (3 to 6 users)
            $randomParticipants = $allUsers->random(rand(3, 6))->pluck('id')->toArray();
            // Filter out existing owners/editors to prevent overlapping team roles
            $existingTeam = array_merge($existingOwners, $project->editors()->pluck('users.id')->toArray());
            $participantIds = array_diff($randomParticipants, $existingTeam);

            if (!empty($participantIds)) {
                $project->participants()->syncWithoutDetaching($participantIds);
            }
        }

        $this->command->info('Project Workspace Directories populated successfully!');
    }
}
