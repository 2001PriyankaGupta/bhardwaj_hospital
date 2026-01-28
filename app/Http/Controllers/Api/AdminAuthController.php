<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use \Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|min:10|max:15|unique:users,phone',
                'gender' => 'required|string|in:male,female,other',
                'age' => 'required|integer|min:1|max:120',
                'address' => 'required|string|max:500',
                'emergency_contact_number' => 'required|string|min:10|max:15',
                'alternate_contact_number' => 'sometimes|string|min:10|max:15',
                'basic_medical_history' => 'sometimes|string',
                'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->first()[0];
            return response()->json([
                'status' => 'false',
                'message' => 'Validation failed',
                'error' => $firstError
            ], 422);
        }

        $profilePicturePath = null;

        // Profile image upload
        if ($request->hasFile('profile_image')) {
            try {
                $file = $request->file('profile_image');
                $filename = 'profile_' . time() . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('profiles', $filename, 'public');
                $profilePicturePath = $filename;
            } catch (\Exception $e) {
                Log::error('Profile image upload failed: ' . $e->getMessage());
                return response()->json([
                    'status' => 'false',
                    'message' => 'Profile image upload failed',
                    'error' => 'Failed to upload profile image.'
                ], 500);
            }
        }

        try {
            DB::beginTransaction();

            $nameParts = explode(' ', $validated['name'], 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            $currentYear = date('Y');
            $birthYear = $currentYear - $validated['age'];
            $dateOfBirth = $birthYear . '-01-01';

            $lastPatient = Patient::orderBy('id', 'desc')->first();
            if ($lastPatient && preg_match('/PID(\d+)/', $lastPatient->patient_id, $matches)) {
                $lastNumber = (int) $matches[1];
                $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '000001';
            }
            $patientId = 'PID' . $nextNumber;

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'age' => $validated['age'],
                'address' => $validated['address'],
                'emergency_contact_number' => $validated['emergency_contact_number'],
                'alternate_contact_number' => $validated['alternate_contact_number'] ?? null,
                'basic_medical_history' => $validated['basic_medical_history'] ?? null,
                'profile_picture' => $profilePicturePath,
                'is_admin' => false,
                'user_type' => 'patient',
                'status' => 'active',
                'password' => bcrypt('default_password'),
            ]);

            // Create patient record
            Patient::create([
                'patient_id' => $patientId,
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'date_of_birth' => $dateOfBirth,
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'medical_history' => $validated['basic_medical_history'] ?? null
            ]);

            $user->save();

            // ✅ TOKEN GENERATION CODE ADDED HERE
            try {
                $token = JWTAuth::fromUser($user);
            } catch (\Exception $tokenException) {
                Log::error('Token generation failed: ' . $tokenException->getMessage());
                throw new \Exception('Token generation failed');
            }

            // Commit transaction
            DB::commit();

            return response()->json([
                'status' => 'true',
                'message' => 'Patient registered successfully',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => 43200 * 60
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Patient registration failed: ' . $e->getMessage());

            if ($profilePicturePath) {
                try {
                    Storage::disk('public')->delete('profiles/' . $profilePicturePath);
                } catch (\Exception $deleteException) {
                    Log::error('Failed to delete uploaded image: ' . $deleteException->getMessage());
                }
            }

            return response()->json([
                'status' => 'false',
                'message' => 'Patient registration failed',
                'error' => $e->getMessage() // ✅ Better to show actual error for debugging
            ], 500);
        }
    }

    // Step 1: Request OTP
    public function requestOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Validation failed',
                'error' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'false',
                'message' => 'Email not registered',
                'error' => 'This email is not registered. Please register first.'
            ], 404);
        }

        // Ensure only non-admin users can login
        if ($user->is_admin == 1) {
            return response()->json([
                'status' => 'false',
                'message' => 'Admin login restricted',
                'error' => 'This account must login through the admin panel.'
            ], 403);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        $otpExpires = now()->addMinutes(10);

        // Save OTP to user
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => $otpExpires,
            'is_verified' => false
        ]);

        try {
            // Send OTP email
            Mail::to($user->email)->send(new OtpMail($otp, $user->name));

            return response()->json([
                'status' => 'true',
                'message' => 'OTP sent successfully',
                'data' => [
                    'email' => $user->email,
                    'expires_in' => 10 // minutes
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('OTP sending failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'false',
                'message' => 'OTP sending failed',
                'error' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    // Step 2: Verify OTP and Login
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Validation failed',
                'error' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Check if OTP matches and is not expired
        if (!$user->otp || $user->otp !== $request->otp) {
            return response()->json([
                'status' => 'false',
                'message' => 'Invalid OTP',
                'error' => 'The OTP you entered is invalid.'
            ], 401);
        }

        if (now()->gt($user->otp_expires_at)) {
            return response()->json([
                'status' => 'false',
                'message' => 'OTP expired',
                'error' => 'The OTP has expired. Please request a new one.'
            ], 401);
        }



        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'true',
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'user_type' => $user->user_type
            ],
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 43200 * 60
        ], 200);
    }

    // Resend OTP
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Validation failed',
                'error' => $validator->errors()->first()
            ], 422);
        }

        // ✅ Yehi wala method new OTP generate karega aur database update karega
        return $this->requestOtp($request);
    }

    public function logout(Request $request)
    {
        auth('api')->logout();

        return response()->json([
            'status' => 'true',
            'message' => 'Logged out successfully'
        ]);
    }

    public function refresh()
    {
        $token = auth('api')->refresh();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }


   public function updateDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Validation failed',
                'error' => $validator->errors()->first()
            ], 422);
        }

        // 🔐 JWT se logged-in patient
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $user->device_token = $request->device_token;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Device token saved successfully'
        ]);
    }



}
