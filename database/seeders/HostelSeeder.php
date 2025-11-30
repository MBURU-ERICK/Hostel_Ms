<?php

namespace Database\Seeders;

use App\Models\Hostel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HostelSeeder extends Seeder
{
    public function run(): void
    {
        // Create landlord users
        $landlords = [
            [
                'name' => 'James Mwangi',
                'email' => 'james.mwangi@example.com',
                'phone' => '+254711223344',
                'password' => 'password',
            ],
            [
                'name' => 'Sarah Kamau',
                'email' => 'sarah.kamau@example.com',
                'phone' => '+254722334455',
                'password' => 'password',
            ],
            [
                'name' => 'David Ochieng',
                'email' => 'david.ochieng@example.com',
                'phone' => '+254733445566',
                'password' => 'password',
            ],
            [
                'name' => 'Grace Wanjiku',
                'email' => 'grace.wanjiku@example.com',
                'phone' => '+254744556677',
                'password' => 'password',
            ],
        ];

        $landlordUsers = [];

        foreach ($landlords as $landlordData) {
            $landlord = User::firstOrCreate(
                ['email' => $landlordData['email']],
                [
                    'name' => $landlordData['name'],
                    'password' => Hash::make($landlordData['password']),
                    'user_type' => 'landlord',
                    'phone' => $landlordData['phone'],
                    'is_approved' => true,
                    'email_verified_at' => now(),
                ]
            );
            $landlordUsers[] = $landlord;
        }

        // Sample hostels data
        $hostels = [
            // University Area Hostels
            [
                'name' => 'University Heights Hostel',
                'description' => 'Modern hostel located just 5 minutes walk from the university main gate. Features 24/7 security, high-speed WiFi, and comfortable study areas. Perfect for serious students who value both comfort and convenience.',
                'location' => 'Nairobi',
                'address' => '123 University Road, Westlands, Nairobi',
                'rent_per_month' => 15000,
                'deposit_amount' => 10000,
                'rooms_available' => 8,
                'total_rooms' => 25,
                'amenities' => ['WiFi', 'Security', 'Laundry', 'Common Room', 'Kitchen', 'Study Room', 'Hot Water'],
                'rules' => "• No smoking in rooms\n• Quiet hours from 10 PM to 7 AM\n• Visitors allowed until 9 PM only\n• Keep common areas clean\n• Report maintenance issues promptly",
                'contact_phone' => '+254711223344',
                'contact_email' => 'universityheights@example.com',
                'landlord_index' => 0,
            ],
            [
                'name' => 'Campus View Apartments',
                'description' => 'Affordable student accommodation with spacious rooms and excellent recreational facilities. Located in a secure neighborhood with easy access to public transport and shopping centers.',
                'location' => 'Nairobi',
                'address' => '456 Campus Lane, Kilimani, Nairobi',
                'rent_per_month' => 12000,
                'deposit_amount' => 8000,
                'rooms_available' => 5,
                'total_rooms' => 15,
                'amenities' => ['WiFi', 'Study Room', 'Gym', 'Security', 'Parking', 'Common Room', 'CCTV'],
                'rules' => "• Student ID required for booking\n• Monthly room inspection\n• Visitors must register at reception\n• No cooking in rooms\n• Keep noise levels reasonable",
                'contact_phone' => '+254722334455',
                'contact_email' => 'campusview@example.com',
                'landlord_index' => 1,
            ],
            [
                'name' => 'Scholar\'s Residence',
                'description' => 'Premium student accommodation designed for academic excellence. Features soundproof study rooms, high-speed internet, and 24/7 power backup. Ideal for postgraduate and serious undergraduate students.',
                'location' => 'Nairobi',
                'address' => '789 Scholar Avenue, Lavington, Nairobi',
                'rent_per_month' => 20000,
                'deposit_amount' => 15000,
                'rooms_available' => 3,
                'total_rooms' => 12,
                'amenities' => ['High-Speed WiFi', 'Study Rooms', 'Library', 'Security', 'Backup Power', 'Laundry', 'Cleaning Service'],
                'rules' => "• Academic environment maintained\n• Study-focused residence\n• Limited visitor hours\n• Respect quiet zones\n• Maintain cleanliness",
                'contact_phone' => '+254733445566',
                'contact_email' => 'scholarsresidence@example.com',
                'landlord_index' => 2,
            ],

            // Mombasa Hostels
            [
                'name' => 'Beachside Student Hostel',
                'description' => 'Beautiful hostel located near the beach with stunning ocean views. Perfect for students who enjoy a relaxed environment while pursuing their studies. Close to several colleges and universities.',
                'location' => 'Mombasa',
                'address' => '321 Beach Road, Nyali, Mombasa',
                'rent_per_month' => 11000,
                'deposit_amount' => 7000,
                'rooms_available' => 6,
                'total_rooms' => 18,
                'amenities' => ['WiFi', 'Security', 'Garden', 'Common Room', 'Beach Access', 'Bicycle Storage'],
                'rules' => "• Beach rules apply\n• Respect other residents\n• No loud music\n• Security curfew at 11 PM\n• Keep premises clean",
                'contact_phone' => '+254744556677',
                'contact_email' => 'beachside@example.com',
                'landlord_index' => 3,
            ],
            [
                'name' => 'Mombasa Tech Hub Hostel',
                'description' => 'Modern hostel catering to tech students with high-speed internet and computer lab access. Located in the growing tech corridor of Mombasa with easy access to tech companies and innovation hubs.',
                'location' => 'Mombasa',
                'address' => '654 Tech Lane, Bamburi, Mombasa',
                'rent_per_month' => 13000,
                'deposit_amount' => 9000,
                'rooms_available' => 4,
                'total_rooms' => 10,
                'amenities' => ['High-Speed WiFi', 'Computer Lab', 'Study Rooms', 'Security', 'Parking', 'Common Room'],
                'rules' => "• Tech-friendly environment\n• Respect equipment\n• No unauthorized software\n• Report tech issues\n• Collaborative learning encouraged",
                'contact_phone' => '+254711223344',
                'contact_email' => 'mombasatech@example.com',
                'landlord_index' => 0,
            ],

            // Kisumu Hostels
            [
                'name' => 'Lakeview Student Hostel',
                'description' => 'Serene hostel offering beautiful views of Lake Victoria. Peaceful environment perfect for focused studying. Located in a secure gated community with 24/7 security.',
                'location' => 'Kisumu',
                'address' => '987 Lakeview Road, Milimani, Kisumu',
                'rent_per_month' => 9000,
                'deposit_amount' => 6000,
                'rooms_available' => 7,
                'total_rooms' => 14,
                'amenities' => ['WiFi', 'Security', 'Garden', 'Common Room', 'Lake View', 'Quiet Study Areas'],
                'rules' => "• Peaceful environment maintained\n• Respect nature surroundings\n• Limited visitor hours\n• Community living values\n• Environmental consciousness",
                'contact_phone' => '+254722334455',
                'contact_email' => 'lakeview@example.com',
                'landlord_index' => 1,
            ],
            [
                'name' => 'Kisumu Metro Hostel',
                'description' => 'Contemporary hostel located in the heart of Kisumu city. Easy access to universities, shopping centers, and transport hubs. Modern amenities with urban convenience.',
                'location' => 'Kisumu',
                'address' => '246 Metro Street, Kisumu CBD',
                'rent_per_month' => 10000,
                'deposit_amount' => 7000,
                'rooms_available' => 5,
                'total_rooms' => 12,
                'amenities' => ['WiFi', 'Security', 'Common Room', 'Kitchen', 'Study Area', 'CCTV', 'Backup Power'],
                'rules' => "• Urban living rules\n• Security conscious\n• Respect city regulations\n• Community cooperation\n• Timely rent payment",
                'contact_phone' => '+254733445566',
                'contact_email' => 'kisumumetro@example.com',
                'landlord_index' => 2,
            ],

            // Nakuru Hostels
            [
                'name' => 'Green Valley Hostel',
                'description' => 'Eco-friendly hostel surrounded by nature. Perfect for environmentally conscious students. Features organic garden, recycling facilities, and sustainable living practices.',
                'location' => 'Nakuru',
                'address' => '135 Green Valley, Lanet, Nakuru',
                'rent_per_month' => 8500,
                'deposit_amount' => 5000,
                'rooms_available' => 8,
                'total_rooms' => 16,
                'amenities' => ['WiFi', 'Security', 'Garden', 'Recycling', 'Common Room', 'Bicycle Storage', 'Organic Garden'],
                'rules' => "• Eco-friendly practices\n• Recycling mandatory\n• Respect nature\n• Water conservation\n• Sustainable living",
                'contact_phone' => '+254744556677',
                'contact_email' => 'greenvalley@example.com',
                'landlord_index' => 3,
            ],
            [
                'name' => 'Nakuru Student Plaza',
                'description' => 'Modern student accommodation with all essential amenities. Located near major educational institutions with easy access to town amenities and transport.',
                'location' => 'Nakuru',
                'address' => '579 Student Plaza, Section 58, Nakuru',
                'rent_per_month' => 9500,
                'deposit_amount' => 6000,
                'rooms_available' => 6,
                'total_rooms' => 15,
                'amenities' => ['WiFi', 'Security', 'Common Room', 'Study Area', 'Kitchen', 'Laundry', 'Parking'],
                'rules' => "• Student-focused community\n• Academic priority\n• Respectful coexistence\n• Clean living standards\n• Security compliance",
                'contact_phone' => '+254711223344',
                'contact_email' => 'nakuruplaza@example.com',
                'landlord_index' => 0,
            ],

            // Eldoret Hostels
            [
                'name' => 'Eldoret Education Hostel',
                'description' => 'Purpose-built hostel for students in Eldoret education hub. Close to multiple universities and colleges. Features dedicated study areas and academic support environment.',
                'location' => 'Eldoret',
                'address' => '864 Education Road, Pioneer, Eldoret',
                'rent_per_month' => 8800,
                'deposit_amount' => 5500,
                'rooms_available' => 9,
                'total_rooms' => 20,
                'amenities' => ['WiFi', 'Security', 'Study Rooms', 'Library', 'Common Room', 'Tutorial Space'],
                'rules' => "• Education-focused\n• Academic integrity\n• Study group friendly\n• Respect learning spaces\n• Collaborative environment",
                'contact_phone' => '+254722334455',
                'contact_email' => 'eldoretedu@example.com',
                'landlord_index' => 1,
            ],
        ];

        foreach ($hostels as $hostelData) {
            $landlord = $landlordUsers[$hostelData['landlord_index']];

            Hostel::firstOrCreate(
                ['name' => $hostelData['name']],
                [
                    'landlord_id' => $landlord->id,
                    'description' => $hostelData['description'],
                    'location' => $hostelData['location'],
                    'address' => $hostelData['address'],
                    'rent_per_month' => $hostelData['rent_per_month'],
                    'deposit_amount' => $hostelData['deposit_amount'],
                    'rooms_available' => $hostelData['rooms_available'],
                    'total_rooms' => $hostelData['total_rooms'],
                    'amenities' => $hostelData['amenities'],
                    'rules' => $hostelData['rules'],
                    'is_approved' => true,
                    'is_available' => $hostelData['rooms_available'] > 0,
                    'contact_phone' => $hostelData['contact_phone'],
                    'contact_email' => $hostelData['contact_email'],
                ]
            );
        }

        $this->command->info('Successfully seeded ' . count($hostels) . ' hostels with ' . count($landlords) . ' landlords.');
        $this->command->info('Landlord login credentials:');
        foreach ($landlords as $landlord) {
            $this->command->info("Email: {$landlord['email']} | Password: {$landlord['password']}");
        }
    }
}
