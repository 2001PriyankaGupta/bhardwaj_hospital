<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\admin\auth\AuthController;
use Illuminate\Http\Request;


Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        switch ($user->user_type) {
            case 'admin':
                return redirect()->route('admin.dashboard')->with('success', 'Login successful!');

            case 'doctor':
                return redirect()->route('doctor.dashboard')->with('success', 'Login successful!');

            case 'staff':
                return redirect()->route('staff.dashboard')->with('success', 'Login successful!');
        }
    }
    return redirect()->route('login');
});

Route::get('login', [AuthController::class, 'login'])->name('login');

// Patient quick join page (useful for manual testing)
Route::get('patient/join', function (Request $request) {
    return view('patient.appointment.join', ['call_id' => $request->query('call_id')]);
})->name('patient.join');
