<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'author_name'     => 'Dr. A. Mbarga',
                'author_title'    => 'General Practitioner',
                'author_facility' => null,
                'author_country'  => 'Cameroon',
                'body'            => 'OPES EMR transformed how we manage patient records at our clinic. For the first time, our staff in both Yaoundé and Douala are working from the same system — in French and English.',
                'body_fr'         => null,
                'rating'          => 5,
                'is_active'       => true,
                'sort_order'      => 1,
            ],
            [
                'author_name'     => 'Hospital Administrator',
                'author_title'    => 'Administrator',
                'author_facility' => 'Regional Hospital',
                'author_country'  => 'Cameroon',
                'body'            => 'Opes Triage integrated into our existing workflow in under a week. Patient wait times dropped noticeably. I recommend it to every hospital administrator in the region.',
                'body_fr'         => null,
                'rating'          => 5,
                'is_active'       => true,
                'sort_order'      => 2,
            ],
        ];

        foreach ($testimonials as $data) {
            Testimonial::updateOrCreate(
                ['author_name' => $data['author_name'], 'author_title' => $data['author_title']],
                $data
            );
        }
    }
}
