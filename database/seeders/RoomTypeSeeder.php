<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoomType;
use Carbon\Carbon;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = [
            [
                'name' => 'General Ward',
                'description' => 'Shared accommodation with basic medical facilities for multiple patients',
                'base_price' => 1500.00,
                'hourly_rate' => 200.00,
                'max_capacity' => 6,
                'available_rooms' => 8,
                'current_utilization' => 6,
                'amenities' => json_encode(['Hospital Bed', 'Side Table', 'Nurse Call Button', 'Oxygen Port', 'Privacy Curtain', 'Shared Bathroom']),
                'seasonal_pricing' => json_encode(['monsoon' => 1.1, 'winter' => 1.05, 'summer' => 1.0]),
                'discounts' => json_encode(['insurance' => 20, 'senior_citizen' => 15, 'bpl' => 30]),
                'capacity_forecast' => json_encode(['next_week' => 80, 'next_month' => 75]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Semi-Private Room',
                'description' => 'Room shared with one other patient with enhanced privacy',
                'base_price' => 3000.00,
                'hourly_rate' => 400.00,
                'max_capacity' => 2,
                'available_rooms' => 12,
                'current_utilization' => 9,
                'amenities' => json_encode(['Hospital Bed', 'Side Table', 'Nurse Call Button', 'Oxygen Port', 'TV', 'AC', 'Private Bathroom', 'Wardrobe']),
                'seasonal_pricing' => json_encode(['monsoon' => 1.15, 'winter' => 1.08, 'summer' => 1.0]),
                'discounts' => json_encode(['insurance' => 25, 'senior_citizen' => 20, 'bpl' => 35]),
                'capacity_forecast' => json_encode(['next_week' => 85, 'next_month' => 78]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Private Room',
                'description' => 'Single occupancy room with attached bathroom and basic amenities',
                'base_price' => 5000.00,
                'hourly_rate' => 650.00,
                'max_capacity' => 1,
                'available_rooms' => 15,
                'current_utilization' => 11,
                'amenities' => json_encode(['Hospital Bed', 'Side Table', 'Nurse Call Button', 'Oxygen Port', 'TV', 'AC', 'Private Bathroom', 'WiFi', 'Refrigerator', 'Sofa']),
                'seasonal_pricing' => json_encode(['monsoon' => 1.2, 'winter' => 1.1, 'summer' => 1.0]),
                'discounts' => json_encode(['insurance' => 30, 'senior_citizen' => 25, 'bpl' => 40]),
                'capacity_forecast' => json_encode(['next_week' => 90, 'next_month' => 82]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Deluxe Private Room',
                'description' => 'Premium single room with luxury amenities and services',
                'base_price' => 8000.00,
                'hourly_rate' => 1000.00,
                'max_capacity' => 1,
                'available_rooms' => 8,
                'current_utilization' => 6,
                'amenities' => json_encode(['Premium Bed', 'Side Table', 'Nurse Call Button', 'Oxygen Port', 'Smart TV', 'AC', 'Private Bathroom', 'WiFi', 'Mini Fridge', 'Sofa', 'Dining Table', 'Room Service']),
                'seasonal_pricing' => json_encode(['monsoon' => 1.25, 'winter' => 1.15, 'summer' => 1.0]),
                'discounts' => json_encode(['insurance' => 35, 'corporate' => 20]),
                'capacity_forecast' => json_encode(['next_week' => 88, 'next_month' => 80]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'ICU (Intensive Care Unit)',
                'description' => 'Critical care unit with advanced life support and monitoring equipment',
                'base_price' => 12000.00,
                'hourly_rate' => 1500.00,
                'max_capacity' => 1,
                'available_rooms' => 10,
                'current_utilization' => 8,
                'amenities' => json_encode(['ICU Bed', 'Ventilator', 'Multi-parameter Monitor', 'Defibrillator', 'Oxygen Supply', 'Suction', 'Infusion Pump', 'Emergency Drugs']),
                'seasonal_pricing' => json_encode(['monsoon' => 1.25, 'winter' => 1.15, 'summer' => 1.0]),
                'discounts' => json_encode(['insurance' => 40, 'government' => 25]),
                'capacity_forecast' => json_encode(['next_week' => 95, 'next_month' => 88]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'CCU (Cardiac Care Unit)',
                'description' => 'Specialized unit for cardiac patients with cardiac monitoring',
                'base_price' => 15000.00,
                'hourly_rate' => 1800.00,
                'max_capacity' => 1,
                'available_rooms' => 6,
                'current_utilization' => 5,
                'amenities' => json_encode(['Cardiac Bed', 'ECG Monitor', 'Defibrillator', 'Pacemaker', 'Oxygen Supply', 'Cardiac Drugs', 'Ventilator']),
                'seasonal_pricing' => json_encode(['monsoon' => 1.3, 'winter' => 1.2, 'summer' => 1.0]),
                'discounts' => json_encode(['insurance' => 45, 'government' => 30]),
                'capacity_forecast' => json_encode(['next_week' => 92, 'next_month' => 85]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'NICU (Neonatal ICU)',
                'description' => 'Specialized intensive care for newborn babies',
                'base_price' => 18000.00,
                'hourly_rate' => 2200.00,
                'max_capacity' => 1,
                'available_rooms' => 5,
                'current_utilization' => 4,
                'amenities' => json_encode(['Incubator', 'Ventilator', 'Monitor', 'Oxygen Supply', 'Phototherapy', 'Infusion Pump']),
                'seasonal_pricing' => json_encode(['monsoon' => 1.2, 'winter' => 1.1, 'summer' => 1.0]),
                'discounts' => json_encode(['insurance' => 50, 'government' => 35]),
                'capacity_forecast' => json_encode(['next_week' => 90, 'next_month' => 83]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Maternity Suite',
                'description' => 'Specialized suite for delivery and postpartum care',
                'base_price' => 10000.00,
                'hourly_rate' => 1200.00,
                'max_capacity' => 2,
                'available_rooms' => 8,
                'current_utilization' => 6,
                'amenities' => json_encode(['Delivery Bed', 'Baby Cot', 'Fetal Monitor', 'Private Bathroom', 'TV', 'AC', 'Sofa Bed', 'Nursing Chair']),
                'seasonal_pricing' => json_encode(['monsoon' => 1.18, 'winter' => 1.12, 'summer' => 1.0]),
                'discounts' => json_encode(['insurance' => 35, 'package' => 20, 'government' => 25]),
                'capacity_forecast' => json_encode(['next_week' => 88, 'next_month' => 80]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Pediatric Room',
                'description' => 'Child-friendly room with facilities for parent stay',
                'base_price' => 4500.00,
                'hourly_rate' => 550.00,
                'max_capacity' => 2,
                'available_rooms' => 10,
                'current_utilization' => 7,
                'amenities' => json_encode(['Child Bed', 'Parent Bed', 'TV', 'AC', 'Play Area', 'Monitoring Equipment', 'Private Bathroom']),
                'seasonal_pricing' => json_encode(['monsoon' => 1.15, 'winter' => 1.08, 'summer' => 1.0]),
                'discounts' => json_encode(['insurance' => 30, 'package' => 15, 'government' => 20]),
                'capacity_forecast' => json_encode(['next_week' => 82, 'next_month' => 75]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Isolation Room',
                'description' => 'Negative pressure room for infectious disease control',
                'base_price' => 10000.00,
                'hourly_rate' => 1200.00,
                'max_capacity' => 1,
                'available_rooms' => 4,
                'current_utilization' => 2,
                'amenities' => json_encode(['Specialized Bed', 'Negative Pressure', 'HEPA Filtration', 'PPE Station', 'Monitoring', 'Private Bathroom']),
                'seasonal_pricing' => json_encode(['monsoon' => 1.1, 'winter' => 1.05, 'summer' => 1.0]),
                'discounts' => json_encode(['insurance' => 40, 'government' => 30]),
                'capacity_forecast' => json_encode(['next_week' => 70, 'next_month' => 65]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        foreach ($roomTypes as $roomType) {
            RoomType::create($roomType);
        }

        $this->command->info('Hospital Room Types seeded successfully!');
        $this->command->info('Total room types created: ' . count($roomTypes));
    }
}