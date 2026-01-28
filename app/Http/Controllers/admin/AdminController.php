<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{


    public function dashboard()
    {
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $petData = [12,19,3,5,2,3,15,8,12,10,6,14];
        $activeUsers = 75;
        $inactiveUsers = 25;

        // Fetch dynamic data for the dashboard
        $appointmentsToday = Appointment::whereDate('created_at', today())->count();
        $totalPatients = Patient::count();
        $availableDoctors = Doctor::where('status', 'active')->count();
        $pendingTests = Appointment::where('status', 'pending')->count();

        // Calculate dynamic sub-labels
        $newAdmissionsToday = Patient::whereDate('created_at', today())->count();
        $doctorsOnEmergency = 2;
        $pendingTestsYesterday = Appointment::where('status', 'pending')
            ->whereDate('created_at', today()->subDay())
            ->count();
        $pendingTestsDifference = $pendingTests - $pendingTestsYesterday;

        // Fetch recent activities
        $recentActivities = Appointment::with(['doctor', 'resource', 'patient', 'conversation'])->latest()->take(5)->get();

        return view('admin.index', compact(
            'months', 'petData', 'activeUsers', 'inactiveUsers',
            'appointmentsToday', 'totalPatients', 'availableDoctors', 'pendingTests',
            'newAdmissionsToday', 'doctorsOnEmergency', 'pendingTestsDifference', 'recentActivities'
        ));
    }

    public function emergency()
    {
        return view('admin.emergency_dashboard');
    }
}
