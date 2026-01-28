@extends('doctor.layouts.master')

@section('title', 'Queue Management')

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .priority-emergency {
        border-left: 4px solid #dc2626;
    }

    .priority-high {
        border-left: 4px solid #f59e0b;
    }

    .priority-normal {
        border-left: 4px solid #10b981;
    }

    .priority-follow-up {
        border-left: 4px solid #dc2626;
    }

    .status-waiting {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-called {
        background-color: #dbeafe;
        color: #dc2626;
    }

    .status-in-consultation {
        background-color: #dbeafe;
        color: #dc2626;
    }

    .status-completed {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-skipped {
        background-color: #f3f4f6;
        color: #374151;
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }

        100% {
            opacity: 1;
        }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

@section('content')


    <body class="bg-gray-50 mt-4">
        <!-- Navigation -->
        <nav class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-blue-600">MedCare</h1>
                        <span class="ml-4 text-gray-500">Queue View</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Dr. {{ auth()->user()->name }}</span>

                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Patient Queue</h1>
                            <p class="text-gray-600 mt-2">Manage your patient queue in real-time</p>
                        </div>
                        <div class="flex space-x-3" id="availabilityControls">
                            <!-- Availability controls will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Patients Today -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="rounded-full bg-blue-100 p-3 mr-4">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Today</p>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total_today'] }}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Waiting Patients -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="rounded-full bg-yellow-100 p-3 mr-4">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Waiting</p>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['waiting'] }}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- In Progress -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="rounded-full bg-blue-100 p-3 mr-4">
                                <i class="fas fa-user-md text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">In Progress</p>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['in_progress'] }}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Today -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="rounded-full bg-green-100 p-3 mr-4">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Completed Today</p>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Patient Section -->
                <div class="mb-8">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">
                                <i class="fas fa-user-md mr-2"></i> Current Patient
                            </h2>
                        </div>

                        <div id="currentPatientSection">
                            @if ($currentPatient)
                                <div class="p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="rounded-full bg-blue-100 p-4 mr-4">
                                                <i class="fas fa-user text-blue-600 text-2xl"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900">
                                                    {{ $currentPatient->patient->name }}</h3>
                                                <p class="text-gray-600">
                                                    {{ $currentPatient->patient->age }} years •
                                                    {{ $currentPatient->patient->gender }}
                                                    @if ($currentPatient->chief_complaint)
                                                        • {{ $currentPatient->chief_complaint }}
                                                    @endif
                                                </p>
                                                <div class="flex items-center mt-2">
                                                    <span
                                                        class="px-3 py-1 rounded-full text-sm font-medium status-{{ $currentPatient->status }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ ucfirst(str_replace('_', ' ', $currentPatient->status)) }}
                                                    </span>
                                                    @if ($currentPatient->consultation_started_at)
                                                        <span class="ml-4 text-gray-600">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            Duration:
                                                            {{ now()->diffInMinutes($currentPatient->consultation_started_at) }}
                                                            min
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-x-3">
                                            @if ($currentPatient->status === 'called')
                                                <button onclick="startConsultation({{ $currentPatient->id }})"
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                                    <i class="fas fa-play mr-2"></i> Start Consultation
                                                </button>
                                            @endif

                                            @if ($currentPatient->status === 'in_consultation')
                                                <button onclick="completeConsultation({{ $currentPatient->id }})"
                                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                                    <i class="fas fa-check mr-2"></i> Complete
                                                </button>

                                                <a href="{{ route('doctor.consultation.show', $currentPatient->patient_id) }}"
                                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                                    <i class="fas fa-file-medical mr-2"></i> Consultation
                                                </a>
                                            @endif

                                            <button onclick="skipPatient({{ $currentPatient->id }})"
                                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                                <i class="fas fa-forward mr-2"></i> Skip
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="p-8 text-center">
                                    <i class="fas fa-user-md text-gray-300 text-5xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No current patient</h3>
                                    <p class="text-gray-500">You're not currently consulting any patient.</p>
                                    <button onclick="callNextPatient()"
                                        class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        <i class="fas fa-bell mr-2"></i> Call Next Patient
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Waiting Patients -->
                <div class="mb-8">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="border-b">
                            <div class="flex justify-between items-center px-6 py-4">
                                <h2 class="text-xl font-bold text-gray-900">
                                    <i class="fas fa-clock mr-2"></i> Waiting Patients
                                    <span class="ml-2 bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded">
                                        {{ count($waitingPatients) }}
                                    </span>
                                </h2>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-sync-alt mr-1 cursor-pointer" onclick="loadQueueData()"></i>
                                    Auto-refresh: <span id="refreshTimer">30</span>s
                                </div>
                            </div>
                        </div>

                        <div id="waitingPatientsSection">
                            @if (count($waitingPatients) > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Patient
                                                </th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Details
                                                </th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Waiting Time
                                                </th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Priority
                                                </th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($waitingPatients as $patient)
                                                <tr class="hover:bg-gray-50 fade-in priority-{{ $patient->priority }}">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div
                                                                class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                                <i class="fas fa-user text-blue-600"></i>
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm font-medium text-gray-900">
                                                                    {{ $patient->patient->name }}
                                                                    @if ($patient->is_emergency)
                                                                        <span
                                                                            class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">
                                                                            <i class="fas fa-exclamation-triangle"></i>
                                                                            Emergency
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="text-sm text-gray-500">
                                                                    {{ $patient->patient->age }}y •
                                                                    {{ $patient->patient->gender }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm text-gray-900">
                                                            @if ($patient->chief_complaint)
                                                                {{ Str::limit($patient->chief_complaint, 50) }}
                                                            @else
                                                                <span class="text-gray-400">No complaint specified</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            Arrived: {{ $patient->created_at->format('h:i A') }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $patient->waiting_time }} min
                                                        </div>
                                                        @if ($patient->waiting_time > 30)
                                                            <div class="text-xs text-red-600">
                                                                <i class="fas fa-exclamation-circle"></i> Long wait
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span
                                                            class="px-3 py-1 rounded-full text-xs font-medium 
                                                        @if ($patient->priority == 'emergency') bg-red-100 text-red-800
                                                        @elseif($patient->priority == 'high') bg-yellow-100 text-yellow-800
                                                        @elseif($patient->priority == 'normal') bg-green-100 text-green-800
                                                        @else bg-blue-100 text-blue-800 @endif">
                                                            {{ ucfirst(str_replace('_', ' ', $patient->priority)) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <button onclick="callPatient({{ $patient->id }})"
                                                            class="text-blue-600 hover:text-blue-900 mr-4">
                                                            <i class="fas fa-bell mr-1"></i> Call
                                                        </button>
                                                        <button onclick="viewPatientDetails({{ $patient->id }})"
                                                            class="text-green-600 hover:text-green-900 mr-4">
                                                            <i class="fas fa-eye mr-1"></i> View
                                                        </button>
                                                        <button onclick="skipPatient({{ $patient->id }})"
                                                            class="text-gray-600 hover:text-gray-900">
                                                            <i class="fas fa-forward mr-1"></i> Skip
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No patients waiting</h3>
                                    <p class="text-gray-500">All patients have been attended to.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Completed Today -->
                @if (count($completedToday) > 0)
                    <div>
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="border-b">
                                <div class="px-6 py-4">
                                    <h2 class="text-xl font-bold text-gray-900">
                                        <i class="fas fa-check-circle mr-2 text-green-600"></i> Completed Today
                                    </h2>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Patient
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Consultation Time
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Waiting Time
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Completed At
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($completedToday as $patient)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div
                                                            class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-user text-green-600"></i>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $patient->patient->name }}</div>
                                                            <div class="text-sm text-gray-500">
                                                                {{ $patient->patient->age }}y</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">
                                                        {{ $patient->consultation_duration }} minutes</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $patient->waiting_time }}
                                                        minutes</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">
                                                        {{ $patient->consultation_ended_at->format('h:i A') }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>



    @endsection
    @section('scripts')
        <!-- JavaScript -->
        <script>
            let refreshInterval = 30000; // 30 seconds
            let refreshTimer = 30;
            let timerInterval;

            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                loadAvailabilityControls();
                startAutoRefresh();
                updateRefreshTimer();
            });

            // Load availability controls
            async function loadAvailabilityControls() {
                try {
                    const response = await fetch('{{ route('doctor.queue.stats') }}');
                    const data = await response.json();

                    if (data.success) {
                        const controls = `
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Status:</span>
                            <div class="relative">
                                <select id="availabilityStatus" onchange="updateAvailability()" 
                                        class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="available" ${'{{ $availability->status }}' === 'available' ? 'selected' : ''}>
                                        Available
                                    </option>
                                    <option value="busy" ${'{{ $availability->status }}' === 'busy' ? 'selected' : ''}>
                                        Busy
                                    </option>
                                    <option value="break" ${'{{ $availability->status }}' === 'break' ? 'selected' : ''}>
                                        On Break
                                    </option>
                                    <option value="unavailable" ${'{{ $availability->status }}' === 'unavailable' ? 'selected' : ''}>
                                        Unavailable
                                    </option>
                                </select>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="acceptingPatients" 
                                       ${'{{ $availability->is_accepting_patients }}' ? 'checked' : ''}
                                       onchange="updateAvailability()"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="acceptingPatients" class="ml-2 text-sm text-gray-700">
                                    Accepting Patients
                                </label>
                            </div>
                        </div>
                    `;

                        document.getElementById('availabilityControls').innerHTML = controls;
                    }
                } catch (error) {
                    console.error('Error loading availability controls:', error);
                }
            }

            // Update doctor availability
            async function updateAvailability() {
                const status = document.getElementById('availabilityStatus').value;
                const acceptingPatients = document.getElementById('acceptingPatients').checked;

                try {
                    const response = await fetch('{{ route('doctor.queue.availability') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: status,
                            is_accepting_patients: acceptingPatients
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast('Availability updated successfully', 'success');
                    } else {
                        showToast('Failed to update availability', 'error');
                    }
                } catch (error) {
                    console.error('Error updating availability:', error);
                    showToast('Error updating availability', 'error');
                }
            }

            // Call patient
            async function callPatient(queueId) {
                try {
                    const response = await fetch(`/doctor/queue/${queueId}/call`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast('Patient called successfully', 'success');
                        loadQueueData();
                    } else {
                        showToast(data.message || 'Failed to call patient', 'error');
                    }
                } catch (error) {
                    console.error('Error calling patient:', error);
                    showToast('Error calling patient', 'error');
                }
            }

            // Call next patient
            async function callNextPatient() {
                try {
                    const response = await fetch('{{ route('doctor.queue.data') }}');
                    const data = await response.json();

                    if (data.success && data.waiting_patients.length > 0) {
                        const nextPatient = data.waiting_patients[0];
                        await callPatient(nextPatient.id);
                    } else {
                        showToast('No patients waiting', 'info');
                    }
                } catch (error) {
                    console.error('Error calling next patient:', error);
                }
            }

            // Start consultation
            async function startConsultation(queueId) {
                try {
                    const response = await fetch(`/doctor/queue/${queueId}/start`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast('Consultation started', 'success');

                        // Redirect to consultation page if URL provided
                        if (data.redirect_url) {
                            setTimeout(() => {
                                window.location.href = data.redirect_url;
                            }, 1000);
                        } else {
                            loadQueueData();
                        }
                    } else {
                        showToast(data.message || 'Failed to start consultation', 'error');
                    }
                } catch (error) {
                    console.error('Error starting consultation:', error);
                    showToast('Error starting consultation', 'error');
                }
            }

            // Complete consultation
            async function completeConsultation(queueId) {
                Swal.fire({
                    title: 'Complete Consultation',
                    text: 'Are you sure you want to mark this consultation as complete?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, complete',
                    cancelButtonText: 'Cancel'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/doctor/queue/${queueId}/complete`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                showToast('Consultation completed successfully', 'success');
                                loadQueueData();
                            } else {
                                showToast(data.message || 'Failed to complete consultation', 'error');
                            }
                        } catch (error) {
                            console.error('Error completing consultation:', error);
                            showToast('Error completing consultation', 'error');
                        }
                    }
                });
            }

            // Skip patient
            async function skipPatient(queueId) {
                const {
                    value: reason
                } = await Swal.fire({
                    title: 'Skip Patient',
                    input: 'text',
                    inputLabel: 'Reason for skipping (optional)',
                    inputPlaceholder: 'Enter reason...',
                    showCancelButton: true,
                    confirmButtonColor: '#6b7280',
                    cancelButtonColor: '#dc2626',
                    confirmButtonText: 'Skip Patient',
                    cancelButtonText: 'Cancel'
                });

                if (reason !== undefined) {
                    try {
                        const response = await fetch(`/doctor/queue/${queueId}/skip`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                reason: reason
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            showToast('Patient skipped', 'info');
                            loadQueueData();
                        } else {
                            showToast(data.message || 'Failed to skip patient', 'error');
                        }
                    } catch (error) {
                        console.error('Error skipping patient:', error);
                        showToast('Error skipping patient', 'error');
                    }
                }
            }

            // View patient details
            function viewPatientDetails(queueId) {
                // You can implement a modal or redirect to patient details page
                Swal.fire({
                    title: 'Patient Details',
                    text: 'This would show detailed patient information',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            }

            // Load queue data
            async function loadQueueData() {
                try {
                    const response = await fetch('{{ route('doctor.queue.data') }}');
                    const data = await response.json();

                    if (data.success) {
                        updateCurrentPatient(data.current_patient);
                        updateWaitingPatients(data.waiting_patients);
                        resetRefreshTimer();
                    }
                } catch (error) {
                    console.error('Error loading queue data:', error);
                }
            }

            // Update current patient section
            function updateCurrentPatient(currentPatient) {
                const section = document.getElementById('currentPatientSection');

                if (!currentPatient) {
                    section.innerHTML = `
                    <div class="p-8 text-center">
                        <i class="fas fa-user-md text-gray-300 text-5xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No current patient</h3>
                        <p class="text-gray-500">You're not currently consulting any patient.</p>
                        <button onclick="callNextPatient()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-bell mr-2"></i> Call Next Patient
                        </button>
                    </div>
                `;
                    return;
                }

                const statusText = currentPatient.status.replace('_', ' ');
                const statusClass = currentPatient.status === 'in_consultation' ? 'bg-blue-100 text-blue-800' :
                    currentPatient.status === 'called' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800';

                let actionButtons = '';
                if (currentPatient.status === 'called') {
                    actionButtons = `
                    <button onclick="startConsultation(${currentPatient.id})" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-play mr-2"></i> Start Consultation
                    </button>
                `;
                } else if (currentPatient.status === 'in_consultation') {
                    actionButtons = `
                    <button onclick="completeConsultation(${currentPatient.id})" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-check mr-2"></i> Complete
                    </button>
                `;
                }

                actionButtons += `
                <button onclick="skipPatient(${currentPatient.id})" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-forward mr-2"></i> Skip
                </button>
            `;

                section.innerHTML = `
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="rounded-full bg-blue-100 p-4 mr-4">
                                <i class="fas fa-user text-blue-600 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">${currentPatient.patient_name}</h3>
                                <p class="text-gray-600">
                                    ${currentPatient.patient_age} years • ${currentPatient.patient_gender}
                                </p>
                                <div class="flex items-center mt-2">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium ${statusClass}">
                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                        ${statusText.charAt(0).toUpperCase() + statusText.slice(1)}
                                    </span>
                                    ${currentPatient.consultation_started_at ? `
                                                                                                                <span class="ml-4 text-gray-600">
                                                                                                                    <i class="fas fa-clock mr-1"></i>
                                                                                                                    Duration: ${currentPatient.consultation_duration} min
                                                                                                                </span>
                                                                                                            ` : ''}
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-x-3">
                            ${actionButtons}
                        </div>
                    </div>
                </div>
            `;
            }

            // Update waiting patients section
            function updateWaitingPatients(patients) {
                const section = document.getElementById('waitingPatientsSection');

                if (patients.length === 0) {
                    section.innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No patients waiting</h3>
                        <p class="text-gray-500">All patients have been attended to.</p>
                    </div>
                `;
                    return;
                }

                let rows = '';
                patients.forEach(patient => {
                    const priorityClass = patient.priority === 'emergency' ? 'bg-red-100 text-red-800' :
                        patient.priority === 'high' ? 'bg-yellow-100 text-yellow-800' :
                        patient.priority === 'normal' ? 'bg-green-100 text-green-800' :
                        'bg-blue-100 text-blue-800';

                    const emergencyBadge = patient.is_emergency ?
                        `<span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">
                        <i class="fas fa-exclamation-triangle"></i> Emergency
                    </span>` : '';

                    const longWaitWarning = patient.waiting_time > 30 ?
                        `<div class="text-xs text-red-600">
                        <i class="fas fa-exclamation-circle"></i> Long wait
                    </div>` : '';

                    rows += `
                    <tr class="hover:bg-gray-50 fade-in priority-${patient.priority}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        ${patient.patient_name}
                                        ${emergencyBadge}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        ${patient.patient_age}y • ${patient.patient_gender}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                ${patient.chief_complaint || '<span class="text-gray-400">No complaint specified</span>'}
                            </div>
                            <div class="text-sm text-gray-500">
                                Arrived: ${patient.created_at}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                ${patient.waiting_time} min
                            </div>
                            ${longWaitWarning}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${priorityClass}">
                                ${patient.priority.charAt(0).toUpperCase() + patient.priority.slice(1)}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="callPatient(${patient.id})" 
                                    class="text-blue-600 hover:text-blue-900 mr-4">
                                <i class="fas fa-bell mr-1"></i> Call
                            </button>
                            <button onclick="viewPatientDetails(${patient.id})" 
                                    class="text-green-600 hover:text-green-900 mr-4">
                                <i class="fas fa-eye mr-1"></i> View
                            </button>
                            <button onclick="skipPatient(${patient.id})" 
                                    class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-forward mr-1"></i> Skip
                            </button>
                        </td>
                    </tr>
                `;
                });

                section.innerHTML = `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Patient
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Waiting Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Priority
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${rows}
                        </tbody>
                    </table>
                </div>
            `;
            }

            // Auto-refresh functionality
            function startAutoRefresh() {
                setInterval(loadQueueData, refreshInterval);
            }

            function updateRefreshTimer() {
                timerInterval = setInterval(() => {
                    refreshTimer--;
                    if (refreshTimer <= 0) {
                        refreshTimer = 30;
                    }
                    document.getElementById('refreshTimer').textContent = refreshTimer;
                }, 1000);
            }

            function resetRefreshTimer() {
                refreshTimer = 30;
                document.getElementById('refreshTimer').textContent = refreshTimer;
            }

            // Utility functions
            function showToast(message, type = 'success') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: type,
                    title: message
                });
            }

            // Play notification sound
            function playNotificationSound() {
                const audio = new Audio('/notification.mp3'); // Add your notification sound file
                audio.play().catch(e => console.log('Audio play failed:', e));
            }

            // Check for new emergency patients
            async function checkForEmergencies() {
                try {
                    const response = await fetch('{{ route('doctor.queue.data') }}');
                    const data = await response.json();

                    if (data.success && data.waiting_patients) {
                        const emergencies = data.waiting_patients.filter(p => p.is_emergency);

                        if (emergencies.length > 0) {
                            // Play sound for emergencies
                            playNotificationSound();

                            // Show emergency alert
                            if (!document.getElementById('emergencyAlert')) {
                                const alert = document.createElement('div');
                                alert.id = 'emergencyAlert';
                                alert.className =
                                    'fixed top-4 right-4 bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                                alert.innerHTML = `
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span>Emergency patient waiting!</span>
                                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            `;
                                document.body.appendChild(alert);

                                // Auto-remove after 10 seconds
                                setTimeout(() => {
                                    if (alert.parentElement) {
                                        alert.remove();
                                    }
                                }, 10000);
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error checking for emergencies:', error);
                }
            }

            // Check for emergencies every 30 seconds
            setInterval(checkForEmergencies, 30000);
        </script>

    @endsection
