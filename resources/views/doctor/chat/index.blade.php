@extends('doctor.layouts.master')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f5f5;
    }

    .chat-container {
        display: flex;
        height: calc(100vh - 170px);
        /* Adjusted for dashboard header and added margin-top */
        margin: 15px;
        margin-left: 250px;
        margin-top: 20px;
        /* Account for doctor sidebar width */
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        width: calc(99% - 250px);
    }

    /* Adjusted for doctor sidebar */
    .sidebar {
        width: 35%;
        background: #ffffff;
        border-right: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
    }

    .chat-area {
        width: 65%;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    .chat-header {
        background: linear-gradient(135deg, #a3a3a3 0%, #e2e2e2 100%);
        color: white;
        padding: 15px 20px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-top: 50px;
    }

    .chat-header h5,
    .chat-header h6 {
        margin: 0;
        font-weight: 600;
    }

    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f8f9fa;
    }

    .message.sent {
        margin-left: auto;
        margin-right: 10px;
    }

    .message.received {
        margin-right: auto;
        margin-left: 10px;
        margin-bottom: 12px;
    }

    .message-bubble {
        padding: 12px 16px;
        border-radius: 18px;
        background: #fff;
        max-width: 70%;
        word-wrap: break-word;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .message.sent .message-bubble {
        background: #dcf8c6;
        border-bottom-right-radius: 4px;
    }

    .message.received .message-bubble {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-bottom-left-radius: 4px;
    }

    .message-time {
        font-size: 11px;
        color: #666;
        margin-top: 4px;
        text-align: right;
    }

    .message.sent .message-time {
        text-align: right;
    }

    .message.received .message-time {
        text-align: left;
    }

    .sender-name {
        font-size: 12px;
        color: #666;
        margin-bottom: 4px;
        font-weight: 500;
    }

    .chat-input-area {
        display: flex;
        padding: 15px 20px;
        background: #ffffff;
        border-top: 1px solid #e0e0e0;
        align-items: center;
    }

    .chat-input {
        flex: 1;
        border-radius: 25px;
        border: 1px solid #ddd;
        padding: 12px 18px;
        outline: none;
        font-size: 14px;
    }

    .chat-input:focus {
        border-color: #f5aa91;
    }

    .send-btn {
        width: 45px;
        height: 45px;
        background: #25d366;
        border: none;
        border-radius: 50%;
        margin-left: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s;
    }

    .send-btn:hover {
        background: #128c7e;
    }

    .conversation-item {
        padding: 15px 20px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.3s;
        display: flex;
        align-items: center;
    }

    .conversation-item:hover {
        background: #f8f9fa;
    }

    .conversation-item.active {
        background: #f0ebeb;
        border-left: 4px solid #f69258;
    }

    .conversation-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f69258;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .conversation-content {
        flex: 1;
        min-width: 0;
    }

    .conversation-item strong {
        color: #333;
        font-size: 16px;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conversation-item small {
        color: #666;
        font-size: 14px;
        margin-top: 2px;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .emoji-picker {
        position: absolute;
        bottom: 80px;
        right: 20px;
        background: #fff;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 10px;
        display: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .emoji-picker span {
        cursor: pointer;
        font-size: 24px;
        padding: 5px;
        transition: transform 0.2s;
    }

    .emoji-picker span:hover {
        transform: scale(1.2);
    }

    .message-actions button {
        background: none;
        border: none;
        color: #007bff;
        cursor: pointer;
        font-size: 12px;
        margin-right: 10px;
        transition: color 0.3s;
    }

    .message-actions button:hover {
        color: #0056b3;
    }

    .message-actions .delete-btn {
        color: #dc3545;
    }

    .message-actions .delete-btn:hover {
        color: #c82333;
    }

    #chatError {
        color: #dc3545;
        padding: 8px 20px;
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
        margin: 0 20px 10px;
    }

    #closeConversationBtn {
        background: #dc3545;
        border: none;
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        transition: background 0.3s;
    }

    #closeConversationBtn:hover {
        background: #c82333;
    }

    @media (max-width: 768px) {
        .chat-container {
            flex-direction: column;
            height: calc(100vh - 140px);
            margin: 10px;
            margin-left: 10px;
            /* Adjusted for mobile */
            width: calc(100% - 20px);
        }

        .sidebar {
            width: 100%;
            height: 200px;
        }

        .chat-area {
            width: 100%;
            height: calc(100% - 200px);
        }

        .conversation-item {
            padding: 10px 15px;
        }

        .conversation-avatar {
            width: 35px;
            height: 35px;
            font-size: 16px;
        }

        .chat-messages {
            padding: 15px;
        }

        .chat-input-area {
            padding: 10px 15px;
        }
    }



    /* Closed conversation badge and styling */
    .closed-badge {
        background: #6c757d;
        color: #fff;
        padding: 4px 8px;
        font-size: 12px;
        border-radius: 999px;
        margin-left: auto;
        display: inline-block;
    }

    .conversation-item[data-status="closed"] {
        opacity: 0.92;
    }
</style>


@section('content')
    <div class="chat-container mt-4">

        {{-- SIDEBAR --}}
        <div class="sidebar" id="conversationsList"
            style="overflow-y: auto; max-height: calc(100vh - 150px); scrollbar-width: thin; scrollbar-color: #ccc #f5f5f5;">
            <div class="chat-header">
                <h5>Chats</h5>
                <a href="{{ route('doctor.appointments.index') }}" style="color: black;font-size:15px"> <- </a>
            </div>

            <div id="conversationsList">
                @php
                    $openConversations = $conversations->filter(fn($c) => strtolower($c->status ?? '') !== 'closed');
                    $closedConversations = $conversations->filter(fn($c) => strtolower($c->status ?? '') === 'closed');
                @endphp

                {{-- Open / Active Conversations --}}
                @if ($openConversations->isNotEmpty())
                    @foreach ($openConversations as $conv)
                        <div class="conversation-item" data-id="{{ $conv->conversation_id }}"
                            data-status="{{ $conv->status }}" data-appointment-id="{{ $conv->appointment_id ?? '' }}">
                            <div class="conversation-avatar">👤</div>
                            <div class="conversation-content">
                                <strong>{{ $conv->patient ? (trim(($conv->patient->first_name ?? '') . ' ' . ($conv->patient->last_name ?? '')) ?: 'Patient') : 'Patient' }}</strong><br>
                                <small>{{ $conv->latestMessage ? $conv->latestMessage->message ?? 'No messages yet' : 'No messages yet' }}</small>
                            </div>
                            @if ($conv->status === 'closed')
                                <span class="closed-badge">Closed</span>
                            @endif
                            @if (
                                $conv->status === 'closed' &&
                                    $conv->appointment_id &&
                                    !\App\Models\ChatConversation::where('patient_id', $conv->patient_id)->where('status', '!=', 'closed')->exists())
                                <div class="conversation-actions" style="margin-left:auto;">
                                    <button class="btn btn-sm btn-success start-conv-btn"
                                        data-appointment-id="{{ $conv->appointment_id }}">Start</button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p style="padding: 15px; color: #666;">No active conversations. Conversations will appear here once
                        patients start chatting or appointments are created.</p>
                @endif

                {{-- Closed Conversations (collapsible) --}}
                <div class="closed-section" style="margin-top:10px;">
                    <div id="closedSectionToggle"
                        style="padding:10px 20px; border-top:1px solid #eee; cursor:pointer; display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; gap:8px; align-items:center;">
                            <strong>Closed Conversations</strong>
                            <small class="text-muted">({{ $closedConversations->count() }})</small>
                        </div>
                        <button id="closedToggleBtn" class="btn btn-sm btn-outline-secondary" type="button">Show</button>
                    </div>

                    <div id="closedConversationsList" style="display:none;">
                        @if ($closedConversations->isNotEmpty())
                            @foreach ($closedConversations as $conv)
                                <div class="conversation-item" data-id="{{ $conv->conversation_id }}"
                                    data-status="{{ $conv->status }}"
                                    data-appointment-id="{{ $conv->appointment_id ?? '' }}">
                                    <div class="conversation-avatar">👤</div>
                                    <div class="conversation-content">
                                        <strong>{{ $conv->patient ? (trim(($conv->patient->first_name ?? '') . ' ' . ($conv->patient->last_name ?? '')) ?: 'Patient') : 'Patient' }}</strong><br>
                                        <small>{{ $conv->latestMessage ? $conv->latestMessage->message ?? 'No messages yet' : 'No messages yet' }}</small>
                                    </div>
                                    @if ($conv->status === 'closed')
                                        <span class="closed-badge">Closed</span>
                                    @endif
                                    @if (
                                        $conv->status === 'closed' &&
                                            $conv->appointment_id &&
                                            !\App\Models\ChatConversation::where('patient_id', $conv->patient_id)->where('status', '!=', 'closed')->exists())
                                        <div class="conversation-actions" style="margin-left:auto;">
                                            <button class="btn btn-sm btn-success start-conv-btn"
                                                data-conversation-id="{{ $conv->conversation_id }}">Start</button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p style="padding: 15px; color: #666;">No closed conversations.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- CHAT AREA --}}
        <div class="chat-area">
            <div class="chat-header">
                <h6 id="chatTitle">Select a conversation</h6>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span id="onlineStatus" class="badge bg-success" style="display:none;">Online</span>
                    <button id="closeConversationBtn" class="btn btn-danger btn-sm" style="display:none;">Close
                        Conversation</button>
                </div>
            </div>

            <div id="messages" class="chat-messages">
                <p class="text-muted text-center">Select a conversation</p>
            </div>

            <div class="chat-input-area">
                <button id="emojiBtn">😊</button>
                <input type="text" id="messageInput" class="chat-input" placeholder="Type message">
                <input type="file" id="fileInput" style="display:none;">
                {{-- <button id="fileBtn" class="send-btn" type="button" style="background:#007bff;" style="">📎</button>
                <button id="sendBtn" class="send-btn" type="button">📤</button> --}}

                <button id="fileBtn" class="send-btn" type="button" title="Attach file">
                    <i class="fas fa-paperclip"></i>
                </button>
                <button id="sendBtn" class="send-btn" type="button" title="Send message">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <div id="chatError" style="color:#b71c1c; padding:6px; display:none;"></div>

            <div id="emojiPicker" class="emoji-picker">
                <span>😀</span><span>😃</span><span>😂</span><span>😍</span><span>😎</span><span>😭</span>
            </div>

            <!-- Start Conversation Modal -->
            <div id="startConversationModal" class="modal" tabindex="-1"
                style="display:none; position:fixed; z-index:1050; left:0; top:0; width:100%; height:100%; overflow:auto; background:rgba(0,0,0,0.4);">
                <div style="background:#fff; margin:6% auto; padding:20px; max-width:720px; border-radius:8px;">
                    <h5>Start Conversation from Appointment</h5>
                    <div id="appointmentList" style="max-height:380px; overflow:auto; margin-top:10px;"></div>
                    <div style="margin-top:15px; text-align:right;">
                        <button id="closeStartModal" class="btn btn-secondary btn-sm">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>



