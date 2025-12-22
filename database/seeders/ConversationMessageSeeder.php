<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConversationMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Message::insert([
                'conversation_id' => 1,
                'sender_id' => 1,
                'sender_type' => "user",
                'message' => 'Hello',
                'message_type' => 'text',
                'receiver_type' => 'organization',
                'receiver_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
