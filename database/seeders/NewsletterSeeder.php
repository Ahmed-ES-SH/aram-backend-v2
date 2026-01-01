<?php

namespace Database\Seeders;

use App\Models\Newsletter;
use Illuminate\Database\Seeder;

class NewsletterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Monthly Update
        Newsletter::create([
            'subject' => 'January Monthly Update',
            'content' => 'Here are the latest updates from our platform for the month of January.',
            'section_1_title' => 'New Feature: Dashboards',
            'section_1_description' => 'We have launched a new dashboard for better analytics.',
            'section_2_title' => 'Community Spotlight',
            'section_2_description' => 'Highlighting our top contributors this month.',
            'section_3_title' => 'Upcoming Maintenance',
            'section_3_description' => 'Scheduled maintenance on Jan 25th from 2 AM to 4 AM.',
        ]);

        // 2. Special Offer
        Newsletter::create([
            'subject' => 'Exclusive 50% Off Offer',
            'content' => 'Don\'t miss out on this limited time offer just for you.',
            'section_1_title' => '50% Discount on Annual Plans',
            'section_1_description' => 'Use code JAN50 at checkout.',
            'section_2_image' => 'https://via.placeholder.com/600x200?text=Sale+Banner',
            'section_3_title' => 'Refer a Friend',
            'section_3_description' => 'Get extra month free when you refer.',
        ]);

        // 3. Weekly Digest
        Newsletter::create([
            'subject' => 'Weekly Tech Digest #42',
            'content' => 'Top tech news of the week curated for you.',
            'section_1_title' => 'AI Breakthroughs',
            'section_1_description' => 'New models are changing the landscape of coding.',
            'section_2_title' => 'Laravel 11 Released',
            'section_2_description' => 'Exciting new features in the latest framework update.',
            'section_3_title' => 'Web Security Tips',
            'section_3_description' => 'How to secure your API endpoints effectively.',
        ]);

        // 4. Event Invitation
        Newsletter::create([
            'subject' => 'Invitation: Annual Developer Conference',
            'content' => 'Join us for the biggest developer event of the year.',
            'section_1_image' => 'https://via.placeholder.com/600x300?text=Conference+Hero',
            'section_2_title' => 'Keynote Speakers',
            'section_2_description' => 'Hear from industry leaders and pioneers.',
            'section_3_title' => 'Workshop Schedule',
            'section_3_description' => 'Hands-on sessions on Cloud, AI, and DevOps.',
        ]);
    }
}