@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js/doctor/chat.js') }}"></script>
    <script>
        let baseUrl = "{{ config('app.url') }}";
        /* ===============================
                                                                                                                                           GLOBAL STATE
                                                                                                                                        ================================ */
        let currentConversationId = null;
        let conversationStatus = null; // Track if conversation is closed
        const currentUserId = {{ Auth::id() }};
        const currentDoctorId = {{ $doctor->id ?? 'null' }};

        // Fallback: if doctor ID is not available, try to get it from a data attribute or API call
        if (currentDoctorId === null) {
            // This shouldn't happen in normal flow, but as a safeguard
            console.warn('Doctor ID not available in JavaScript');
        }

        /* ===============================
           INIT
        ================================ */
        document.addEventListener('DOMContentLoaded', () => {
            initConversations();
            initSend();
            initEmoji();
            initClose();

            // Determine which conversation to open: URL param `?c=` takes precedence, otherwise don't auto-open
            const params = new URLSearchParams(window.location.search);
            const urlConv = params.get('c');

            if (urlConv) {
                const el = document.querySelector('.conversation-item[data-id="' + urlConv + '"]');
                if (el) {
                    // Use the existing click handler for selection
                    el.click();
                } else {
                    // If it's not in the list, fetch and add it
                    (async () => {
                        try {
                            const res = await fetch(baseUrl + '/doctor/chat/' + encodeURIComponent(
                                urlConv));
                            if (res.ok) {
                                const json = await res.json();
                                if (json.success && json.conversation) {
                                    addConversationToList(json.conversation);
                                    const newEl = document.querySelector('.conversation-item[data-id="' +
                                        urlConv + '"]');
                                    if (newEl) newEl.click();
                                } else {
                                    console.error('Conversation API returned error', json);
                                }
                            } else {
                                console.error('Failed to fetch conversation', res.status);
                            }
                        } catch (err) {
                            console.error('Error fetching conversation', err);
                        }
                    })();
                }
            }
        });

        /* ===============================
           CONVERSATIONS
        ================================ */
        function attachConversationItemHandlers(root = document) {
            // Attach click handlers to conversation items inside `root`
            root.querySelectorAll('.conversation-item').forEach(item => {
                // Prevent adding duplicate listeners
                if (item._hasConversationHandler) return;
                item._hasConversationHandler = true;

                item.addEventListener('click', () => {
                    document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove(
                        'active'));
                    item.classList.add('active');

                    currentConversationId = item.dataset.id;
                    // Use patient name if available (innerText might include other labels)
                    const nameEl = item.querySelector('.conversation-content strong');
                    document.getElementById('chatTitle').innerText = nameEl ? nameEl.innerText : item
                        .innerText;

                    // If conversation is closed, hide inputs and Close button, show Closed badge; otherwise show inputs
                    const status = (item.dataset.status || '').toLowerCase();
                    if (status === 'closed') {
                        document.getElementById('onlineStatus').style.display = 'none';
                        document.getElementById('closeConversationBtn').style.display = 'none';
                        // Disable/hide inputs immediately to reflect closed state
                        disableInputs();
                    } else {
                        document.getElementById('onlineStatus').style.display = 'inline';
                        document.getElementById('closeConversationBtn').style.display = 'inline';
                        // Show input area (was hidden for closed conversation)
                        const inputArea = document.querySelector('.chat-input-area');
                        inputArea.style.display = 'flex';
                        // Remove any closed note that may exist
                        const closedNote = inputArea.querySelector('p');
                        if (closedNote && closedNote.textContent === 'Conversation is closed') {
                            inputArea.removeChild(closedNote);
                        }
                        // Ensure individual controls are enabled
                        document.getElementById('messageInput').disabled = false;
                        document.getElementById('sendBtn').disabled = false;
                        document.getElementById('fileBtn').disabled = false;
                        document.getElementById('emojiBtn').disabled = false;
                    }

                    // Update URL to reflect current conversation
                    window.history.replaceState(null, '', '?c=' + currentConversationId);

                    loadMessages(currentConversationId);
                });
            });

            // Attach handlers for any start buttons inside root (reopen closed conversation)
            root.querySelectorAll('.start-conv-btn').forEach(btn => {
                if (btn._hasStartHandler) return;
                btn._hasStartHandler = true;
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const conversationId = btn.dataset.conversationId || btn.closest(
                        '.conversation-item')?.dataset.id;
                    if (!conversationId) {
                        alert('No conversation linked to this action.');
                        return;
                    }
                    if (!confirm('Reopen this conversation?')) return;
                    await reopenConversation(conversationId);
                });
            });
        }

        function initConversations() {
            // Attach handlers for existing items
            attachConversationItemHandlers(document);

            // Closed conversations toggle
            const closedToggleBtn = document.getElementById('closedToggleBtn');
            const closedList = document.getElementById('closedConversationsList');
            const closedSectionToggle = document.getElementById('closedSectionToggle');

            if (closedToggleBtn && closedList) {
                closedToggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (closedList.style.display === 'none' || closedList.style.display === '') {
                        closedList.style.display = 'block';
                        closedToggleBtn.innerText = 'Hide';
                        // Attach handlers to items inside closed list
                        attachConversationItemHandlers(closedList);
                    } else {
                        closedList.style.display = 'none';
                        closedToggleBtn.innerText = 'Show';
                    }
                });
            }

            if (closedSectionToggle) {
                closedSectionToggle.addEventListener('click', (e) => {
                    // If button click already handled, ignore; ensure toggle works when clicking on header
                    const btn = document.getElementById('closedToggleBtn');
                    if (btn) btn.click();
                });
            }
        }

        function addConversationToList(conv) {
            const list = document.getElementById('conversationsList');
            const div = document.createElement('div');
            div.className = 'conversation-item';
            div.dataset.id = conv.conversation_id;
            div.dataset.status = conv.status || '';
            div.dataset.appointmentId = conv.appointment_id || '';
            div.dataset.conversationId = conv.conversation_id || '';

            const name = conv.patient ? (((conv.patient.first_name || '') + ' ' + (conv.patient.last_name || '')).trim() ||
                'Patient') : 'Patient';
            const preview = (conv.latestMessage && conv.latestMessage.message) || 'No messages yet';

            div.innerHTML = `
        <div class="conversation-avatar">👤</div>
        <div class="conversation-content">
            <strong>${name}</strong><br>
            <small>${preview}</small>
        </div>
    `;

            // Insert at top
            list.insertBefore(div, list.firstChild);

            // If the conversation is closed and has an appointment (or just closed), add a Start button that reopens the conversation
            if (conv.status === 'closed') {
                const actionDiv = document.createElement('div');
                actionDiv.className = 'conversation-actions';
                actionDiv.style.marginLeft = 'auto';
                const btn = document.createElement('button');
                btn.className = 'btn btn-sm btn-success start-conv-btn';
                btn.dataset.conversationId = conv.conversation_id;
                btn.innerText = 'Start';
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const convId = btn.dataset.conversationId;
                    if (!convId) {
                        alert('Conversation identifier missing');
                        return;
                    }
                    if (!confirm('Reopen this conversation?')) return;
                    await reopenConversation(convId);
                });
                actionDiv.appendChild(btn);
                div.appendChild(actionDiv);
            }

            // Attach click handler
            div.addEventListener('click', () => {
                document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('active'));
                div.classList.add('active');

                currentConversationId = div.dataset.id;
                document.getElementById('chatTitle').innerText = name;
                document.getElementById('onlineStatus').style.display = 'inline';
                document.getElementById('closeConversationBtn').style.display = 'inline';

                // Show input area in case it was hidden for a closed conversation
                const inputArea = document.querySelector('.chat-input-area');
                inputArea.style.display = 'flex';
                // Remove any closed note
                const closedNote = inputArea.querySelector('p');
                if (closedNote && closedNote.textContent === 'Conversation is closed') {
                    inputArea.removeChild(closedNote);
                }
                // Ensure individual controls are enabled
                document.getElementById('messageInput').disabled = false;
                document.getElementById('sendBtn').disabled = false;
                document.getElementById('fileBtn').disabled = false;
                document.getElementById('emojiBtn').disabled = false;

                // Update URL
                window.history.replaceState(null, '', '?c=' + currentConversationId);

                loadMessages(currentConversationId);
            });
        }

        /* ===============================
           LOAD MESSAGES
        ================================ */
        async function loadMessages(conversationId) {
            const messagesContainer = document.getElementById('messages');
            messagesContainer.innerHTML = '<p class="text-muted text-center">Loading messages...</p>';

            try {
                // Ensure the conversationId is properly encoded to avoid issues with special characters
                const url = `${baseUrl}/doctor/chat/${encodeURIComponent(conversationId)}/messages`;
                const res = await fetch(url);

                if (!res.ok) {
                    // Handle HTTP errors
                    const errorText = await res.text();
                    console.error('Failed to load messages:', res.status, errorText);
                    const container = document.getElementById('messages');
                    container.innerHTML = '<p class="text-muted text-center">Failed to load messages (Error ' + res
                        .status + ')</p>';
                    return;
                }

                const json = await res.json();

                const container = document.getElementById('messages');

                if (json.success && json.messages) {
                    console.log('json.messages:', json.messages);
                    let messagesArray;
                    if (Array.isArray(json.messages)) {
                        messagesArray = json.messages;
                    } else if (json.messages && typeof json.messages === 'object') {
                        if (Array.isArray(json.messages.data)) {
                            messagesArray = json.messages.data;
                        } else {
                            // Assume it's an object with numeric string keys, convert to array
                            messagesArray = Object.values(json.messages);
                        }
                    } else {
                        container.innerHTML = '<p class="text-muted text-center">Invalid messages format: ' + JSON
                            .stringify(json.messages) + '</p>';
                        return;
                    }

                    if (messagesArray.length === 0) {
                        container.innerHTML =
                            '<p class="text-muted text-center">No messages yet. Start the conversation!</p>';
                    } else {
                        container.innerHTML = ''; // Clear loading message
                        messagesArray.forEach(m => {
                            container.appendChild(createMessageElement(m));
                        });
                        // Scroll to bottom after loading messages
                        setTimeout(() => {
                            container.scrollTop = container.scrollHeight;
                        }, 100);
                        // Mark messages as read
                        markAsRead(conversationId);
                    }

                    // Check conversation status and disable if closed
                    conversationStatus = json.conversation_status;
                    if (conversationStatus === 'closed') {
                        disableInputs();
                    }
                } else {
                    // Show specific error message from API
                    const errorMsg = json.message || 'Failed to load messages';
                    container.innerHTML = '<p class="text-muted text-center">' + errorMsg + '</p>';
                }
            } catch (err) {
                console.error('Error loading messages', err);
                const container = document.getElementById('messages');
                let errorMessage = 'Error loading messages. ';

                if (err.name === 'TypeError' && err.message.includes('fetch')) {
                    errorMessage += 'Network connection issue. Please check your internet connection.';
                } else if (err.message) {
                    errorMessage += err.message;
                } else {
                    errorMessage += 'Please check your connection and try again.';
                }

                container.innerHTML = '<p class="text-muted text-center">' + errorMessage + '</p>';
            }
        }

        /* ===============================
           MESSAGE ELEMENT
        ================================ */
        function createMessageElement(m) {
            const div = document.createElement('div');
            div.className = 'message';

            // Get sender information - use the processed sender data from controller
            let senderId = m.sender_id;
            let senderType = m.sender_type;
            let senderName = m.sender_name || 'Unknown User';

            // System messages should be centered
            if (senderType === 'system' || (!senderId && senderType === 'system')) {
                div.classList.add('system');
                div.innerHTML = `
            <div class="message-bubble" style="background:#f5f5f5; text-align:center; margin: 5px auto; max-width: 80%;">
                ${m.message || 'System message'}
                <div class="message-time" style="font-size: 11px; color: #666; margin-top: 2px;">${formatMessageTime(m.created_at)}</div>
            </div>
        `;
                return div;
            }

            // Treat as sent if the sender is the current doctor
            if (senderType === 'doctor' && senderId == currentDoctorId) {
                div.classList.add('sent');
            } else {
                div.classList.add('received');
            }

            // Add sender name for received messages (from patients)
            const senderLabel = (senderType === 'patient' && div.classList.contains('received')) ?
                `<div class="sender-name" style="font-size: 12px; color: #666; margin-bottom: 2px;">${senderName}</div>` :
                '';

            // Handle file messages
            let messageContent = m.message || 'Message content';
            if (m.attachments && m.attachments.length > 0) {
                const file = m.attachments[0];
                messageContent = `<a href="${file.url}" target="_blank">${file.name}</a>`;
            }

            // Add edit/delete buttons for sent messages
            let actionButtons = '';
            if (div.classList.contains('sent')) {
                actionButtons = `
            <div class="message-actions" style="margin-top: 5px; text-align: right;">
                <button class="edit-btn" data-id="${m.id}" style="background:none; border:none; color:#007bff; cursor:pointer; font-size:12px;">Edit</button>
                <button class="delete-btn" data-id="${m.id}" style="background:none; border:none; color:#dc3545; cursor:pointer; font-size:12px; margin-left:10px;">Delete</button>
            </div>
        `;
            }

            div.innerHTML = `
        ${senderLabel}
        <div class="message-bubble">
            ${messageContent}
            <div class="message-time">${formatMessageTime(m.created_at)}</div>
        </div>
        ${actionButtons}
    `;

            // Add event listeners for edit and delete
            if (div.classList.contains('sent')) {
                div.querySelector('.edit-btn').addEventListener('click', () => editMessage(m.id));
                div.querySelector('.delete-btn').addEventListener('click', () => deleteMessage(m.id));
            }

            return div;
        }

        /* ===============================
           TIME FORMATTING
        ================================ */
        function formatMessageTime(timestamp) {
            if (!timestamp) return '';

            const date = new Date(timestamp);
            const now = new Date();
            const diffMs = now - date;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

            if (diffDays === 0) {
                // Today - show time only
                return date.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } else if (diffDays === 1) {
                // Yesterday
                return 'Yesterday ' + date.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } else if (diffDays < 7) {
                // This week - show day and time
                return date.toLocaleDateString([], {
                    weekday: 'short'
                }) + ' ' + date.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } else {
                // Older - show date and time
                return date.toLocaleDateString([], {
                    month: 'short',
                    day: 'numeric'
                }) + ' ' + date.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }

        /* ===============================
           SEND MESSAGE
        ================================ */
        function initSend() {
            document.getElementById('sendBtn').addEventListener('click', sendMessage);
            document.getElementById('messageInput').addEventListener('keypress', e => {
                if (e.key === 'Enter') sendMessage();
            });
            document.getElementById('fileBtn').addEventListener('click', () => {
                document.getElementById('fileInput').click();
            });
            document.getElementById('fileInput').addEventListener('change', sendFile);
        }

        async function sendFile() {
            // Just call sendMessage, which now handles files
            await sendMessage();
        }

        async function sendMessage() {
            const chatError = document.getElementById('chatError');
            chatError.style.display = 'none';
            chatError.innerText = '';

            if (!currentConversationId) {
                chatError.style.display = 'block';
                chatError.innerText = 'No conversation selected. Click a conversation or open from an appointment.';
                return;
            }

            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];

            if (!message && !file) return;

            // Disable send button to prevent double-sends
            const sendBtn = document.getElementById('sendBtn');
            sendBtn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('conversation_id', currentConversationId);
                if (message) formData.append('message', message);
                if (file) formData.append('file', file);

                console.debug('Sending message payload', {
                    conversation_id: currentConversationId,
                    message,
                    file: file ? file.name : null
                });

                const res = await fetch('{{ route('doctor.chat.send') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                console.debug('Send response status', res.status);

                if (!res.ok) {
                    const text = await res.text();
                    console.error('Send failed', res.status, text);

                    if (res.status === 419) {
                        chatError.style.display = 'block';
                        chatError.innerText = 'Session expired. Please refresh the page and try again.';
                    } else if (res.status === 422) {
                        try {
                            const err = JSON.parse(text);
                            const msg = (err.errors && Object.values(err.errors).flat().join(' ')) || err.message ||
                                'Validation error';
                            chatError.style.display = 'block';
                            chatError.innerText = msg;
                        } catch (e) {
                            chatError.style.display = 'block';
                            chatError.innerText = 'Validation failed. Check your message.';
                        }
                    } else {
                        try {
                            const err = JSON.parse(text);
                            chatError.style.display = 'block';
                            chatError.innerText = err.message || ('Error: ' + res.status);
                        } catch (e) {
                            chatError.style.display = 'block';
                            chatError.innerText = 'Failed to send message (status ' + res.status + ')';
                        }
                    }
                    return;
                }

                const json = await res.json();
                console.debug('Send response JSON', json);

                if (json.success) {
                    // Clear input only after successful send
                    input.value = '';
                    fileInput.value = '';
                    const msgData = json.data || json.message || json;

                    // Append the sent message optimistically, then reload from server to ensure persisted state
                    document.getElementById('messages')
                        .appendChild(createMessageElement(msgData));
                    const container = document.getElementById('messages');
                    container.scrollTop = container.scrollHeight;

                    // Update conversation preview and move conversation to bottom
                    const convItem = document.querySelector('.conversation-item[data-id="' + currentConversationId +
                        '"]');
                    if (convItem) {
                        const namePart = convItem.querySelector('strong') ? convItem.querySelector('strong').innerText :
                            '';
                        const preview = (msgData.attachments && msgData.attachments.length > 0) ? 'File sent' : msgData
                            .message;
                        convItem.innerHTML = `<strong>${namePart}</strong><br><small>${preview}</small>`;
                        const parent = convItem.parentNode;
                        parent.insertBefore(convItem, parent.firstChild); // Move to top
                    }

                    // Refresh the message list from server to make sure everything that was saved is visible
                    try {
                        await loadMessages(currentConversationId);
                    } catch (e) {
                        console.warn('Failed to reload messages after send', e);
                    }

                } else {
                    console.error('Send error', json);
                    const errorMsg = json.message || 'Failed to send message';
                    chatError.style.display = 'block';
                    chatError.innerText = errorMsg;
                }
            } catch (err) {
                console.error('Error sending message', err);
                chatError.style.display = 'block';
                chatError.innerText = 'Network error. Please check your connection.';
            } finally {
                sendBtn.disabled = false;
            }
        }

        /* ===============================
           EDIT MESSAGE
        ================================ */
        async function editMessage(messageId) {
            const newMessage = prompt('Edit your message:');
            if (!newMessage || newMessage.trim() === '') return;

            try {
                const res = await fetch(
                    `{{ route('doctor.chat.message.update', ['conversationId' => ':conversationId', 'messageId' => ':messageId']) }}`
                    .replace(':conversationId', currentConversationId).replace(':messageId', messageId), {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            message: newMessage.trim()
                        })
                    });

                if (res.ok) {
                    await loadMessages(currentConversationId);
                } else {
                    alert('Failed to edit message');
                }
            } catch (err) {
                console.error('Error editing message', err);
                alert('Error editing message');
            }
        }

        /* ===============================
           DELETE MESSAGE
        ================================ */
        async function deleteMessage(messageId) {
            if (!confirm('Are you sure you want to delete this message?')) return;

            try {
                const res = await fetch(
                    `{{ route('doctor.chat.message.delete', ['conversationId' => ':conversationId', 'messageId' => ':messageId']) }}`
                    .replace(':conversationId', currentConversationId).replace(':messageId', messageId), {
                        method: 'DELETE',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                if (res.ok) {
                    await loadMessages(currentConversationId);
                } else {
                    alert('Failed to delete message');
                }
            } catch (err) {
                console.error('Error deleting message', err);
                alert('Error deleting message');
            }
        }

        /* ===============================
           MARK AS READ
        ================================ */
        async function markAsRead(conversationId) {
            try {
                await fetch(`{{ route('doctor.chat.conversation.read', ':conversationId') }}`.replace(
                    ':conversationId', conversationId), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            } catch (err) {
                console.error('Error marking as read', err);
            }
        }

        /* ===============================
           DISABLE INPUTS
        ================================ */
        function disableInputs() {
            // Hide input area completely
            const inputArea = document.querySelector('.chat-input-area');
            if (inputArea) inputArea.style.display = 'none';
            // Hide close button and online status
            const closeBtn = document.getElementById('closeConversationBtn');
            const online = document.getElementById('onlineStatus');
            if (closeBtn) closeBtn.style.display = 'none';
            if (online) online.style.display = 'none';
            // Add a visible note in the messages area (if not already present)
            const messages = document.getElementById('messages');
            if (messages) {
                const existing = messages.querySelector('.closed-note');
                if (!existing) {
                    const note = document.createElement('p');
                    note.className = 'closed-note text-muted text-center';
                    note.textContent = 'Conversation is closed';
                    note.style.margin = '10px 0';
                    messages.appendChild(note);
                }
            }
        }

        /* ===============================
           CLOSE CONVERSATION
        ================================ */
        function initClose() {
            document.getElementById('closeConversationBtn').addEventListener('click', closeConversation);
        }

        async function closeConversation() {
            if (!currentConversationId) {
                alert('No conversation selected');
                return;
            }

            if (!confirm('Are you sure you want to close this conversation? This will prevent further messages.')) {
                return;
            }

            try {
                const res = await fetch(`{{ route('doctor.chat.close', ':conversationId') }}`.replace(
                    ':conversationId', currentConversationId), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (res.ok) {
                    const json = await res.json();
                    if (json.success) {
                        alert('Conversation closed successfully');
                        // Apply disabled/hidden inputs
                        disableInputs();

                        // Update conversation item in the list: mark closed and add Start button if appointment exists
                        const convItem = document.querySelector('.conversation-item[data-id="' + currentConversationId +
                            '"]');
                        if (convItem) {
                            convItem.dataset.status = 'closed';
                            const conversationId = convItem.dataset.conversationId || convItem.dataset.id || convItem
                                .getAttribute('data-id');
                            if (conversationId && !convItem.querySelector('.start-conv-btn')) {
                                const actionDiv = document.createElement('div');
                                actionDiv.className = 'conversation-actions';
                                actionDiv.style.marginLeft = 'auto';
                                const btn = document.createElement('button');
                                btn.className = 'btn btn-sm btn-success start-conv-btn';
                                btn.dataset.conversationId = conversationId;
                                btn.innerText = 'Start';
                                btn.addEventListener('click', async (e) => {
                                    e.stopPropagation();
                                    if (!confirm('Reopen this conversation?')) return;
                                    await reopenConversation(conversationId);
                                });
                                actionDiv.appendChild(btn);
                                convItem.appendChild(actionDiv);
                            }
                        }
                    } else {
                        alert('Failed to close conversation: ' + (json.message || 'Unknown error'));
                    }
                } else {
                    alert('Failed to close conversation (status ' + res.status + ')');
                }
            } catch (err) {
                console.error('Error closing conversation', err);
                alert('Error closing conversation');
            }
        }

        /* ===============================
           EMOJI
        ================================ */
        function initEmoji() {
            const picker = document.getElementById('emojiPicker');
            const btn = document.getElementById('emojiBtn');
            const input = document.getElementById('messageInput');

            btn.onclick = () => picker.style.display = picker.style.display === 'block' ? 'none' : 'block';

            picker.querySelectorAll('span').forEach(e => {
                e.onclick = () => {
                    input.value += e.innerText;
                    picker.style.display = 'none';
                };
            });
        }

        /* ===============================
           START CONVERSATION (Modal + API)
        ================================ */
        function initNewConversation() {
            const newBtn = document.getElementById('newConversationBtn');
            if (newBtn) newBtn.addEventListener('click', openStartModal);
            const closeModalBtn = document.getElementById('closeStartModal');
            if (closeModalBtn) closeModalBtn.addEventListener('click', closeStartModal);
            const modal = document.getElementById('startConversationModal');
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) closeStartModal();
                });
            }
        }

        async function openStartModal() {
            const modal = document.getElementById('startConversationModal');
            const list = document.getElementById('appointmentList');
            list.innerHTML = '<p>Loading appointments...</p>';
            modal.style.display = 'block';
            try {
                const res = await fetch('{{ route('doctor.chat.appointments') }}');
                if (res.ok) {
                    const json = await res.json();
                    if (json.success && json.appointments && json.appointments.length > 0) {
                        list.innerHTML = '';
                        json.appointments.forEach(a => {
                            const div = document.createElement('div');
                            div.style.borderBottom = '1px solid #eee';
                            div.style.padding = '10px';
                            div.innerHTML =
                                `<strong>${a.patient_name}</strong><br><small>${a.date} ${a.time} — ${a.status}</small>
                                    <div style="margin-top:8px; text-align:right;"><button class="btn btn-sm btn-primary start-appt-btn" data-id="${a.id}">Start</button></div>`;
                            list.appendChild(div);
                        });
                        list.querySelectorAll('.start-appt-btn').forEach(b => {
                            b.addEventListener('click', () => startConversationByAppointment(b.dataset.id));
                        });
                    } else {
                        list.innerHTML = '<p>No scheduled appointments available to start a conversation.</p>';
                    }
                } else {
                    list.innerHTML = '<p>Failed to load appointments (status ' + res.status + ')</p>';
                }
            } catch (err) {
                console.error('Error loading appointments', err);
                list.innerHTML = '<p>Error loading appointments. Please try again.</p>';
            }
        }

        function closeStartModal() {
            document.getElementById('startConversationModal').style.display = 'none';
        }

        async function startConversationByAppointment(appointmentId) {
            try {
                const res = await fetch('{{ route('doctor.chat.start') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        appointment_id: appointmentId
                    })
                });

                if (res.ok) {
                    const json = await res.json();
                    if (json.success && json.conversation_id) {
                        // Close modal
                        closeStartModal();
                        // Fetch the created conversation and add to list
                        const convRes = await fetch('${baseUrl}/doctor/chat/' + encodeURIComponent(json
                            .conversation_id));
                        if (convRes.ok) {
                            const convJson = await convRes.json();
                            if (convJson.success && convJson.conversation) {
                                addConversationToList(convJson.conversation);
                                // auto-open it
                                const el = document.querySelector('.conversation-item[data-id="' + json
                                    .conversation_id + '"]');
                                if (el) el.click();
                            }
                        }
                    } else {
                        alert('Failed to start conversation: ' + (json.message || 'Unknown error'));
                    }
                } else {
                    const text = await res.text();
                    alert('Failed to start conversation: ' + (text || res.status));
                }
            } catch (err) {
                console.error('Error starting conversation', err);
                alert('Error starting conversation. Check console.');
            }
        }

        /* Reopen a closed conversation (status update) */
        async function reopenConversation(conversationId) {
            try {
                const res = await fetch(`{{ route('doctor.chat.reopen', ':conversationId') }}`.replace(
                    ':conversationId', conversationId), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (res.ok) {
                    const json = await res.json();
                    if (json.success) {
                        // Update UI: mark item active/active status, remove Start button
                        const convItem = document.querySelector('.conversation-item[data-id="' + conversationId + '"]');
                        if (convItem) {
                            convItem.dataset.status = 'active';
                            const startBtn = convItem.querySelector('.start-conv-btn');
                            if (startBtn) startBtn.remove();
                        }

                        // If currently viewing this conversation, re-enable inputs and reload messages
                        if (currentConversationId === conversationId) {
                            document.getElementById('onlineStatus').style.display = 'inline';
                            document.getElementById('closeConversationBtn').style.display = 'inline';
                            const inputArea = document.querySelector('.chat-input-area');
                            inputArea.style.display = 'flex';
                            const closedNote = inputArea.querySelector('p');
                            if (closedNote && closedNote.textContent === 'Conversation is closed') {
                                inputArea.removeChild(closedNote);
                            }
                            document.getElementById('messageInput').disabled = false;
                            document.getElementById('sendBtn').disabled = false;
                            document.getElementById('fileBtn').disabled = false;
                            document.getElementById('emojiBtn').disabled = false;

                            try {
                                await loadMessages(conversationId);
                            } catch (e) {
                                console.warn('Failed to reload messages after reopen', e);
                            }
                        } else {
                            // Auto-open the reopened conversation
                            const el = document.querySelector('.conversation-item[data-id="' + conversationId + '"]');
                            if (el) el.click();
                        }

                        alert('Conversation reopened successfully');
                    } else {
                        alert('Failed to reopen conversation: ' + (json.message || 'Unknown error'));
                    }
                } else {
                    alert('Failed to reopen conversation (status ' + res.status + ')');
                }
            } catch (err) {
                console.error('Error reopening conversation', err);
                alert('Error reopening conversation');
            }
        }
    </script>
@endsection
