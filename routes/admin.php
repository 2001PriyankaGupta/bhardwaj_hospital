<?php

use App\Http\Controllers\admin\auth\AuthController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\ProfileController;
use App\Http\Controllers\admin\BedController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\RateCardController;
use App\Http\Controllers\admin\PackageController;
use App\Http\Controllers\admin\DiscountController;
use App\Http\Controllers\admin\SystemSettingsController;
use App\Http\Controllers\admin\NotificationTemplateController;
use App\Http\Controllers\admin\DepartmentController;
use App\Http\Controllers\admin\DoctorController;
use App\Http\Controllers\admin\AppointmentController;
use App\Http\Controllers\admin\PatientController;
use App\Http\Controllers\admin\StaffController;
use App\Http\Controllers\admin\ShiftController;
use App\Http\Controllers\admin\EmergencyTriageController;
use App\Http\Controllers\admin\InvoiceController;
use App\Http\Controllers\admin\QueueController;
use App\Http\Controllers\admin\EventController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\ScheduleController;
use App\Http\Controllers\admin\BannerController;
use App\Http\Controllers\admin\HealthTipController;
use App\Http\Controllers\admin\PaymentController;


Route::post('login', [AuthController::class, 'loginSubmit'])->name('login.submit');

// ==================== ADMIN ROUTES ====================

