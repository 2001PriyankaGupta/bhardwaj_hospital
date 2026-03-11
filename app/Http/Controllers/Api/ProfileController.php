<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Patient;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class ProfileController extends Controller
{

    public function updateProfile(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Please login first to update profile.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'gender' => 'sometimes|in:male,female,other',
            'phone' => 'sometimes|string|max:20',
            'date_of_birth' => 'sometimes|date',
            'age' => 'sometimes|integer|min:1|max:120',
            'address' => 'sometimes|string|max:500',
            'emergency_contact_number' => 'sometimes|string|max:20',
            'alternate_contact_number' => 'sometimes|string|max:20|nullable',
            'basic_medical_history' => 'sometimes|string|nullable',
            'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                
                if ($user->profile_picture) {
                    $oldImagePath = 'profiles/' . $user->profile_picture;
                    Storage::disk('public')->delete($oldImagePath);
                }

                $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('profiles', $filename, 'public');
                
                $user->profile_picture = $filename;
            }

            if ($request->has('name')) {
                $user->name = $request->name;
            }
            
            if ($request->has('gender')) {
                $user->gender = $request->gender;
            }
            
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            
            if ($request->has('age')) {
                $user->age = $request->age;
            }
            
            if ($request->has('address')) {
                $user->address = $request->address;
            }
            
            if ($request->has('emergency_contact_number')) {
                $user->emergency_contact_number = $request->emergency_contact_number;
            }
            
            if ($request->has('alternate_contact_number')) {
                $user->alternate_contact_number = $request->alternate_contact_number;
            }
            
            if ($request->has('basic_medical_history')) {
                $user->basic_medical_history = $request->basic_medical_history;
            }

            if ($request->has('date_of_birth')) {
                // If DOB is updated, also recalculate age
                $user->age = \Carbon\Carbon::parse($request->date_of_birth)->age;
            }

            $user->save();

            // Find and update corresponding patient record
            $patient = Patient::where('user_id', $user->id)->first();
            
            if ($patient) {
                if ($request->has('name')) {
                    $nameParts = explode(' ', $request->name, 2);
                    $patient->first_name = $nameParts[0];
                    $patient->last_name = isset($nameParts[1]) ? $nameParts[1] : '';
                }
                
                if ($request->has('email')) {
                    $patient->email = $request->email;
                }
                
                if ($request->has('phone')) {
                    $patient->phone = $request->phone;
                }
                
                if ($request->has('gender')) {
                    $patient->gender = $request->gender;
                }
                
                if ($request->has('age')) {
                    // Update date of birth from age
                    $currentYear = date('Y');
                    $birthYear = $currentYear - $request->age;
                    $patient->date_of_birth = $birthYear . '-01-01';
                }
                
                if ($request->has('address')) {
                    $patient->address = $request->address;
                }
                
                if ($request->has('basic_medical_history')) {
                    $patient->medical_history = $request->basic_medical_history;
                }

                if ($request->has('date_of_birth')) {
                    $patient->date_of_birth = $request->date_of_birth;
                }
                
                $patient->save();
            }

            // Commit transaction
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully'
            ], 200);

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            Log::error('Profile update failed: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProfile()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'age' => $user->age,
                'address' => $user->address,
                'emergency_contact_number' => $user->emergency_contact_number,
                'alternate_contact_number' => $user->alternate_contact_number,
                'basic_medical_history' => $user->basic_medical_history,
                'profile_picture' =>$user->profile_picture,
                'user_type' => $user->user_type,
                'status' => $user->status,
                'patient_id' => $user->patient?->patient_id,
                'date_of_birth' => $user->patient?->date_of_birth,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Profile retrieved successfully',
                'user' => $userData, 
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // Additional method to update password
    public function updatePassword(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Please login first to update password.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully'
        ], 200);
    }

    public function updateProfileImage(Request $request)
    {
        try {
            // Authenticate user via JWT
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Please login first to update profile image.'
            ], 401);
        }

        // Validate only image
        $validator = Validator::make($request->all(), [
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');

                // Delete old image if exists
                if ($user->profile_picture) {
                    $oldImagePath = 'profiles/' . $user->profile_picture;
                    Storage::disk('public')->delete($oldImagePath);
                }

                // Save new image
                $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('profiles', $filename, 'public');

                // Update user record
                $user->profile_picture = $filename;
                $user->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Profile image updated successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update profile image',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}   