@extends('layouts.master')

@include('layouts.header')

@section('content')
<style>
    /* Remove full-screen vertical and horizontal scrolling */
    body, html {
        margin: 0;
        padding: 0;
        overflow: hidden; /* Prevent global scrolling */
    }

    .container-fluid {
        height: 100vh; /* Full height container */
        display: flex;
        flex-direction: column;
    }

    .chat-container {
        flex-grow: 1;
        display: flex;
        overflow: hidden; /* Prevent horizontal scrolling */
    }

    /* Sidebar */
    .chat-sidebar {
        overflow-y: auto;
        border-right: 1px solid #ddd;
        height: 100%; /* Sidebar takes full height */
    }

    .chat-sidebar .list-group-item {
        border: none;
        cursor: pointer;
    }

    .chat-sidebar .list-group-item:hover {
        background-color: #f5f5f5;
    }

    /* Sidebar Scrollbar */
    .chat-sidebar::-webkit-scrollbar {
        width: 8px;
    }

    .chat-sidebar::-webkit-scrollbar-track {
        background: #f5f5f5;
        border-radius: 4px;
    }

    .chat-sidebar::-webkit-scrollbar-thumb {
        background: #a9a9a9;
        border-radius: 4px;
    }

    .chat-sidebar::-webkit-scrollbar-thumb:hover {
        background: #888;
    }

    /* Chat Content */
    .chat-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden; /* Prevent overflow in chat content */
    }

    .chat-header {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    .chat-message {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }

    .chat-messages {
        flex-grow: 1;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 10px;
        box-sizing: border-box;
    }

    .chat-messages::-webkit-scrollbar {
        width: 8px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: #f5f5f5;
        border-radius: 4px;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #a9a9a9;
        border-radius: 4px;
    }

    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #888;
    }

    .chat-message {
        margin-bottom: 10px;
    }

    .chat-message.sent {
        justify-content: flex-end;
    }

    .message-content {
        max-width: 60%;
        padding: 10px;
        border-radius: 10px;
        background-color: #f0f0f0; /* Default background for received messages */
        word-wrap: break-word;
    }

    .chat-message.sent .message-content {
        background-color: #e6e6fa; /* Background for sent messages */
        text-align: right;
    }

    .chat-message.received {
        justify-content: flex-start; /* Align received messages to the left */
    }

    .chat-message.received .message-content {
        background-color: #f0f0f0; /* Background for received messages */
        text-align: left;
    }

    .message-content img {
        max-width: 300px; /* Ensures the image does not exceed the width of the container */
        height: auto; /* Maintains the aspect ratio */
        border-radius: 10px;
        margin-top: 5px;
        display: block; /* Ensures no inline spacing */
        overflow: hidden; /* Prevents overflow */
    }

    .chat-input {
        border-top: 1px solid #ddd;
        padding: 10px;
        display: flex;
        align-items: center;
    }

    .chat-input form {
        display: flex;
        align-items: center;
        width: 100%;
    }

    .chat-input input[type="file"] {
        display: none; /* Hide the file input */
    }

    .chat-input .btn-outline-secondary {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
        border-radius: 50%; /* Make the button circular */
    }

    /* Spinner */
    .loading-spinner {
        display: none;
        justify-content: center;
        align-items: center;
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        z-index: 10;
    }

    .date-separator {
        position: relative;
        text-align: center;
        font-size: 0.9rem;
        color: #6c757d;
        margin: 20px 0;
    }

    .date-separator::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        width: 45%;
        height: 1px;
        background-color: #ddd;
        transform: translateY(-50%);
    }

    .date-separator::after {
        content: "";
        position: absolute;
        top: 50%;
        right: 0;
        width: 45%;
        height: 1px;
        background-color: #ddd;
        transform: translateY(-50%);
    }

    .unread-count {
        display: inline-block;
        background-color: red;
        color: white;
        font-size: 12px;
        font-weight: bold;
        border-radius: 30%;
        width: 15px;
        height: 20px;
        text-align: center;
        line-height: 20px;
        margin-left: 5px;
    }

</style>

