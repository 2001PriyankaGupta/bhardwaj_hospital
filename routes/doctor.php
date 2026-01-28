<?php

use App\Http\Controllers\admin\auth\AuthController;
use App\Http\Controllers\admin\DoctorController;
use App\Http\Controllers\admin\ProfileController;
use App\Http\Controllers\admin\AppointmentController;
use App\Http\Controllers\admin\PatientRecordController;
use App\Http\Controllers\admin\PrescriptionController;
use App\Http\Controllers\admin\QueueController;
use App\Http\Controllers\admin\EmergencyTriageController;
use App\Http\Controllers\admin\ScheduleController;

use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'loginSubmit'])->name('login.submit');

Route::middleware(['auth', 'doctor'])->group(function () {

    Route::get('dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-profile', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    Route::resource('appointments', AppointmentController::class);
    Route::get('appointments/slots/available', [AppointmentController::class, 'getAvailableSlots'])->name('appointments.slots.available');
    Route::get('appointments/resources/available', [AppointmentController::class, 'getAvailableResources'])->name('appointments.resources.available');
    Route::post('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status.update');
    Route::get('appointments-calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');

    Route::get('appointments/{id}/start-call', [AppointmentController::class, 'start'])->name('appointments.start');
    Route::get('appointments/{id}/chat', [AppointmentController::class, 'chat'])->name('appointments.chat');
    Route::post('notify-patient', [DoctorController::class, 'notifyPatient'])
    ->name('notify.patient');

    // End an ongoing call (doctor web UI)
    Route::post('appointments/end-call', [AppointmentController::class, 'endCall'])->name('appointments.end-call');


    Route::get('chat', [\App\Http\Controllers\doctor\ChatController::class, 'index'])->name('chat.index');
    Route::post('chat/start', [\App\Http\Controllers\doctor\ChatController::class, 'startConversation'])->name('chat.start');
    Route::get('chat/appointments', [\App\Http\Controllers\doctor\ChatController::class, 'getAppointmentsForChat'])->name('chat.appointments');
    Route::get('chat/{conversationId}', [\App\Http\Controllers\doctor\ChatController::class, 'getConversation'])->name('chat.get');
    Route::get('chat/{conversationId}/messages', [\App\Http\Controllers\doctor\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('chat/send', [\App\Http\Controllers\doctor\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('chat/{conversationId}/message/{messageId}', [\App\Http\Controllers\doctor\ChatController::class, 'getMessage'])->name('chat.message.get');
    Route::put('chat/{conversationId}/message/{messageId}', [\App\Http\Controllers\doctor\ChatController::class, 'updateMessage'])->name('chat.message.update');
    Route::delete('chat/{conversationId}/message/{messageId}', [\App\Http\Controllers\doctor\ChatController::class, 'deleteMessage'])->name('chat.message.delete');
    Route::post('chat/{conversationId}/read', [\App\Http\Controllers\doctor\ChatController::class, 'markConversationRead'])->name('chat.conversation.read');
    Route::post('chat/{conversationId}/message/{messageId}/read', [\App\Http\Controllers\doctor\ChatController::class, 'markMessageRead'])->name('chat.message.read');
    Route::post('chat/{conversationId}/upload', [\App\Http\Controllers\doctor\ChatController::class, 'uploadAttachment'])->name('chat.upload');
    Route::post('chat/{conversationId}/close', [\App\Http\Controllers\doctor\ChatController::class, 'closeConversation'])->name('chat.close');
    Route::post('chat/{conversationId}/reopen', [\App\Http\Controllers\doctor\ChatController::class, 'reopenConversation'])->name('chat.reopen');
    Route::get('chat/{conversationId}/details', [\App\Http\Controllers\doctor\ChatController::class, 'getConversationDetails'])->name('chat.details');

    // Route::get('/appointments', ...)->name('appointments.index');
    // Route::get('/patients', ...)->name('patients.index');

    Route::resource('medical-reports', PatientRecordController::class);
        Route::get('patient/{patientId}/appointments', [PatientRecordController::class, 'getPatientAppointments'])
            ->name('patient.appointments');
        Route::get('medical-reports/{id}/download', [PatientRecordController::class, 'downloadReport'])
            ->name('medical-reports.download');
        Route::get('medical-reports/{id}/print', [PatientRecordController::class, 'printReport'])
            ->name('medical-reports.print');

        // Prescription Routes
        Route::resource('prescriptions', PrescriptionController::class);
        Route::get('prescriptions/{id}/download', [PrescriptionController::class, 'downloadPrescription'])
            ->name('prescriptions.download');
        Route::get('prescriptions/{id}/print', [PrescriptionController::class, 'printPrescription'])
            ->name('prescriptions.print');

    Route::resource('doctors', DoctorController::class);
    Route::get('/{doctor}/leave/create', [DoctorController::class, 'createLeave'])->name('leave.create');
    Route::post('/{doctor}/leave', [DoctorController::class, 'storeLeave'])->name('leave.store');

    // Redirect old singular edit path to the plural path to avoid 404s from legacy links
    Route::get('/{doctor}/leave/{leave}/edit', function (\App\Models\Doctor $doctor, \App\Models\LeaveApplication $leave) {
        return redirect()->route('doctor.leave.edit', ['doctor' => $doctor->id, 'leave' => $leave->id]);
    });

    // Edit / Update leave (doctors can edit pending leaves)
    // Use plural 'leaves' to match other leave routes and avoid confusion
    Route::get('/{doctor}/leaves/{leave}/edit', [DoctorController::class, 'editLeave'])->name('leave.edit');
    Route::put('/{doctor}/leaves/{leave}', [DoctorController::class, 'updateLeave'])->name('leave.update');

    Route::delete('leaves/{leave}', [DoctorController::class, 'destroyLeave'])->name('leaves.destroy');
    Route::get('/{doctor}/leaves/{leave}', [DoctorController::class, 'showLeave'])->name('leaves.show');


    Route::resource('emergency', EmergencyTriageController::class);
    Route::post('emergency/{emergency}/assign-staff', [EmergencyTriageController::class, 'assignStaff'])->name('emergency.assign-staff');
    Route::post('emergency/{emergency}/update-status', [EmergencyTriageController::class, 'updateStatus'])->name('emergency.update-status');
});



    Route::get('doctors/{doctor}/schedules', [ScheduleController::class, 'schedules'])->name('doctors.schedules');
    Route::post('doctors/{doctor}/schedules', [ScheduleController::class, 'storeSchedule'])->name('doctors.schedules.store');
    Route::get('doctors/{doctor}/leaves', [DoctorController::class, 'leaves'])->name('doctors.leaves');
    Route::put('leaves/{leave}/status', [DoctorController::class, 'updateLeaveStatus'])->name('leaves.status.update');
    Route::get('doctors/{doctor}/performance', [DoctorController::class, 'performance'])->name('doctors.performance');
    Route::delete('leaves/{leave}', [DoctorController::class, 'destroyLeave'])->name('leaves.destroy');


    Route::get('date-slots/{dateSlot}/edit', [ScheduleController::class, 'editSchedule'])
            ->name('doctor.date-slots.edit');

    Route::put('date-slots/{id}', [ScheduleController::class, 'updateSchedule'])
            ->name('doctor.date-slots.update');

    Route::delete('date-slots/{id}', [ScheduleController::class, 'deleteSchedule'])
            ->name('doctor.date-slots.delete');




