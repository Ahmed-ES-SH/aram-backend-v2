<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Todo;
use App\Models\User;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user or create one if none exists
        $user = User::first();

        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $userId = $user->id;

        $todos = [
            [
                'user_id' => $userId,
                'title' => 'Complete Project Documentation',
                'description' => 'Finish the API documentation for the new module.',
                'priority' => 'high',
                'is_completed' => false,
            ],
            [
                'user_id' => $userId,
                'title' => 'Review Pull Requests',
                'description' => 'Check the pending PRs from the frontend team.',
                'priority' => 'high',
                'is_completed' => false,
            ],
            [
                'user_id' => $userId,
                'title' => 'Update Dependencies',
                'description' => 'Run composer update and npm update to get latest packages.',
                'priority' => 'medium',
                'is_completed' => false,
            ],
            [
                'user_id' => $userId,
                'title' => 'Team Meeting',
                'description' => 'Weekly sync with the development team.',
                'priority' => 'medium',
                'is_completed' => true,
            ],
            [
                'user_id' => $userId,
                'title' => 'Clean up temporary files',
                'description' => 'Remove old logs and temp uploads from verify folder.',
                'priority' => 'low',
                'is_completed' => false,
            ],
            [
                'user_id' => $userId,
                'title' => 'Research new JS framework',
                'description' => 'Look into the latest features of Vue 3.',
                'priority' => 'low',
                'is_completed' => true,
            ],
        ];

        foreach ($todos as $todo) {
            Todo::create($todo);
        }
    }
}
