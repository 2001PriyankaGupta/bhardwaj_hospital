<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationTemplatesSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            // SMS Templates
            [
                'name' => 'Order Confirmation SMS',
                'type' => 'sms',
                'subject' => null,
                'content' => 'Hello {customer_name}, your order #{order_id} has been confirmed. Total amount: {amount}. Thank you for shopping with us!',
                'variables' => ['customer_name', 'order_id', 'amount'],
                'status' => true
            ],
            [
                'name' => 'OTP Verification SMS', 
                'type' => 'sms',
                'subject' => null,
                'content' => 'Your OTP for verification is {otp}. This OTP is valid for 10 minutes. Do not share with anyone.',
                'variables' => ['otp'],
                'status' => true
            ],
            [
                'name' => 'Delivery Update SMS',
                'type' => 'sms',
                'subject' => null,
                'content' => 'Hi {customer_name}, your order #{order_id} is out for delivery. Expected delivery: {delivery_time}. Track: {tracking_link}',
                'variables' => ['customer_name', 'order_id', 'delivery_time', 'tracking_link'],
                'status' => true
            ],

            // Email Templates
            [
                'name' => 'Welcome Email',
                'type' => 'email',
                'subject' => 'Welcome to Our Service - Get Started!',
                'content' => "Dear {name},\n\nWelcome to our platform! We're excited to have you on board.\n\nYour account has been successfully created with email: {email}\n\nGet started by exploring our features.\n\nIf you have any questions, feel free to contact our support team.\n\nBest regards,\nThe Team",
                'variables' => ['name', 'email'],
                'status' => true
            ],
            [
                'name' => 'Order Invoice Email',
                'type' => 'email', 
                'subject' => 'Your Order Invoice #{order_number}',
                'content' => "Hello {customer_name},\n\nThank you for your order! Here is your invoice details:\n\nOrder Number: {order_number}\nOrder Date: {order_date}\nTotal Amount: {total_amount}\n\nYou can track your order here: {tracking_link}\n\nThank you for shopping with us!",
                'variables' => ['customer_name', 'order_number', 'order_date', 'total_amount', 'tracking_link'],
                'status' => true
            ],
            [
                'name' => 'Password Reset Email',
                'type' => 'email',
                'subject' => 'Password Reset Request', 
                'content' => "Hello {name},\n\nWe received a request to reset your password. Click the link below to reset your password:\n\nReset Link: {reset_link}\n\nThis link will expire in 60 minutes.",
                'variables' => ['name', 'reset_link'],
                'status' => true
            ],

            // Push Notification Templates
            [
                'name' => 'New Message Alert',
                'type' => 'push',
                'subject' => 'New Message',
                'content' => 'You have a new message from {sender_name}',
                'variables' => ['sender_name'],
                'status' => true
            ],
            [
                'name' => 'Special Offer Push',
                'type' => 'push',
                'subject' => 'Special Offer!', 
                'content' => 'Get {discount}% off on your next purchase. Limited time offer!',
                'variables' => ['discount'],
                'status' => true
            ],
            [
                'name' => 'System Update Notification',
                'type' => 'push',
                'subject' => 'System Update',
                'content' => 'New features available! Update your app to version {version} to enjoy the latest improvements.',
                'variables' => ['version'],
                'status' => true
            ]
        ];

        foreach ($templates as $template) {
            NotificationTemplate::create($template);
        }

        $this->command->info('Notification templates seeded successfully!');
    }
}