<div class="container-fluid">
    <div class="row chat-container" style="padding-bottom:60px">
        <!-- Sidebar -->
        <div class="col-md-4 chat-sidebar">
            <div class="list-group">
                <div class="chat-header">
                    <h5 class="mb-0">Chats</h5>
                </div>
                <div id="channels">
                    @foreach (collect($message)->sortByDesc('unread_count') as $chat)
                        @php
                            $profilePath = 'storage/images/profile_photos/' . $chat->user_id . '_' . $chat->user_name . '.png';
                            $defaultImage = asset('images/not_found.jpg');
                        @endphp
                        <a href="#" class="list-group-item d-flex align-items-center" data-id="{{ $chat->user_id }}" onclick="loadChat({{ $chat->user_id }}, '{{ $chat->user_name }}', '{{ $chat->channelId }}', '{{ file_exists(public_path($profilePath)) ? asset($profilePath) : $defaultImage }}')">
                            <div class="me-3">
                                <img src="{{ file_exists(public_path($profilePath)) ? asset($profilePath) : $defaultImage }}" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px;">
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $chat->user_name }}
                                    @if ($chat->unread_count > 0)
                                        <span class="unread-count">{{ $chat->unread_count }}</span>
                                    @endif
                                </h6>
                                <small class="text-muted">{{ $chat->last_message }}</small>
                            </div>
                            <small class="text-muted ms-auto"> {{ \Carbon\Carbon::parse(($chat->last_message_at), 'Asia/Jakarta')->timezone('Asia/Jakarta')->locale('id')->diffForHumans() }}</small>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Chat Content -->
        <div class="col-md-8 chat-content position-relative">
            <!-- Loading Spinner -->
            <div id="loading-spinner" style="display:none">
                <div class="loading-spinner d-flex">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

            <!-- Chat Header -->
            <div class="chat-header d-flex align-items-center p-3 border-bottom">
                <div class="me-3">
                    <img id="chatAvatar" src="{{ asset('images/not_found.jpg')}}" alt="Avatar" class="rounded-circle" style="width: 50px; height: 50px;">
                </div>
                <h5 id="chatName" class="mb-0">Pilih Percakapan</h5>
            </div>

            <!-- Chat Messages -->
            <div class="text-center" id="contextText">Pilih Percakapan terlebih dahulu</div>
            <div id="chatMessages" class="chat-messages"></div>

            <!-- Chat Input -->
            <div class="chat-input">
                <form id="sendMessageForm" class="d-flex align-items-center">
                    <label for="imageInput" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; margin-right: 5px;margin-top:5px">
                        <i class="bi bi-plus"></i>
                    </label>
                    <input type="file" id="imageInput" class="d-none" accept="image/*" onchange="handleImageSend(event)" disabled>
                    <input type="text" id="messageInput" class="form-control" placeholder="Ketikkan pesan..." disabled>
                    <button class="btn btn-primary ms-2" id="messageButton" type="submit" disabled>Kirim</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var user_id = '';
    var user_name = '';
    var channel_id = '';
    let currentDate = null;
    let channel = null;
    let client = null;
    let messagesContainer = null;

    (async function initializeStreamChat() {
        console.log("StreamChat initialized");

        // Initialize Stream client
        client = new StreamChat('{{ config('stream.api_key') }}');
        await client.connectUser(
            {
                id: '{{ auth()->user()->id }}',
                name: '{{ auth()->user()->name }}',
                email: '{{ auth()->user()->email }}',
            },
            '{{ $userToken }}'
        );

        user_id = '{{ auth()->user()->id }}';
        const filter = { type: "messaging", members: { $in: [user_id] } };
        const sort = { last_message_at: -1 };
        const channels = await client.queryChannels(filter, sort, { watch: true });

        listenForNewMessages();

        refreshSidebar();
    })();

    function listenForNewMessages() {
        client.on('message.new', (event) => {
            refreshSidebar(); // Refresh the sidebar when any new message is received
        });
    }

    function showLoadingSpinner() {
        const spinner = document.getElementById('loading-spinner');
        if (spinner) spinner.style.display = 'flex';
    }

    function hideLoadingSpinner() {
        const spinner = document.getElementById('loading-spinner');
        if (spinner) spinner.style.display = 'none';
    }

    const autoload = {{ $autoload ? 'true' : 'false' }};
    const chatData = {
        userId: {{ $data['user_id'] ?? 'null' }},
        userName: '{{ $data['user_name'] ?? '' }}',
        channelId: '{{ $data['channelId'] ?? '' }}',
        profilePicture: '{{ $data['profilePicture'] ?? '' }}',
    };

    if (autoload && chatData.userId && chatData.channelId) {
        loadChat(chatData.userId, chatData.userName, chatData.channelId, chatData.profilePicture);
    }

    async function loadChat(userId, userName, channelId, profilePicture) {
        const contextText = document.getElementById('contextText');
        const chatName = document.getElementById('chatName');
        const chatAvatar = document.getElementById('chatAvatar');
        messagesContainer = document.getElementById('chatMessages');
        const imageInput = document.getElementById('imageInput');
        const messageInput = document.getElementById('messageInput');
        const messageButton = document.getElementById('messageButton');
        currentDate = null;

        refreshSidebar();

        localStorage.setItem('lastChat', JSON.stringify({ userId, userName, channelId, profilePicture }));

        messagesContainer.innerHTML = ''; // Clear previous messages

        chatName.textContent = userName;
        chatAvatar.src = profilePicture;
        contextText.style.display = 'none';

        imageInput.disabled = false;
        messageInput.disabled = false;
        messageButton.disabled = false;

        channel = client.channel('messaging', channelId);
        await channel.watch();
        await channel.markRead();

        channel.state.messages.forEach((message) => {
            renderMessage(message, messagesContainer, '{{ auth()->user()->id }}');
        });

        scrollToBottom();

        channel.on('message.new', (event) => {
            renderMessage(event.message, messagesContainer, '{{ auth()->user()->id }}');
            scrollToBottom();
        });

        document.getElementById('sendMessageForm').onsubmit = async (e) => {
            e.preventDefault();
            const text = messageInput.value.trim();
            if (text) {
                await channel.markRead();
                await channel.sendMessage({ text });
                messageInput.value = '';
                scrollToBottom();
            }
        };
    }

    async function handleImageSend(event) {
        const fileInput = event.target;
        const file = fileInput.files[0];

        if (file) {
            try {
                if (channel) {
                    const fileUrl = await channel.sendFile(file);
                    await channel.sendMessage({
                        attachments: [
                            {
                                type: 'image',
                                image_url: fileUrl.file,
                            },
                        ],
                        user_id: '{{ auth()->user()->id }}',
                    });
                    scrollToBottom();
                } else {
                    console.error('Channel is not defined!');
                }

                fileInput.value = '';
            } catch (error) {
                console.error('Error uploading file:', error);
            }
        }
    }

    function renderMessage(message, container, currentUserId) {
        const messageDate = new Date(message.created_at).toISOString().split('T')[0];

        if (currentDate != messageDate) {
            currentDate = messageDate;

            const dateSeparator = document.createElement('div');
            dateSeparator.className = 'date-separator';
            dateSeparator.textContent = new Date(messageDate).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });

            container.appendChild(dateSeparator);
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${message.user.id == currentUserId ? 'sent' : 'received'}`;

        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';

        if (message.attachments && message.attachments.length > 0) {
            message.attachments.forEach((attachment) => {
                if (attachment.type === 'image') {
                    const img = document.createElement('img');
                    img.src = attachment.image_url;
                    img.alt = 'Image attachment';
                    img.style.maxWidth = '300px';
                    img.style.borderRadius = '10px';
                    contentDiv.appendChild(img);
                }
            });
        } else if (message.text) {
            const textNode = document.createTextNode(message.text);
            contentDiv.appendChild(textNode);
        }

        const timestamp = document.createElement('small');
        timestamp.className = 'text-muted ms-2';
        timestamp.style.fontSize = '0.8em';
        timestamp.textContent = new Date(message.created_at).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
        });
        contentDiv.appendChild(timestamp);

        messageDiv.appendChild(contentDiv);
        container.appendChild(messageDiv);
    }

    async function refreshSidebar() {
        try {
            const response = await fetch('{{ route('chat.getChannels') }}'); // Replace with your backend route
            const channels = await response.json();
            renderSidebar(channels);
        } catch (error) {
            console.error('Error fetching updated channels:', error);
        }
    }

    function renderSidebar(channels) {
        const channelsContainer = document.getElementById('channels');
        channelsContainer.innerHTML = '';

        channels.forEach((chat) => {
            const channelHtml = `
                <a href="#" class="list-group-item d-flex align-items-center" data-id="${chat.user_id}" onclick="loadChat(${chat.user_id}, '${chat.user_name}', '${chat.channelId}', '${chat.profilePicture}')">
                    <div class="me-3">
                        <img src="${chat.profilePicture}" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px;">
                    </div>
                    <div>
                        <h6 class="mb-0">${chat.user_name} ${chat.unread_count > 0 ? `<span class="unread-count">${chat.unread_count}</span>` : ''}</h6>
                        <small class="text-muted">${chat.last_message}</small>
                    </div>
                    <small class="text-muted ms-auto">${new Date(chat.last_message_at).toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                    })}</small>
                </a>
            `;
            channelsContainer.innerHTML += channelHtml;
        });
    }

    function scrollToBottom() {
        if (messagesContainer) {
            const images = messagesContainer.querySelectorAll('img');
            const promises = Array.from(images).map((img) => {
                return new Promise((resolve) => {
                    if (img.complete) {
                        resolve();
                    } else {
                        img.onload = resolve;
                        img.onerror = resolve;
                    }
                });
            });

            Promise.all(promises).then(() => {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            });
        }
    }

    const container = document.getElementById('chatMessages');
    container.setAttribute('tabindex', '0');

    container.addEventListener('focus', () => {
        markAsRead();
    });

    async function markAsRead() {
        if(channel) {
            await channel.markRead();
            refreshSidebar();
        }
    }
</script>


@endsection
