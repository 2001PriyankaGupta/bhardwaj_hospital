<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorLike;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class DoctorLikeController extends Controller
{
   
    public function toggleLike($doctorId)
    {

            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login first to access conversations.'
                ], 401);
            }
          
            
            // Check if user is patient
            if ($user->user_type !== 'patient') {
                return response()->json([
                    'status' => false,
                    'message' => 'Only patients can like doctors'
                ], 403);
            }
            
            // Check if like already exists
            $like = DoctorLike::where('user_id', $user->id)
                ->where('doctor_id', $doctorId)
                ->first();
            
            if ($like) {
                // Toggle like status
                $like->update(['is_liked' => !$like->is_liked]);
            } else {
                // Create new like
                $like = DoctorLike::create([
                    'user_id' => $user->id,
                    'doctor_id' => $doctorId,
                    'is_liked' => true
                ]);
            }
            
            // Get total likes for this doctor
            $totalLikes = DoctorLike::where('doctor_id', $doctorId)
                ->where('is_liked', true)
                ->count();
            
            return response()->json([
                'status' => true,
                'message' => $like->is_liked ? 'Doctor liked successfully' : 'Doctor unliked successfully',
                'data' => [
                    'doctor_id' => (int)$doctorId,
                    'is_liked' => $like->is_liked,
                    'total_likes' => $totalLikes
                ]
            ]);
            
        
    }
    
   
     public function getDoctorLikes($doctorId)
    {
        $likes = DoctorLike::with('user:id,name')
            ->where('doctor_id', $doctorId)
            ->where('is_liked', true)
            ->get();
            
        return response()->json([
            'total' => $likes->count(),
            'likes' => $likes
        ]);
    }
}