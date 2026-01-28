<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventsTableSeeder extends Seeder
{
    public function run()
    {
        $events = [
            [
                'title' => 'Annual Blood Donation Camp 2024',
                'type' => 'blood_donation',
                'description' => 'Join us for our annual blood donation camp. Your single donation can save up to 3 lives. All blood types are needed.',
                'event_date' => now()->addDays(15),
                'start_time' => '09:00',
                'end_time' => '17:00',
                'venue' => 'Hospital Main Auditorium',
                'organizer' => 'Hospital Blood Bank',
                'contact_person' => 'Dr. Rajesh Kumar',
                'contact_number' => '+91 9876543210',
                'email' => 'bloodbank@hospital.com',
                'target_participants' => 200,
                'facebook_url' => 'https://facebook.com/blooddonationcamp',
                'instagram_url' => 'https://instagram.com/blooddonationcamp',
                'requirements' => 'Donors must be 18-65 years old, weigh at least 50kg, and be in good health. Please bring your ID proof.',
                'status' => 'upcoming',
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title' => 'Diabetes Awareness Workshop',
                'type' => 'workshop',
                'description' => 'Learn about diabetes prevention, management, and healthy lifestyle choices. Free blood sugar testing available.',
                'event_date' => now()->addDays(7),
                'start_time' => '10:00',
                'end_time' => '13:00',
                'venue' => 'Conference Room A',
                'organizer' => 'Endocrinology Department',
                'contact_person' => 'Dr. Priya Sharma',
                'target_participants' => 50,
                'status' => 'upcoming',
                'is_featured' => false,
                'is_published' => true,
            ],
            [
                'title' => 'Free Health Checkup Camp',
                'type' => 'health_camp',
                'description' => 'Free health checkup including BP, sugar, ECG, and basic consultations. Open to all age groups.',
                'event_date' => now()->addDays(30),
                'start_time' => '08:00',
                'end_time' => '16:00',
                'venue' => 'Hospital Ground Floor',
                'target_participants' => 500,
                'facebook_url' => 'https://facebook.com/healthcamp',
                'twitter_url' => 'https://twitter.com/healthcamp',
                'status' => 'upcoming',
                'is_featured' => true,
                'is_published' => true,
            ],
        ];

        foreach ($events as $event) {
            $event['slug'] = Str::slug($event['title']) . '-' . Str::random(5);
            Event::create($event);
        }
    }
}