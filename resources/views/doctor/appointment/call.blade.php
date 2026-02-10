@extends('doctor.layouts.master')

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-color: #3a86ff;
        --danger-color: #ff6b6b;
        --success-color: #4ecdc4;
        --warning-color: #ffd166;
        --dark-color: #2d3436;
        --light-color: #f8f9fa;
        --border-color: #e0e0e0;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    body {
        background-color: #f5f7fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .video-call-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 20px;
        position: relative;
    }

    .call-header {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: var(--shadow);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }



    .call-info h2 {
        color: var(--dark-color);
        margin-bottom: 10px;
        font-weight: 600;
    }

    .call-info h2 i {
        color: var(--primary-color);
        margin-right: 10px;
    }

    .patient-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .patient-avatar {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color), #6c63ff);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .patient-details h3 {
        margin: 0;
        color: var(--dark-color);
        font-weight: 600;
    }

    .call-status {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 5px;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-dot.connecting {
        background-color: var(--warning-color);
        animation: pulse 1.5s infinite;
    }

    .status-dot.connected {
        background-color: var(--success-color);
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1;
        }
    }

    .appointment-details {
        display: flex;
        gap: 15px;
        margin-top: 8px;
        font-size: 14px;
        color: #666;
    }

    .appointment-details i {
        margin-right: 5px;
    }

    .call-timer {
        background: var(--light-color);
        padding: 10px 20px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    .timer-display {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 24px;
        font-weight: 600;
        color: var(--dark-color);
    }

    .timer-display i {
        color: var(--primary-color);
    }

    /* Video Streams */
    .video-streams-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .video-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow);
        display: flex;
        flex-direction: column;
    }

    .video-header {
        background: var(--dark-color);
        color: white;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .video-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .video-header h4 i {
        margin-right: 8px;
    }

    .video-indicators {
        display: flex;
        gap: 10px;
    }

    .indicator {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .mic-on {
        background-color: rgba(78, 205, 196, 0.2);
        color: var(--success-color);
    }

    .video-on {
        background-color: rgba(58, 134, 255, 0.2);
        color: var(--primary-color);
    }

    .video-display {
        width: 100%;
        height: 400px;
        background: #000;
        position: relative;
    }

    .no-video-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #999;
        font-size: 18px;
    }

    .patient-sidebar {
        display: none;
    }

    .no-video-placeholder i {
        font-size: 80px;
        margin-bottom: 15px;
        color: #555;
    }

    .video-label {
        padding: 10px 20px;
        background: var(--light-color);
        border-top: 1px solid var(--border-color);
        font-weight: 500;
        color: var(--dark-color);
    }

    /* Controls Panel */
    .controls-panel {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: var(--shadow);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .control-group {
        display: flex;
        gap: 10px;
    }

    .control-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .control-btn i {
        font-size: 18px;
    }

    .audio-control,
    .video-control {
        background-color: var(--light-color);
        color: var(--dark-color);
        border: 1px solid var(--border-color);
    }

    .audio-control:hover,
    .video-control:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
    }

    .prescription-btn {
        background-color: var(--primary-color);
        color: white;
    }

    .prescription-btn:hover {
        background-color: #2a75ff;
        transform: translateY(-2px);
        color: white;
    }

    .notes-btn,
    .record-btn {
        background-color: var(--light-color);
        color: var(--dark-color);
        border: 1px solid var(--border-color);
    }

    .notes-btn:hover,
    .record-btn:hover {
        background-color: #e9ecef;
    }

    .end-call-btn {
        background-color: var(--danger-color);
        color: white;
        padding: 12px 30px;
    }

    .end-call-btn:hover {
        background-color: #ff5252;
        transform: translateY(-2px);
    }

    /* Patient Sidebar */
    .patient-sidebar {
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow);
        overflow: hidden;
        position: absolute;
        right: 20px;
        top: 180px;
        width: 300px;
    }

    .sidebar-header {
        background: var(--dark-color);
        color: white;
        padding: 15px 20px;
    }

    .sidebar-header h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .sidebar-header h5 i {
        margin-right: 8px;
        color: var(--primary-color);
    }

    .sidebar-content {
        padding: 20px;
        max-height: 500px;
        overflow-y: auto;
    }

    .info-section {
        margin-bottom: 25px;
    }

    .info-section h6 {
        color: var(--dark-color);
        margin-bottom: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #eee;
    }

    .info-label {
        font-weight: 500;
        color: #666;
    }

    .info-value {
        font-weight: 500;
        color: var(--dark-color);
    }

    .medical-history p {
        font-size: 14px;
        line-height: 1.5;
        color: #666;
    }

    .btn-view-history {
        background: none;
        border: none;
        color: var(--primary-color);
        padding: 5px 0;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 5px;
    }

    .btn-view-history:hover {
        text-decoration: underline;
    }

    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 20px;
    }

    .quick-action-btn {
        padding: 10px 15px;
        background: var(--light-color);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        text-align: left;
        font-size: 14px;
        color: var(--dark-color);
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .quick-action-btn:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }

    /* Modal Styles */
    .modal-header {
        background-color: var(--primary-color);
        color: white;
    }

    .modal-title i {
        margin-right: 10px;
    }

    .medication-row {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 10px;
        align-items: center;
    }

    .control-btn {
        transition: all 0.3s ease;
    }

    .control-btn:hover {
        transform: translateY(-2px);
    }

    .control-btn.muted {
        background-color: rgba(220, 53, 69, 0.1);
        border-color: #dc3545;
    }

    @media (max-width: 1400px) {
        .video-streams-container {
            grid-template-columns: 1fr;
        }

        .patient-sidebar {
            position: static;
            width: 100%;
            margin-top: 20px;
        }
    }

    @media (max-width: 992px) {
        .controls-panel {
            flex-direction: column;
            gap: 15px;
        }

        .control-group {
            width: 100%;
            justify-content: center;
        }

        .call-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .patient-info {
            justify-content: center;
        }

        .appointment-details {
            flex-direction: column;
            gap: 5px;
        }
    }

    @media (max-width: 768px) {
        .control-btn {
            padding: 10px 15px;
            font-size: 14px;
        }

        .control-btn span {
            display: none;
        }

        .video-display {
            height: 300px;
        }
    }
