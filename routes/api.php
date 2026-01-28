<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\admin\DoctorController;
use App\Http\Controllers\admin\AppointmentController;
use App\Http\Controllers\admin\EmergencyTriageController;
use App\Http\Controllers\admin\PatientRecordController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\CommonDataController;
use App\Http\Controllers\Api\DoctorLikeController;
use App\Http\Controllers\Api\VideoCallController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ChatController;


// Public API routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::post('/request-otp', [AdminAuthController::class, 'requestOtp']);
    Route::post('/verify-otp', [AdminAuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AdminAuthController::class, 'resendOtp']);
    // Route::post('/login', [AdminAuthController::class, 'login']);

});

Route::middleware('auth:api')->post(
    '/patient/update-device-token',
    [AdminAuthController::class, 'updateDeviceToken']
);



// Protected API routes (JWT + Admin check)
Route::middleware(['api_auth'])->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::post('/refresh', [AdminAuthController::class, 'refresh']);
        Route::get('/me', [AdminAuthController::class, 'me']);
    });

    Route::prefix('profile')->group(function () {
        Route::post('/update', [ProfileController::class, 'updateProfile']);
        Route::get('/get', [ProfileController::class, 'getProfile']);
        Route::post('/update-password', [ProfileController::class, 'updatePassword']);
        Route::post('/update-profile-image', [ProfileController::class, 'updateProfileImage']);
    });
});

Route::get('/get-specialties', [DoctorController::class, 'getSpecialties']);
Route::prefix('doctors')->group(function () {
    Route::get('/get-doctor', [DoctorController::class, 'getDoctor']); // Get all doctors
    Route::get('/{id}', [DoctorController::class, 'getDoctorById']); // Get single doctor
    Route::post('/save', [DoctorController::class, 'save']); // Create doctor
    Route::post('/{id}', [DoctorController::class, 'updateDoctor']); // Update doctor
    Route::delete('/{id}', [DoctorController::class, 'destroyDoctor']);
});

Route::get('/doctors', [DoctorController::class, 'findDoctors']);
Route::get('/get-resources', [AppointmentController::class, 'getResources']);

Route::prefix('appointments')->group(function () {
    Route::get('/doctor-dates', [AppointmentController::class, 'getDoctorDates']); // Get available dates for doctor
    Route::get('/doctor-slots', [AppointmentController::class, 'getDoctorSlots']); // Get available slots for doctor on date
    Route::get('/', [AppointmentController::class, 'getAllAppointments']); // Get all appointments
    Route::post('/save', [AppointmentController::class, 'createAppointment']); // Create appointment
    Route::get('/{id}', [AppointmentController::class, 'getAppointmentById']); // Get single appointment
    Route::post('/{id}', [AppointmentController::class, 'updateAppointment']); // Update appointment
    Route::delete('/{id}', [AppointmentController::class, 'deleteAppointment']); // Delete appointment
});

// Chat endpoints for patients (auth:api expected to be patient guard)
Route::middleware('api_auth')->prefix('chat')->group(function () {

    Route::post('/conversation/start', [ChatController::class, 'startConversation']);
    // Conversations
    Route::get('/conversations', [ChatController::class, 'listConversations']);
    Route::get('/{conversationId}', [ChatController::class, 'getConversationDetails']);
    Route::post('/{conversationId}/read', [ChatController::class, 'markConversationRead']);
    Route::post('/{conversationId}/rate', [ChatController::class, 'rateConversation']);

    // Messages
    Route::get('/{conversationId}/messages', [ChatController::class, 'getMessages']);
    Route::post('/{conversationId}/message/send', [ChatController::class, 'sendMessage']);
    Route::get('/{conversationId}/messages/{messageId}', [ChatController::class, 'getMessage']);
    Route::patch('/{conversationId}/messages/{messageId}', [ChatController::class, 'updateMessage']);
    Route::delete('/{conversationId}/messages/{messageId}', [ChatController::class, 'deleteMessage']);
    Route::post('/{conversationId}/messages/{messageId}/read', [ChatController::class, 'markMessageRead']);

    // Attachments
    Route::post('/{conversationId}/attachments', [ChatController::class, 'uploadAttachment']);

    Route::get('/quick-replies', [ChatController::class, 'getQuickReplies']);
    Route::post('/{conversationId}/close', [ChatController::class, 'closeConversation']);
    Route::post('/{conversationId}/reopen', [ChatController::class, 'reopenConversation']);
});