Route::middleware(['auth', 'admin'])->group(function () {
    // Admin Dashboard
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('emergency', [AdminController::class, 'emergency'])->name('emergency');

    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-profile', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Beds Management
    Route::prefix('beds')->name('beds.')->group(function () {
        Route::get('/', [BedController::class, 'index'])->name('index');
        Route::get('/data', [BedController::class, 'getBedsData'])->name('data');
        Route::get('/status', [BedController::class, 'getStats'])->name('status');
        Route::post('/', [BedController::class, 'store'])->name('store');
        Route::get('/{id}', [BedController::class, 'show'])->name('show');
        Route::put('/{id}', [BedController::class, 'update'])->name('update');
        Route::delete('/{id}', [BedController::class, 'destroy'])->name('destroy');
    });

    Route::resource('rooms', \App\Http\Controllers\admin\RoomController::class);
    Route::resource('room-types', \App\Http\Controllers\admin\RoomTypeController::class);

    // Services Routes
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{id}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::get('/services/{id}/edit', [ServiceController::class, 'edit'])->name('services.edit');

    // Rate Cards Routes
    Route::get('/rate-cards', [RateCardController::class, 'index'])->name('rate-cards.index');
    Route::post('/rate-cards', [RateCardController::class, 'store'])->name('rate-cards.store');
    Route::put('/rate-cards/{id}', [RateCardController::class, 'update'])->name('rate-cards.update');
    Route::delete('/rate-cards/{id}', [RateCardController::class, 'destroy'])->name('rate-cards.destroy');

    // Packages Routes
    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::put('/packages/{id}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{id}', [PackageController::class, 'destroy'])->name('packages.destroy');

    // Discounts Routes
    Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
    Route::post('/discounts', [DiscountController::class, 'store'])->name('discounts.store');
    Route::put('/discounts/{id}', [DiscountController::class, 'update'])->name('discounts.update');
    Route::delete('/discounts/{id}', [DiscountController::class, 'destroy'])->name('discounts.destroy');

    // System Settings
    Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/update', [SystemSettingsController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/backup/create', [SystemSettingsController::class, 'createBackup'])->name('settings.backup.create');
    Route::post('/settings/backup/restore/{id}', [SystemSettingsController::class, 'restoreBackup'])->name('settings.backup.restore');
    Route::post('/settings/user/update/{id}', [SystemSettingsController::class, 'updateUser'])->name('settings.user.update');
    Route::post('/settings/logs/clear', [SystemSettingsController::class, 'clearLogs'])->name('settings.logs.clear');

    // Notifications
    Route::resource('notifications', NotificationTemplateController::class);
    Route::get('notifications/{notification}/schedule', [NotificationTemplateController::class, 'showScheduleForm'])->name('notifications.schedule');
    Route::post('notifications/{notification}/schedule', [NotificationTemplateController::class, 'scheduleMessage'])->name('notifications.schedule.store');
    Route::post('scheduled-messages/{scheduledMessage}/cancel', [NotificationTemplateController::class, 'cancelScheduledMessage'])->name('scheduled-messages.cancel');

    // Departments
    Route::resource('departments', DepartmentController::class);
    Route::post('departments/{department}/services', [DepartmentController::class, 'updateServices'])->name('departments.update-services');
    Route::post('departments/update-hierarchy', [DepartmentController::class, 'updateHierarchy'])->name('departments.update-hierarchy');
    Route::get('departments/hierarchy-tree', [DepartmentController::class, 'hierarchyTree'])->name('departments.hierarchy-tree');

    // Doctors Management
    Route::resource('doctors', DoctorController::class);
 
    Route::get('doctors/{doctor}/leaves', [DoctorController::class, 'leaves'])->name('doctors.leaves');
    Route::put('leaves/{leave}/status', [DoctorController::class, 'updateLeaveStatus'])->name('leaves.status.update');
    Route::get('doctors/{doctor}/performance', [DoctorController::class, 'performance'])->name('doctors.performance');
    Route::delete('leaves/{leave}', [DoctorController::class, 'destroyLeave'])->name('leaves.destroy');

    // Appointment Routes
    Route::get('appointments/doctor-dates', [AppointmentController::class, 'getDoctorDates'])->name('appointments.doctor-dates');
    Route::get('appointments/doctor-slots', [AppointmentController::class, 'getDoctorSlots'])->name('appointments.doctor-slots');
    Route::get('appointments/slots/available', [AppointmentController::class, 'getAvailableSlots'])->name('appointments.slots.available');
    Route::get('appointments/resources/available', [AppointmentController::class, 'getAvailableResources'])->name('appointments.resources.available');
    Route::post('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status.update');
    Route::get('appointments-calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::resource('appointments', AppointmentController::class);

    // Patients
    Route::prefix('patients')->group(function () {
        Route::get('/', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/{patient}', [PatientController::class, 'show'])->name('patients.show');
        Route::delete('/{patient}', [PatientController::class, 'destroy'])->name('destroy');
        Route::get('/{patient}/medical-records', [PatientController::class, 'medicalRecords'])->name('patients.medical-records');
        Route::post('/{patient}/medical-records', [PatientController::class, 'storeMedicalRecord'])->name('patients.store-medical-record');
        Route::put('/{id}/medical-records', [PatientController::class, 'updateMedicalRecord'])->name('patients.update-medical-record');
        Route::delete('/{id}/medical-records', [PatientController::class, 'deleteMedicalRecord'])->name('patients.delete-medical-record');
        Route::get('/medical-records/{record}/edit', [PatientController::class, 'editMedicalRecord'])->name('patients.edit-medical-record');
        Route::get('/{patient}/appointment-history', [PatientController::class, 'appointmentHistory'])->name('patients.appointment-history');
        Route::get('/{patient}/communication-logs', [PatientController::class, 'communicationLogs'])->name('patients.communication-logs');
        Route::post('/{patient}/communication-logs', [PatientController::class, 'storeCommunicationLog'])->name('patients.store-communication-log');
        Route::get('/{patient}/analytics', [PatientController::class, 'analytics'])->name('patients.analytics');

         Route::get('/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/{patient}', [PatientController::class, 'update'])->name('patients.update');
    });


    // Health Tips Routes
    Route::prefix('healthtips')->name('healthtips.')->group(function () {
        Route::get('/', [HealthTipController::class, 'index'])->name('index');
        Route::get('/create', [HealthTipController::class, 'create'])->name('create');
        Route::post('/', [HealthTipController::class, 'store'])->name('store');
        Route::get('/{health_tip}', [HealthTipController::class, 'edit'])->name('edit');
        Route::put('/{health_tip}', [HealthTipController::class, 'update'])->name('update');
        Route::delete('/{health_tip}', [HealthTipController::class, 'destroy'])->name('destroy');
    });

     Route::prefix('banner')->name('banner.')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('index');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::post('/', [BannerController::class, 'store'])->name('store');
        Route::get('/{banner}', [BannerController::class, 'show'])->name('show');
        Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
        Route::put('/{banner}', [BannerController::class, 'update'])->name('update');
        Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('destroy');
        Route::post('/{banner}/update-status', [BannerController::class, 'updateStatus'])->name('update-status');
    });

    // Staff Management
    Route::resource('staff', StaffController::class);
    Route::get('/staff/{id}/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
    Route::put('/shifts/{shift}', [ShiftController::class, 'update'])->name('shifts.update');
    Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

    // Emergency
    Route::resource('emergency', EmergencyTriageController::class);
    Route::post('emergency/{emergency}/assign-staff', [EmergencyTriageController::class, 'assignStaff'])->name('emergency.assign-staff');
    Route::post('emergency/{emergency}/update-status', [EmergencyTriageController::class, 'updateStatus'])->name('emergency.update-status');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'addPayment'])->name('invoices.payments.store');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');


    // Events
    Route::resource('events', EventController::class);
    Route::post('events/{event}/update-status', [EventController::class, 'updateStatus'])->name('events.update-status');
    Route::post('events/{event}/toggle-feature', [EventController::class, 'toggleFeature'])->name('events.toggle-feature');
    Route::post('events/{event}/toggle-publish', [EventController::class, 'togglePublish'])->name('events.toggle-publish');

 Route::get('/queue', [QueueController::class, 'index'])->name('queue.index');
    Route::get('/queue/create', [QueueController::class, 'create'])->name('queue.create');
    Route::post('/queue', [QueueController::class, 'store'])->name('queue.store');

 // Queue Management
   Route::prefix('queue')->name('queue.')->group(function () 
   {
        Route::get('/', [QueueController::class, 'index'])->name('index');
        Route::get('/dashboard', [QueueController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [QueueController::class, 'create'])->name('create');
        Route::post('/', [QueueController::class, 'store'])->name('store');
        Route::get('/{queue}', [QueueController::class, 'show'])->name('show');
        Route::get('/{queue}/edit', [QueueController::class, 'edit'])->name('edit');
        Route::put('/{queue}', [QueueController::class, 'update'])->name('update');
        Route::delete('/{queue}', [QueueController::class, 'destroy'])->name('destroy');

        // Custom actions
        Route::post('/call-next/{doctorId}', [QueueController::class, 'callNext'])->name('callNext');
        Route::patch('/complete/{queue}', [QueueController::class, 'complete'])->name('complete');
        Route::get('/doctor-queues/{doctorId}', [QueueController::class, 'getDoctorQueues'])->name('getDoctorQueues');

       
          
        Route::post('/update-appointment-status/{id}', [QueueController::class, 'updateAppointmentStatus'])
        ->name('updateAppointmentStatus');
        
        Route::get('/doctor-appointments/{doctorId}', [QueueController::class, 'getDoctorAppointments'])
            ->name('getDoctorAppointments');
    });

    

    

    Route::resource('roles', RoleController::class);

    // Add this route for fetching role permissions
    Route::get('roles/{role}/permissions', function (App\Models\Role $role) {
        return response()->json([
            'permissions' => $role->permissions->pluck('id')
        ]);
    })->name('roles.permissions');

    // Route::middleware(['permission:view_dashboard'])->group(function () {
    //     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // });
    // Route::prefix('management')->middleware(['permission:view_management'])->group(function () {
    // });

     // DateSlot specific routes
//    Route::prefix('doctor')->group(function () {
    
//         Route::get('/date-slots/{dateSlot}/edit', [ScheduleController::class, 'edit']);
//         Route::put('/date-slots/{dateSlot}', [ScheduleController::class, 'updateSchedule'])
//             ->name('doctor.date-slots.update');
//         Route::delete('/date-slots/{dateSlot}', [ScheduleController::class, 'deleteSchedule'])
//             ->name('doctor.date-slots.delete');
//     });
       Route::get('doctors/{doctor}/schedules', [ScheduleController::class, 'schedules'])->name('doctors.schedules');
    Route::post('doctors/{doctor}/schedules', [ScheduleController::class, 'storeSchedule'])->name('doctors.schedules.store');


    // Payment Transactions
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
    });

    Route::post('payments/{id}/mark-paid', [PaymentController::class, 'markAsPaid'])
        ->name('payments.mark-paid');
});

