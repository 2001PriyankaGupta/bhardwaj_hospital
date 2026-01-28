<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['site_name', 'My Application', 'general', 'Application Name'],
            ['site_email', 'admin@example.com', 'general', 'Admin Email'],
            ['site_description', 'Welcome to our application', 'general', 'Site Description'],
            ['timezone', 'UTC', 'general', 'System Timezone'],
            ['maintenance_mode', '0', 'general', 'Maintenance Mode'],
            ['max_login_attempts', '5', 'security', 'Maximum Login Attempts'],
            ['session_timeout', '120', 'security', 'Session Timeout in Minutes'],
            ['two_factor_auth', '0', 'security', 'Two Factor Authentication'],
            ['password_expiry', '90', 'security', 'Password Expiry in Days'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting[0]],
                [
                    'value' => $setting[1],
                    'group' => $setting[2],
                    'description' => $setting[3],
                ]
            );
        }
    }
}
