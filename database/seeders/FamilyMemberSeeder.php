<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FamilyMember;
use App\Models\User;

class FamilyMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 1;

        // Example family members (make sure these users exist in the users table)
        $familyMembers = [
            [
                'family_member_id' => 2,
                'relationship' => 'Father',
                'status' => 'accepted',
            ],
            [
                'family_member_id' => 3,
                'relationship' => 'Mother',
                'status' => 'accepted',
            ],
            [
                'family_member_id' => 4,
                'relationship' => 'Brother',
                'status' => 'pending',
            ],
        ];

        foreach ($familyMembers as $member) {
            // Avoid duplication if seeder runs multiple times
            $exists = FamilyMember::where('user_id', $userId)
                ->where('family_member_id', $member['family_member_id'])
                ->exists();

            if (! $exists) {
                FamilyMember::create([
                    'user_id' => $userId,
                    'family_member_id' => $member['family_member_id'],
                    'relationship' => $member['relationship'],
                    'status' => $member['status'],
                ]);
            }

            // Create reciprocal accepted relation if status is accepted
            if ($member['status'] === 'accepted') {
                $reverseExists = FamilyMember::where('user_id', $member['family_member_id'])
                    ->where('family_member_id', $userId)
                    ->exists();

                if (! $reverseExists) {
                    FamilyMember::create([
                        'user_id' => $member['family_member_id'],
                        'family_member_id' => $userId,
                        'relationship' => $member['relationship'],
                        'status' => 'accepted',
                    ]);
                }
            }
        }

        $this->command->info('Family members seeded successfully for user ID 1.');
    }
}
