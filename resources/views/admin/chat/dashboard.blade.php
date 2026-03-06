@extends(strpos(request()->path(), 'staff') === 0 ? 'staff.layouts.master' : 'admin.layouts.master')

@section('title')
    Patient Chat Dashboard
@endsection

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 m-4">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Patient Conversations</h4>
                
            </div>
        </div>
    </div>

    <div class="row chat-wrapper">
        <!-- Chat Sidebar: Conversations List -->
        <div class="col-xl-4 col-lg-5">
            <div class="card chat-left-sidebar">
                <div class="card-body p-0">
                    <div class="p-3 border-bottom">
                        <div class="search-box">
                            <input type="text" class="form-control" placeholder="Search patients..." id="searchChat">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>

                    <div class="chat-message-list" style="height: 600px; overflow-y: auto;">
                        <ul class="list-unstyled chat-list" id="conversationList">
                            @foreach($conversations as $conv)
                            <li class="{{ $loop->first ? 'active' : '' }}" onclick="loadConversation('{{ $conv->conversation_id }}', this)">
                                <a href="javascript: void(0);">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-sm">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    {{ strtoupper(substr($conv->patient->first_name ?? 'P', 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="text-truncate font-size-14 mb-1">{{ $conv->patient->first_name ?? 'Unknown' }} {{ $conv->patient->last_name ?? '' }}</h5>
                                            <p class="text-muted font-size-11 mb-1">
                                                <i class="fas fa-user-md me-1"></i> 
                                                {{ $conv->appointment->doctor->first_name ?? 'No Doctor' }} {{ $conv->appointment->doctor->last_name ?? '' }}
                                            </p>
                                            <p class="text-truncate mb-0 last-message" id="last-msg-{{ $conv->conversation_id }}">
                                                {{ $conv->latestMessage->message ?? 'No messages yet' }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="font-size-11">{{ $conv->last_message_at ? $conv->last_message_at->format('H:i') : '' }}</div>
                                            @if($conv->unread_count > 0)
                                            <span class="badge bg-danger rounded-pill float-end unread-badge">{{ $conv->unread_count }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Main Area -->
        <div class="col-xl-8 col-lg-7">
            <div class="card chat-conversation-card" style="height: 685px;">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-3">
                            <span class="avatar-title rounded-circle bg-soft-primary text-primary" id="activeChatAvatar">P</span>
                        </div>
                        <div>
                            <h5 class="font-size-15 mb-1" id="activeChatName">Select a conversation</h5>
                            <p class="text-muted mb-0" id="activeChatStatus">
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-soft-danger btn-sm" onclick="endChat()"><i class="fas fa-times-circle"></i> Close Chat</button>
                    </div>
                </div>

                <div class="card-body p-0 position-relative">
                    <div id="chat-messages" class="chat-conversation p-3" style="height: 500px; overflow-y: auto;">
                        <div class="text-center mt-5 text-muted empty-chat-state">
                            <i class="fas fa-comments fa-4x mb-3 text-light"></i>
                            <p>Select a patient from the list to start chatting</p>
                        </div>
                    </div>

                    <!-- Chat Input Area -->
                    <div class="p-3 border-top chat-input-section bg-white">
                        <form id="chatForm" enctype="multipart/form-data">
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <label for="attachmentInput" class="btn btn-light rounded-circle mb-0" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="fas fa-paperclip text-muted"></i>
                                    </label>
                                    <input type="file" id="attachmentInput" class="d-none" onchange="handleFileSelect(this)">
                                </div>
                                <div class="col">
                                    <div class="position-relative">
                                        <input type="text" class="form-control chat-input" placeholder="Type a message..." id="chatInput" autocomplete="off">
                                        <div id="filePreview" class="position-absolute d-none" style="bottom: 50px; left: 0; background: white; padding: 10px; border-radius: 10px; box-shadow: 0 -5px 15px rgba(0,0,0,0.1); width: 100%;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span id="fileName" class="text-truncate" style="max-width: 80%;"></span>
                                                <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="clearFile()"><i class="fas fa-times"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary chat-send waves-effect waves-light">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .chat-wrapper {
        margin-top: -10px;
    }
    .chat-left-sidebar {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .chat-list li a {
        padding: 15px 20px;
        display: block;
        transition: all 0.3s;
        border-left: 3px solid transparent;
        text-decoration: none;
        color: inherit;
    }
    .chat-list li.active a {
        background-color: rgba(255, 73, 0, 0.05);
        border-left-color: #ff4900;
    }
    .chat-list li:hover a {
        background-color: #f8f9fa;
    }
    .avatar-sm {
        height: 48px;
        width: 48px;
    }
    .bg-soft-primary {
        background-color: rgba(255, 73, 0, 0.1) !important;
    }
    .text-primary {
        color: #ff4900 !important;
    }
    .btn-primary {
        background-color: #ff4900 !important;
        border-color: #ff4900 !important;
    }
    .btn-primary:hover {
        background-color: #e64200 !important;
        border-color: #e64200 !important;
    }
    .chat-conversation {
        display: flex;
        flex-direction: column;
        gap: 15px;
        background-color: #fdf2ee; /* Light orange tint background */
    }
    .message {
        max-width: 75%;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 14px;
        position: relative;
    }
    .message-right {
        align-self: flex-end;
        background-color: #ff4900;
        color: white;
        border-bottom-right-radius: 2px;
    }
    .message-left {
        align-self: flex-start;
        background-color: white;
        color: #333;
        border-bottom-left-radius: 2px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .message-time {
        font-size: 10px;
        margin-top: 5px;
        opacity: 0.7;
    }
    .chat-input-section {
        position: absolute;
        bottom: 0;
        width: 100%;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    .chat-input {
        background-color: #f8f9fa;
        border: none;
        padding: 12px 20px;
        border-radius: 25px;
    }
    .chat-input:focus {
        background-color: white;
        box-shadow: 0 0 0 2px rgba(255, 73, 0, 0.1);
    }
    .chat-send {
        border-radius: 25px;
        padding: 10px 25px;
        background: linear-gradient(100deg, #ff4900, #ff7433);
        border: none;
    }
    .unread-badge {
        font-size: 10px;
        padding: 4px 7px;
    }
    .avatar-title {
        font-weight: 700;
    }
</style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
<script>
    let activeConversationId = null;
    let isAdminAccount = {{ Auth::user()->user_type === 'admin' ? 'true' : 'false' }};

    function loadConversation(convId, element) {
        // Toggle active class
        const listItems = document.querySelectorAll('#conversationList li');
        listItems.forEach(item => item.classList.remove('active'));
        if (element) element.classList.add('active');

        activeConversationId = convId;
        
        // Clear empty state
        const chatWindow = document.getElementById('chat-messages');
        chatWindow.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"></div></div>';

        // Fetch details via AJAX
        fetch(`{{ url(request()->segment(1)) }}/chat/conversation/${convId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderConversation(data.conversation);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function renderConversation(conversation) {
        document.getElementById('activeChatName').innerText = `${conversation.patient.first_name || ''} ${conversation.patient.last_name || ''}`;
        document.getElementById('activeChatAvatar').innerText = (conversation.patient.first_name || 'P').charAt(0).toUpperCase();
        
        const chatWindow = document.getElementById('chat-messages');
        chatWindow.innerHTML = '';

        if (conversation.messages && conversation.messages.length > 0) {
            conversation.messages.forEach(msg => {
                appendMessage(msg);
            });
            scrollToBottom();
        } else {
            chatWindow.innerHTML = '<div class="text-center mt-5 text-muted">No messages yet. Start the conversation!</div>';
        }
    }

    function appendMessage(msg) {
        const chatWindow = document.getElementById('chat-messages');
        
        // Remove empty state if present
        const emptyState = chatWindow.querySelector('.empty-chat-state');
        if (emptyState) emptyState.remove();

        const isMe = (msg.sender_type === 'admin' || msg.sender_type === 'staff') || (msg.sender_type === 'doctor' && !isAdminAccount);
        // Wait, logic for "isMe" in admin dashboard: any message from admin/staff is on the right.
        const isRight = ['admin', 'staff', 'doctor', 'system'].includes(msg.sender_type);

        const time = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'Just now';
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isRight ? 'message-right' : 'message-left'}`;
        
        let content = `<div class="message-text">${msg.message || ''}</div>`;
        
        if (msg.attachments && msg.attachments.length > 0) {
            msg.attachments.forEach(att => {
                if (att.mime_type.startsWith('image/')) {
                    content += `<div class="message-attachment mt-2">
                        <a href="${att.url}" target="_blank">
                            <img src="${att.url}" class="img-fluid rounded" style="max-height: 200px; cursor: pointer;">
                        </a>
                    </div>`;
                } else {
                    content += `<div class="message-attachment mt-2">
                        <a href="${att.url}" target="_blank" class="btn btn-sm btn-light text-start d-flex align-items-center">
                            <i class="fas fa-file-alt me-2"></i>
                            <span class="text-truncate" style="max-width: 150px;">${att.original_name}</span>
                            <i class="fas fa-download ms-2"></i>
                        </a>
                    </div>`;
                }
            });
        }
        
        messageDiv.innerHTML = `
            ${content}
            <div class="message-time">${time} ${isRight ? '<i class="fas fa-check-double ms-1"></i>' : ''}</div>
        `;
        
        chatWindow.appendChild(messageDiv);
        scrollToBottom();
    }

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            document.getElementById('fileName').innerText = fileName;
            document.getElementById('filePreview').classList.remove('d-none');
        }
    }

    function clearFile() {
        document.getElementById('attachmentInput').value = '';
        document.getElementById('filePreview').classList.add('d-none');
    }

    function scrollToBottom() {
        const chatWindow = document.getElementById('chat-messages');
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    // Polling for new messages
    setInterval(() => {
        if (activeConversationId) {
            fetch(`{{ url(request()->segment(1)) }}/chat/conversation/${activeConversationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const chatWindow = document.getElementById('chat-messages');
                        const currentCount = chatWindow.querySelectorAll('.message').length;
                        const newCount = data.conversation.messages.length;
                        
                        if (newCount > currentCount) {
                            // Append only new messages
                            const newMessages = data.conversation.messages.slice(currentCount);
                            newMessages.forEach(msg => appendMessage(msg));
                            scrollToBottom();
                            // Optional: play notification sound
                        }
                    }
                });
        }
    }, 5000); // Poll every 5 seconds

    document.getElementById('chatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!activeConversationId) {
            alert('Please select a conversation first');
            return;
        }

        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        
        if (!message) return;

        // Send via AJAX
        const formData = new FormData();
        formData.append('conversation_id', activeConversationId);
        formData.append('message', message);
        formData.append('message_type', 'text');
        
        const fileInput = document.getElementById('attachmentInput');
        if (fileInput.files.length > 0) {
            formData.append('attachment', fileInput.files[0]);
        }
        
        formData.append('_token', '{{ csrf_token() }}');

        fetch(`{{ url(request()->segment(1)) }}/chat/send-message`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                appendMessage(data.message);
                input.value = '';
                clearFile();
                // Update last message in sidebar
                const lastMsgElement = document.getElementById(`last-msg-${activeConversationId}`);
                if (lastMsgElement) lastMsgElement.innerText = message || 'Sent an attachment';
            } else {
                alert('Error sending message: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Auto-load first conversation if exists
    window.onload = function() {
        const firstChat = document.querySelector('#conversationList li');
        if (firstChat) {
            firstChat.click();
        }
    };
</script>
@endsection
