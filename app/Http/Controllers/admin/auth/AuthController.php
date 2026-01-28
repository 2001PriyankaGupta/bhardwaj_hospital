<?php

namespace App\Http\Controllers\admin\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Staff;
use App\Models\Role;

class AuthController extends Controller
{
    public function login()
    {
         return view('admin.auth.login');
    }

    // public function loginSubmit(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|min:6',
    //         'user_type' => 'required|in:admin,doctor,staff'
    //     ]);

    //     $credentials = [
    //         'email' => $request->email,
    //         'password' => $request->password
    //     ];

    //     if (Auth::attempt($credentials)) {
    //         $user = Auth::user();

    //         // Check if user type matches
    //         if ($user->user_type !== $request->user_type) {
    //             Auth::logout();
    //             return redirect()->route('login')
    //                 ->with('error', 'Please select correct role for login');
    //         }

    //         // Redirect based on user_type with correct routes
    //         switch ($user->user_type) {
    //             case 'admin':
    //                 return redirect()->route('admin.dashboard')->with('success', 'Login successful!');

    //             case 'doctor':
    //                 return redirect()->route('doctor.dashboard')->with('success', 'Login successful!');

    //             case 'staff':
    //                 return redirect()->route('staff.dashboard')->with('success', 'Login successful!');
    //         }
    //     }

    //     return redirect()->route('login')->with('error', 'Invalid credentials');
    // }

    public function loginSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'user_type' => 'required|in:admin,doctor,staff'
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->user_type !== $request->user_type) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Please select correct role for login');
            }

            $this->assignRoleToUser($user);

            switch ($user->user_type) {
                case 'admin':
                    return redirect()->route('admin.dashboard')->with('success', 'Login successful!');

                case 'staff':
                    return redirect()->route('staff.dashboard')->with('success', 'Login successful!');

                case 'doctor':
                    return redirect()->route('doctor.dashboard')->with('success', 'Login successful!');
            }
        }

        return redirect()->route('login')->with('error', 'Invalid credentials');
    }

    
    protected function assignRoleToUser($user)
    {
        if ($user->role_id) {
            return;
        }

        $roleSlug = null;
        
        switch ($user->user_type) {
            case 'admin':
                $roleSlug = 'admin';
                break;
            case 'staff':
                $roleSlug = 'staff';
                break;
            case 'doctor':
                // Doctor ka koi role nahi assign karna
                return;
        }

        if ($roleSlug) {
            $role = Role::where('slug', $roleSlug)->first();
            if ($role) {
                $user->role_id = $role->id;
                $user->save();
            }
        }
    }

    public function logout(Request $request)
    {
        Log::info('User logout', [
            'user_id' => Auth::id(),
            'user_type' => Auth::user()->user_type ?? 'unknown'
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully');
    }
}




