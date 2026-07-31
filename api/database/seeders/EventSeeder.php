<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'title' => 'Tech Conference 2026',
                'slug' => Str::slug('Tech Conference 2026'),
                'category' => 'Technology',
                'venue' => 'Jakarta Convention Center',
                'organizer' => 'Maxy Academy',
                'description' => 'Konferensi teknologi terbesar tahun ini, membahas AI, Cloud, dan Cybersecurity.',
                'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&q=80',
                'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&q=80',
                'start_date' => '2026-09-15 09:00:00',
                'end_date' => '2026-09-15 17:00:00',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Digital Marketing Workshop',
                'slug' => Str::slug('Digital Marketing Workshop'),
                'category' => 'Marketing',
                'venue' => 'Online (Zoom)',
                'organizer' => 'Maxy Academy',
                'description' => 'Pelatihan intensif tentang strategi digital marketing untuk bisnis skala kecil dan menengah.',
                'thumbnail' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&q=80',
                'banner' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&q=80',
                'start_date' => '2026-08-20 13:00:00',
                'end_date' => '2026-08-20 16:00:00',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Startup Founders Meetup',
                'slug' => Str::slug('Startup Founders Meetup'),
                'category' => 'Business',
                'venue' => 'Co-working Space Sudirman',
                'organizer' => 'Maxy Academy',
                'description' => 'Ajang kumpul dan networking para pendiri startup untuk berbagi pengalaman dan mencari investor.',
                'thumbnail' => 'https://images.unsplash.com/photo-1515169067868-5387ec356754?w=600&q=80',
                'banner' => 'https://images.unsplash.com/photo-1515169067868-5387ec356754?w=1200&q=80',
                'start_date' => '2026-10-05 18:30:00',
                'end_date' => '2026-10-05 21:00:00',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('events')->insert($events);
    }
}