</style>
@section('content')
    <div class="video-call-container">
        <!-- Header -->
        <div class="call-header">
            <div class="call-info">
                <h2><i class="fas fa-video"></i> Video Consultation</h2>
                <div class="patient-info">
                    <div class="patient-avatar">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="patient-details">
                        <h3>Calling: {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</h3>
                        <div class="call-status">
                            <span class="status-dot connecting"></span>
                            <span id="callStatusText">Connecting to patient...</span>
                        </div>
                        <div class="appointment-details">
                            <span><i class="fas fa-clock"></i> {{ $appointment->appointment_date->format('M d, Y') }} at
                                {{ $appointment->appointment_time }}</span>
                            <span><i class="fas fa-stethoscope"></i> {{ $appointment->reason }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="call-timer">
                <div class="timer-display">
                    <i class="fas fa-clock"></i>
                    <span id="callTimer">00:00</span>
                </div>
            </div>
        </div>

        <!-- Video Streams Container -->
        <div class="video-streams-container">
            <!-- Patient Video Stream -->
            <div class="video-card remote-video">
                <div class="video-header">
                    <h4><i class="fas fa-user-injured"></i> Patient</h4>
                    <div class="video-indicators">
                        <span class="indicator mic-on"><i class="fas fa-microphone"></i></span>
                        <span class="indicator video-on"><i class="fas fa-video"></i></span>
                    </div>
                </div>
                <div id="remote-player" class="video-display">
                    <div class="no-video-placeholder">
                        <i class="fas fa-user-injured"></i>
                        <p>Waiting for patient to join...</p>
                    </div>
                </div>
                <div class="video-label">
                    <span>{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</span>
                </div>
            </div>

            <!-- Doctor Video Stream -->
            <div class="video-card local-video">
                <div class="video-header">
                    <h4><i class="fas fa-user-md"></i> You</h4>
                    <div class="video-indicators">
                        <span class="indicator mic-on" id="micIndicator"><i class="fas fa-microphone"></i></span>
                        <span class="indicator video-on" id="videoIndicator"><i class="fas fa-video"></i></span>
                    </div>
                </div>
                <div id="local-player" class="video-display">
                    <!-- Local video will appear here -->
                </div>
                <div class="video-label">
                    <span>Dr. {{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>

        <!-- Controls Panel -->
        <div class="controls-panel">
            <!-- Left Controls -->
            <div class="control-group left-controls">
                <button id="muteAudio" class="control-btn audio-control" title="Mute/Unmute Microphone">
                    <i id="audioIcon" class="fas fa-microphone"></i>
                    <span id="audioText">Mute Mic</span>
                </button>

                <button id="muteVideo" class="control-btn video-control" title="Start/Stop Video">
                    <i id="videoIcon" class="fas fa-video"></i>
                    <span id="videoText">Stop Video</span>
                </button>
            </div>

            <!-- Center Controls -->
            <div class="control-group center-controls">
                <button class="control-btn prescription-btn" data-bs-toggle="modal" data-bs-target="#prescriptionModal"
                    title="Write Prescription">
                    <i class="fas fa-prescription"></i>
                    <span>Prescription</span>
                </button>


            </div>

            <!-- Right Controls -->
            <div class="control-group right-controls">
                <button id="endCall" class="control-btn end-call-btn" title="End Call">
                    <i class="fas fa-phone-slash"></i>
                    <span>End Call</span>
                </button>
            </div>
        </div>

        <button id="togglePatientInfo" class="control-btn notes-btn">
            <i class="fas fa-user"></i>
            <span>Patient Info</span>
        </button>

        <!-- Patient Info Sidebar -->
        <div class="patient-sidebar">
            <div class="sidebar-header">
                <h5><i class="fas fa-info-circle"></i> Patient Information</h5>
            </div>

            <div class="sidebar-content">
                <div class="info-section">
                    <h6><i class="fas fa-user"></i> Basic Info</h6>
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $appointment->patient->first_name }}
                            {{ $appointment->patient->last_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $appointment->patient->phone ?? 'Not specified' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Gender:</span>
                        <span class="info-value">{{ $appointment->patient->gender ?? 'NA' }}</span>
                    </div>
                </div>

                <div class="info-section">
                    <h6><i class="fas fa-notes-medical"></i> Consultation Details</h6>

                    <div class="info-item">
                        <span class="info-label">Symptoms:</span>
                        <span class="info-value">{{ $appointment->notes ?? 'Not specified' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Duration:</span>
                        <span class="info-value" id="callDurationDisplay">0 min</span>
                    </div>
                </div>

                <div class="info-section">
                    <h6><i class="fas fa-history"></i> Medical History</h6>
                    <div class="medical-history">
                        @if ($appointment->patient->medical_history)
                            <p>{{ Str::limit($appointment->patient->medical_history, 150) }}</p>
                        @else
                            <p class="text-muted">No medical history recorded</p>
                        @endif

                    </div>
                </div>


            </div>
        </div>
    </div>

    <!-- Prescription Modal -->
    <div class="modal fade" id="prescriptionModal" tabindex="-1" aria-labelledby="prescriptionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="prescriptionModalLabel">
                        <i class="fas fa-prescription"></i> Write Prescription
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="prescriptionForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Patient Name</label>
                                <input type="text" class="form-control"
                                    value="{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}"
                                    readonly>
                                readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <input type="text" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Diagnosis</label>
                            <textarea class="form-control" id="diagnosis" rows="3" placeholder="Enter diagnosis..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Medications</label>
                            <div id="medicationsContainer">
                                <div class="medication-row row mb-2">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" placeholder="Medication name">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" placeholder="Dosage">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" placeholder="Frequency">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="removeMedication(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMedication()">
                                <i class="fas fa-plus"></i> Add Medication
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Instructions</label>
                            <textarea class="form-control" id="instructions" rows="3" placeholder="Enter instructions for the patient..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Follow-up Date</label>
                            <input type="date" class="form-control" id="followUpDate">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="additionalNotes" rows="2" placeholder="Any additional notes..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-outline-primary">
                        <i class="fas fa-save"></i> Save Draft
                    </button>
                    <button type="button" class="btn btn-primary" onclick="submitPrescription()">
                        <i class="fas fa-paper-plane"></i> Send to Patient
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Agora SDK -->
    <script src="https://download.agora.io/sdk/release/AgoraRTC_N.js"></script>
    <script>
        let baseUrl = "{{ config('app.url') }}";
        const client = AgoraRTC.createClient({
            mode: "rtc",
            codec: "vp8"
        });

        const appId = "{{ $appID }}";
        const channel = "{{ $channelName }}";
        const token = "{{ $token }}";
        const uid = {{ $uid }};
        const callId = {{ $videoCall->id }};

        let localTracks = [];
        let audioMuted = false;
        let videoMuted = false;
        let callStartTime;
        let callTimerInterval;

        async function startCall() {
            try {
                await client.join(appId, channel, token, uid);

                localTracks = await AgoraRTC.createMicrophoneAndCameraTracks();
                localTracks[1].play("local-player");
                await client.publish(localTracks);

                startCallTimer();
                updateCallStatus(true);

                // 🔔 Notify Patient
                fetch("{{ route('doctor.notify.patient') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        appointment_id: "{{ $appointment->id }}"
                    })
                });

            } catch (e) {
                console.error(e);
            }
        }

        window.onload = startCall;

        client.on("user-published", async (user, mediaType) => {
            await client.subscribe(user, mediaType);

            if (mediaType === "video") {
                user.videoTrack.play("remote-player");
                document.querySelector('.no-video-placeholder').style.display = 'none';
            }

            if (mediaType === "audio") {
                user.audioTrack.play();
            }
        });

        client.on("user-left", () => {
            document.getElementById("remote-player").innerHTML =
                `<div class="no-video-placeholder">
                <i class="fas fa-user-injured"></i>
                <p>Patient left the call</p>
            </div>`;
        });

        function startCallTimer() {
            callStartTime = new Date();
            callTimerInterval = setInterval(() => {
                const diff = Math.floor((new Date() - callStartTime) / 1000);
                const m = Math.floor(diff / 60);
                const s = diff % 60;
                document.getElementById("callTimer").innerText =
                    `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
                document.getElementById("callDurationDisplay").innerText = `${m} min`;
            }, 1000);
        }

        function updateCallStatus(connected) {
            const dot = document.querySelector('.status-dot');
            const text = document.getElementById('callStatusText');
            dot.className = connected ? 'status-dot connected' : 'status-dot connecting';
            text.innerText = connected ? 'Connected' : 'Connecting...';
        }

        // 🎤 Audio Mute/Unmute
        document.getElementById("muteAudio").onclick = () => {
            audioMuted = !audioMuted;
            localTracks[0].setEnabled(!audioMuted);

            // Update icon and text
            const audioIcon = document.getElementById("audioIcon");
            const audioText = document.getElementById("audioText");

            if (audioMuted) {
                audioIcon.className = "fas fa-microphone-slash";
                audioText.innerText = "Unmute Mic";
                audioIcon.style.color = "#dc3545"; // Red color when muted
            } else {
                audioIcon.className = "fas fa-microphone";
                audioText.innerText = "Mute Mic";
                audioIcon.style.color = ""; // Reset to default
            }
        };

        // 🎥 Video Mute/Unmute
        document.getElementById("muteVideo").onclick = () => {
            videoMuted = !videoMuted;
            localTracks[1].setEnabled(!videoMuted);

            // Update icon and text
            const videoIcon = document.getElementById("videoIcon");
            const videoText = document.getElementById("videoText");

            if (videoMuted) {
                videoIcon.className = "fas fa-video-slash";
                videoText.innerText = "Start Video";
                videoIcon.style.color = "#dc3545"; // Red color when muted
            } else {
                videoIcon.className = "fas fa-video";
                videoText.innerText = "Stop Video";
                videoIcon.style.color = ""; // Reset to default
            }
        };

        // ❌ End Call
        document.getElementById("endCall").onclick = async () => {
            if (!confirm('End the call?')) return;
            // disable to avoid double click
            const endBtn = document.getElementById('endCall');
            endBtn.disabled = true;

            try {
                // Call server to mark call completed and notify the patient
                await fetch("{{ route('doctor.appointments.end-call') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        call_id: callId
                    })
                });
            } catch (e) {
                console.error('Failed to call end endpoint', e);
            }

            // Cleanup local media and leave
            clearInterval(callTimerInterval);
            localTracks.forEach(track => {
                track.stop();
                track.close();
            });
            try {
                await client.leave();
            } catch (e) {
                console.error(e);
            }

            window.location.href = "{{ route('doctor.appointments.index') }}";
        };

        // 👤 Toggle Patient Info
        document.getElementById('togglePatientInfo').onclick = () => {
            const sidebar = document.querySelector('.patient-sidebar');
            sidebar.style.display = sidebar.style.display === 'block' ? 'none' : 'block';
        };
    </script>
@endsection
