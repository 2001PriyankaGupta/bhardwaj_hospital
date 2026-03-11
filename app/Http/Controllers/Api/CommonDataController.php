<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Service;
use App\Models\Event;
use App\Models\HealthTip;
use App\Models\Banner;
use App\Models\PatientNotify;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class CommonDataController extends Controller
{
   
    public function getServices()
    {
        try {
            $services = Service::all();
            
            return response()->json([
                'status' => true,
                'message' => 'Services retrieved successfully',
                'data' => $services
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve services',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function searchHealthTips(Request $request)
    {
        try {
            $q = $request->query('q') ?? $request->query('search');

            if (!$q) {
                return response()->json([
                    'status' => true,
                    'message' => 'Please provide search text',
                    'data' => []
                ], 200);
            }

            $healthTips = HealthTip::where('status', 1)
                ->where(function ($query) use ($q) {
                    $query->where('title', 'LIKE', "%{$q}%")
                        ->orWhere('description', 'LIKE', "%{$q}%")
                        ->orWhere('author', 'LIKE', "%{$q}%");
                })
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Health tips search results',
                'count' => $healthTips->count(),
                'data' => $healthTips
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to search health tips',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
    public function getEvents()
    {
        try {
            $events = Event::all();
            
            // Transform events to return only filenames
            $events->transform(function ($event) {
                // Single image - return just the filename
                if (!empty($event->image)) {
                    // Extract filename from full path or URL
                    if (filter_var($event->image, FILTER_VALIDATE_URL)) {
                        // If it's a URL, extract just the filename
                        $event->image = basename($event->image);
                    } else {
                        // If it's a path, remove 'storage/' prefix and get filename
                        $event->image = basename(str_replace('storage/', '', $event->image));
                    }
                }
                
                // Multiple images array - return just filenames
                if (!empty($event->images) && is_array($event->images)) {
                    $event->images = array_map(function ($image) {
                        if (!empty($image)) {
                            if (filter_var($image, FILTER_VALIDATE_URL)) {
                                // If it's a URL, extract just the filename
                                return basename($image);
                            } else {
                                // If it's a path, remove 'storage/' prefix and get filename
                                return basename(str_replace('storage/', '', $image));
                            }
                        }
                        return $image;
                    }, $event->images);
                }
                
                return $event;
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Events retrieved successfully',
                'data' => $events
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getEventById($id)
    {
        try {
            $event = Event::find($id);
            
            if (!$event) {
                return response()->json([
                    'status' => false,
                    'message' => 'Event not found'
                ], 404);
            }
            
            // Single image - return just the filename
            if (!empty($event->image)) {
                // Extract filename from full path or URL
                if (filter_var($event->image, FILTER_VALIDATE_URL)) {
                    // If it's a URL, extract just the filename
                    $event->image = basename($event->image);
                } else {
                    // If it's a path, remove 'storage/' prefix and get filename
                    $event->image = basename(str_replace('storage/', '', $event->image));
                }
            }
            
            // Multiple images - return just filenames
            if (!empty($event->images) && is_array($event->images)) {
                $event->images = array_map(function ($image) {
                    if (!empty($image)) {
                        if (filter_var($image, FILTER_VALIDATE_URL)) {
                            // If it's a URL, extract just the filename
                            return basename($image);
                        } else {
                            // If it's a path, remove 'storage/' prefix and get filename
                            return basename(str_replace('storage/', '', $image));
                        }
                    }
                    return $image;
                }, $event->images);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Event retrieved successfully',
                'data' => $event
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getHealthTips()
    {
        try {
            $healthTips = HealthTip::all();
            
            $healthTips->transform(function ($tip) {
                if (!empty($tip->image)) {
                    if (filter_var($tip->image, FILTER_VALIDATE_URL)) {
                        $tip->image = basename($tip->image);
                    } else {
                        $tip->image = basename(str_replace('storage/', '', $tip->image));
                    }
                }
                
                if (!empty($tip->images) && is_array($tip->images)) {
                    $tip->images = array_map(function ($image) {
                        if (!empty($image)) {
                            if (filter_var($image, FILTER_VALIDATE_URL)) {
                                return basename($image);
                            } else {
                                return basename(str_replace('storage/', '', $image));
                            }
                        }
                        return $image;
                    }, $tip->images);
                }
                
                return $tip;
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Health tips retrieved successfully',
                'data' => $healthTips
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve health tips',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBanners()
    {
        try {
            $banners = Banner::all();         

            $banners->transform(function ($banner) {
                if (!empty($banner->image)) {
                    if (filter_var($banner->image, FILTER_VALIDATE_URL)) {
                        $banner->image = basename($banner->image);
                    } else {
                        $banner->image = basename(str_replace('storage/', '', $banner->image));
                    }
                }

                return $banner;
            });

            return response()->json([
                'status' => true,
                'message' => 'Banners retrieved successfully',
                'data' => $banners
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve banners',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getNotifications()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Please login to view notifications.'
            ], 401);
        }

        try {
            // Check if user has patient profile
            if (!$user->patient) {
                return response()->json([
                    'status' => false,
                    'message' => 'Patient profile not found.'
                ], 404);
            }

            $notifications = PatientNotify::where('patient_id', $user->patient->id)
                ->latest()
                ->get();

            if ($notifications->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No notifications found.',
                    'data' => []
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Notifications retrieved successfully.',
                'data' => $notifications
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to retrieve notifications: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve notifications.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getPrescriptions(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Please login to view prescriptions.'
            ], 401);
        }

        try {
            if (!$user->patient) {
                return response()->json([
                    'status' => false,
                    'message' => 'Patient profile not found.'
                ], 404);
            }

            $query = Prescription::where('patient_id', $user->patient->id)
                ->with(['doctor', 'medicalRecord', 'appointment']);

            // ✅ Specific appointment (required parameter)
            if (!$request->has('appointment_id')) {
                return response()->json([
                    'status' => false,
                    'message' => 'appointment_id parameter is required.'
                ], 400);
            }

            $query->where('appointment_id', $request->appointment_id);
            
            $prescriptions = $query->orderBy('prescription_date', 'desc')
                ->get();

            if ($prescriptions->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No prescriptions found for this appointment.',
                    'data' => []
                ]);
            }

            // Format response
            $formattedPrescriptions = $prescriptions->map(function ($prescription) {
                return [
                    'id' => $prescription->id,
                    'prescription_date' => $prescription->prescription_date,
                    'medicines' => $prescription->medication_details,
                    'instructions' => $prescription->instructions,
                    'follow_up_advice' => $prescription->follow_up_advice,
                    'valid_until' => $prescription->valid_until,
                    'doctor' => $prescription->doctor ? [
                        'name' => $prescription->doctor->first_name . ' ' . $prescription->doctor->last_name,
                        'specialization' => $prescription->doctor->specialization,
                    ] : null,
                    
                ];
            });

            // Get appointment details from first prescription
            $appointmentDetails = null;
            if ($prescriptions->first()->appointment) {
                $appt = $prescriptions->first()->appointment;
                $appointmentDetails = [
                    'appointment_id' => $appt->id,
                    'date' => $appt->appointment_date,
                    'time' => $appt->appointment_time,
                    'status' => $appt->status,
                    'reason' => $appt->reason,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Prescriptions retrieved successfully.',
                'appointment' => $appointmentDetails,
                'prescriptions' => $formattedPrescriptions
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve prescriptions: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve prescriptions.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function checkAppVersion()
    {
        $latestVersion = \App\Models\SystemSetting::where('key', 'latest_app_version')->first()->value ?? '1.0.0';
        $updateUrl = \App\Models\SystemSetting::where('key', 'play_store_url')->first()->value ?? 'https://play.google.com/store/apps/details?id=com.bhardwaj.hospital';
        $updateMessage = \App\Models\SystemSetting::where('key', 'app_update_message')->first()->value ?? 'A new version of the app is available with latest features and improvements. Please update for a better experience.';

        return response()->json([
            'status' => true,
            'message' => 'App version info retrieved',
            'data' => [
                'latest_version' => $latestVersion,
                'update_url' => $updateUrl,
                'update_message' => $updateMessage
            ]
        ]);
    }
}