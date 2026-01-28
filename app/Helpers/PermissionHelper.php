<?php

namespace App\Helpers;

use App\Models\RolePermission;

class PermissionHelper
{
    public static function hasPermission($permissionKey)
    {
        // Agar user authenticated nahi hai
        if (!auth()->check()) {
            return false;
        }

        $userType = auth()->user()->user_type;
        
        // Admin ko hamesha sab permissions
        if ($userType === 'admin') {
            return true;
        }

        // Check permission from database
        $permission = RolePermission::where('role', $userType)
            ->where('permission_key', $permissionKey)
            ->first();

        return $permission && $permission->is_allowed;
    }

    public static function getAllowedPermissions()
    {
        if (!auth()->check()) {
            return [];
        }

        $userType = auth()->user()->user_type;
        
        if ($userType === 'admin') {
            return [
                'dashboard' => true,
                'bed_management' => true,
                'appointment_calendar' => true,
                'event_management' => true,
                'queue_management' => true,
                'doctor_management' => true,
                'staff_management' => true,
                'patient_management' => true,
                'emergency_triage' => true,
                'billing_invoices' => true,
                'department_setup' => true,
                'room_ward' => true,
                'service_pricing' => true,
                'notification_templates' => true,
                'system_settings' => true
            ];
        }

        $permissions = RolePermission::where('role', $userType)
            ->where('is_allowed', true)
            ->pluck('is_allowed', 'permission_key')
            ->toArray();

        return $permissions;
    }
}