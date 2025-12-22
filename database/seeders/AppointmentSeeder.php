<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $appointments = [
            [
                'user_id' => 1,
                'organization_id' => 1,
                'start_time' => $now->copy()->addDays(3)->setTime(10, 0, 0),
                'end_time' => $now->copy()->addDays(3)->setTime(11, 0, 0),
                'price' => 150.00,
                'status' => 'pending',
                'user_notes' => 'Looking forward to my first visit!',
                'organization_notes' => null,
            ],
            [
                'user_id' => 1,
                'organization_id' => 2,
                'start_time' => $now->copy()->addDays(5)->setTime(14, 0, 0),
                'end_time' => $now->copy()->addDays(5)->setTime(15, 0, 0),
                'price' => 200.00,
                'status' => 'confirmed',
                'user_notes' => 'Confirmed booking for consultation.',
                'organization_notes' => 'Appointment confirmed successfully.',
                'confirmed_at' => $now->copy()->subDay(),
            ],
            [
                'user_id' => 1,
                'organization_id' => 3,
                'start_time' => $now->copy()->addDays(7)->setTime(9, 0, 0),
                'end_time' => $now->copy()->addDays(7)->setTime(10, 0, 0),
                'price' => 180.00,
                'status' => 'rejected',
                'user_notes' => 'Please confirm as soon as possible.',
                'organization_notes' => 'We are fully booked at this time.',
                'rejected_at' => $now->copy()->subDays(2),
            ],
            [
                'user_id' => 1,
                'organization_id' => 4,
                'start_time' => $now->copy()->addDays(10)->setTime(16, 0, 0),
                'end_time' => $now->copy()->addDays(10)->setTime(17, 0, 0),
                'price' => 120.00,
                'status' => 'cancelled_by_user',
                'user_notes' => 'Had to cancel due to personal reasons.',
                'organization_notes' => null,
                'cancelled_at' => $now->copy()->subDay(),
            ],
            [
                'user_id' => 1,
                'organization_id' => 5,
                'start_time' => $now->copy()->addDays(12)->setTime(11, 30, 0),
                'end_time' => $now->copy()->addDays(12)->setTime(12, 30, 0),
                'price' => 250.00,
                'status' => 'cancelled_by_org',
                'user_notes' => 'Excited to try this service!',
                'organization_notes' => 'Appointment cancelled due to maintenance.',
                'cancelled_at' => $now->copy()->subDays(3),
            ],
        ];

        foreach ($appointments as $data) {
            Appointment::create($data);
        }
    }
}