// Notifications API for patients (or any authenticated user)
Route::middleware('auth:api')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\Api\NotificationController::class, 'markRead']);
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Api\NotificationController::class, 'markAllRead']);
});

Route::prefix('emergency')->group(function () {
    Route::get('/', [EmergencyTriageController::class, 'getAllemergency']);
    Route::get('/{id}', [EmergencyTriageController::class, 'getemergencyById']);
    Route::post('/save', [EmergencyTriageController::class, 'createemergency']);
    Route::post('/{id}', [EmergencyTriageController::class, 'updateemergency']); // or use PUT method
    Route::delete('/{id}', [EmergencyTriageController::class, 'deleteemergency']);

    // Optional endpoints
    Route::get('/options/triage-levels', [EmergencyTriageController::class, 'getTriageLevels']);
    Route::get('/options/statuses', [EmergencyTriageController::class, 'getStatusOptions']);
});

Route::prefix('medical-reports')->group(function () {

    Route::get('/', [PatientRecordController::class, 'getReports']);

    Route::get('/{id}', [PatientRecordController::class, 'getReportById']);

    Route::get('/{id}/download', [PatientRecordController::class, 'downloadReportPdf']);
});


    // GET endpoints
    Route::get('/services', [CommonDataController::class, 'getServices']);
    Route::get('/services/{id}', [CommonDataController::class, 'getServiceById']);
    Route::get('/events', [CommonDataController::class, 'getEvents']);
    Route::get('/events/{id}', [CommonDataController::class, 'getEventById']);
    Route::get('/health-tips', [CommonDataController::class, 'getHealthTips']);
    Route::get('/banners', [CommonDataController::class, 'getBanners']);
    Route::get('/notifications', [CommonDataController::class, 'getNotifications']);
    Route::get('/prescriptions', [CommonDataController::class, 'getPrescriptions']);


    // POST endpoints with filters/parameters
    Route::post('/services/filter', [CommonDataController::class, 'getServicesWithFilters']);
    Route::post('/events/filter', [CommonDataController::class, 'getEventsWithFilters']);


    Route::prefix('doctors')->group(function () {
        Route::post('/{doctorId}/like', [DoctorLikeController::class, 'toggleLike']);
        Route::get('/{doctorId}/likes', [DoctorLikeController::class, 'getDoctorLikes']);
    });

    Route::prefix('video-call')->group(function () {
        // Patient routes
        Route::get('/active-call', [VideoCallController::class, 'getActiveCall']);
        Route::post('/join', [VideoCallController::class, 'joinCall']);

        Route::post('/start', [VideoCallController::class, 'startCall']);

        Route::post('/end', [VideoCallController::class, 'endCall']);
        Route::get('/status', [VideoCallController::class, 'getCallStatus']);
        Route::get('/history', [VideoCallController::class, 'callHistory']);
    });

    // Patient Appointments with video call status
    Route::get('/patient/appointments', [AppointmentController::class, 'getPatientAppointments']);

    Route::post('/payments/verify', [PaymentController::class, 'verifyPayment']);
    Route::get('/payments/razorpay/{paymentId}', [PaymentController::class, 'getPaymentDetails']);

    // Payments
    // Route::prefix('payments')->group(function () {
    //     Route::post('/initiate', [PaymentController::class, 'initiate']);
    //     Route::post('/confirm', [PaymentController::class, 'confirm']); // webhook from gateway
    //     Route::post('/manual', [PaymentController::class, 'manual']); // admin/staff record
    //     Route::get('/{id}/invoice', [PaymentController::class, 'getInvoice']);
    // });

