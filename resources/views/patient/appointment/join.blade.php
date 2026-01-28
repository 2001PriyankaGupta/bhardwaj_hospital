<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <div class="container" style="max-width:900px;margin:40px auto;">
    <h3>Patient: Join Video Call</h3>

    <div class="mb-3">
        <label class="form-label">Call ID (optional)</label>
        <input id="callIdInput" class="form-control" value="{{ $call_id ?? '' }}" placeholder="Enter call_id or leave blank to use active call check" />
    </div>

    <div class="mb-3">
        <label class="form-label">Patient JWT (paste a valid API token)</label>
        <input id="jwtInput" class="form-control" placeholder="Paste patient JWT here for API auth" />
        <div class="form-text">You can store token in localStorage for convenience.</div>
    </div>

    <div class="mb-3">
        <button id="saveToken" class="btn btn-secondary">Save Token</button>
        <button id="clearToken" class="btn btn-link">Clear Token</button>
    </div>

    <div class="mb-3">
        <button id="fetchActiveCall" class="btn btn-primary">Get Active Call (by appointment)</button>
        <button id="joinCall" class="btn btn-success">Join Call</button>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <h5>Your Video</h5>
            <div id="local-player" style="background:#000;height:360px"></div>
        </div>
        <div class="col-md-6">
            <h5>Remote Video</h5>
            <div id="remote-player" style="background:#000;height:360px"></div>
        </div>
    </div>

    <div class="mt-3">
        <button id="leaveBtn" class="btn btn-danger">Leave Call</button>
    </div>

    <div id="log" style="margin-top:20px;color:#666;"></div>
</div>

<!-- Agora SDK -->
<script src="https://download.agora.io/sdk/release/AgoraRTC_N.js"></script>

<script>
    const tokenStorageKey = 'patient_jwt_token';

    document.getElementById('saveToken').onclick = () => {
        const t = document.getElementById('jwtInput').value.trim();
        if (!t) return alert('Paste a JWT token first');
        localStorage.setItem(tokenStorageKey, t);
        log('Token saved to localStorage');
    };

    document.getElementById('clearToken').onclick = () => {
        localStorage.removeItem(tokenStorageKey);
        document.getElementById('jwtInput').value = '';
        log('Token cleared');
    };

    // Prefill input from storage
    const stored = localStorage.getItem(tokenStorageKey);
    if (stored) document.getElementById('jwtInput').value = stored;

    function log(msg){
        const el = document.getElementById('log');
        el.innerText = (new Date()).toLocaleTimeString() + ' - ' + msg + '\n' + el.innerText;
    }

    let client = null;
    let localTracks = [];

    async function getActiveCallByAppointment(appointmentId){
        const jwt = localStorage.getItem(tokenStorageKey);
        if (!jwt) return alert('Save patient JWT first');

        const url = '/api/video-call/active-call?appointment_id=' + encodeURIComponent(appointmentId);
        const res = await fetch(url, { headers: { 'Authorization': 'Bearer ' + jwt } });
        const data = await res.json();
        if (!data.status) {
            log('No active call: ' + (data.message||''));
            return null;
        }
        log('Active call found');
        return data.data; // contains channel_name, token, etc
    }

    async function joinCallApi(callId){
        const jwt = localStorage.getItem(tokenStorageKey);
        if (!jwt) return alert('Save patient JWT first');

        const res = await fetch('/api/video-call/join', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + jwt
            },
            body: JSON.stringify({ call_id: callId })
        });

        const json = await res.json();
        if (!json.status) {
            log('Join API error: ' + (json.message||''));
            alert('Join failed: ' + (json.message||''));
            return null;
        }
        log('Join API success');
        return json.data; // channel_name, token, uid, app_id
    }

    document.getElementById('fetchActiveCall').onclick = async () => {
        const callIdOrAppointment = document.getElementById('callIdInput').value.trim();
        if (!callIdOrAppointment) return alert('Provide appointment_id in the Call ID field for active-call lookup');
        const details = await getActiveCallByAppointment(callIdOrAppointment);
        if (details) {
            document.getElementById('callIdInput').value = details.call_id || '';
            log('Active call id: ' + (details.call_id||'n/a'));
        }
    };

    document.getElementById('joinCall').onclick = async () => {
        const callId = document.getElementById('callIdInput').value.trim();
        if (!callId) return alert('Enter call_id (from doctor start API) or use Get Active Call');

        const details = await joinCallApi(callId);
        if (!details) return;

        const appId = details.app_id || '{{ config('services.agora.key') }}';
        const token = details.token;
        const channel = details.channel_name;
        const uid = details.uid;

        // Save for polling
        let activeCallId = details.call_id || callId;
        let pollIntervalId = null;

        try {
            if (!client) {
                client = AgoraRTC.createClient({ mode: 'rtc', codec: 'vp8' });
            }

            await client.join(appId, channel, token, uid);
            localTracks = await AgoraRTC.createMicrophoneAndCameraTracks();
            localTracks[1].play('local-player');
            await client.publish(localTracks);

            client.on('user-published', async (user, mediaType) => {
                await client.subscribe(user, mediaType);
                if (mediaType === 'video') {
                    user.videoTrack.play('remote-player');
                    log('Remote video playing');
                }
                if (mediaType === 'audio') {
                    user.audioTrack.play();
                    log('Remote audio playing');
                }
            });

            client.on('user-left', () => {
                log('Remote user left');
                document.getElementById('remote-player').innerHTML = '';
            });

            log('Joined and publishing local tracks');

            // Start polling call status so we auto-leave if doctor ends the call
            const jwt = localStorage.getItem(tokenStorageKey);
            if (jwt && activeCallId) {
                pollIntervalId = setInterval(async () => {
                    try {
                        const res = await fetch('/api/video-call/status?call_id=' + encodeURIComponent(activeCallId), { headers: { 'Authorization': 'Bearer ' + jwt } });
                        const json = await res.json();
                        if (json && json.status && json.data) {
                            if (json.data.status === 'completed') {
                                log('Call marked completed by server');
                                // leave and cleanup
                                if (localTracks.length) {
                                    localTracks.forEach(t => { t.stop(); t.close(); });
                                }
                                if (client) { try { await client.leave(); } catch (e) { console.error(e); } }
                                alert('Call has ended');
                                clearInterval(pollIntervalId);
                                // Optionally redirect or update UI
                            }
                        }
                    } catch (e) {
                        console.error('Polling error', e);
                    }
                }, 5000);
            }

            if (client) {
                await client.leave();
                client = null;
            }
            document.getElementById('local-player').innerHTML = '';
            document.getElementById('remote-player').innerHTML = '';
            log('Left call');
        } catch (e) { console.error(e); log('Leave failed'); }
    };
</script>
</body>
</html>
