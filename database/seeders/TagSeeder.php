<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'PHP',
            'Laravel',
            'JavaScript',
            'Vue.js',
            'React',
            'Node.js',
            'Python',
            'Django',
            'MySQL',
            'PostgreSQL',
            'HTML',
            'CSS',
            'Bootstrap',
            'Tailwind CSS',
            'API',
            'REST',
            'GraphQL',
            'Git',
            'Docker',
            'AWS'
        ];

        foreach ($tags as $tagName) {
            Tag::create([
                'tag' => $tagName
            ]);
        }
    }
}
