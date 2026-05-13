<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Reverb Chat</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message {
            margin-bottom: 15px;
            display: flex;
            animation: slideIn 0.3s ease-out;
        }

        .message.own {
            justify-content: flex-end;
        }

        .message-content {
            padding: 10px 15px;
            border: 3px solid #000000;
            word-wrap: break-word;
            font-weight: 600;
            background: #fce132;
            color: #000000;
        }

        .message.own .message-content {
            background: #fd6f57;
            color: #000000;
        }

        .message-time {
            font-size: 10px;
            color: #000000;
            margin-top: 5px;
            display: block;
            font-weight: bold;
        }

        .message-user {
            font-size: 11px;
            font-weight: 900;
            color: #000000;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .user-badge {
            display: inline-block;
            background: #3d9028;
            color: #ffffff;
            padding: 6px 10px;
            margin: 2px;
            font-size: 11px;
            border: 2px solid #000000;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body class="font-mono bg-[#c79efa] min-h-screen flex justify-center items-center p-5">
    <div class="w-full max-w-md">
        <!-- Login Form -->
        <div id="loginForm" class="bg-white border-4 border-black p-8 text-center {{ auth()->check() ? 'hidden' : '' }}">
            <h1 class="text-4xl font-black text-black mb-5 tracking-widest">REAL-TIME CHAT</h1>
            <p class="text-gray-600 mb-5 font-bold">Pilih user untuk mulai chat</p>
            <form method="POST" action="{{ route('chat.login') }}">
                @csrf
                <select name="user_id" class="w-full p-3 my-4 border-4 border-black bg-white font-bold text-base cursor-pointer" required>
                    <option value="" selected hi>-- Pilih User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full p-4 bg-black text-white border-4 border-black mt-3 font-black uppercase tracking-wide">Login</button>
            </form>
            @if($errors->any())
                <div class="bg-[#ceff4e] text-white p-3 my-4 border-4 border-black text-sm font-bold">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Chat Area -->
        <div id="chatContainer" class="bg-[#fefbea] border-4 border-black h-150 flex flex-col {{ auth()->check() ? '' : 'hidden' }}">
            <div class="bg-[#ceff4e] text-black px-5 py-5 border-b-4 border-black font-black text-xl flex justify-between items-center uppercase tracking-wider">
                <div class="flex gap-3">
                     <span id="currentUser">KAMU {{ auth()->user()->name ?? 'User' }}</span>
                </div>
                <form action="/logout" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-black text-white border-4 border-white px-3 py-1 cursor-pointer text-xs font-bold uppercase hover:bg-white hover:text-black transition">Logout</button>
                </form>
            </div>

            <div class="flex-1 overflow-y-auto p-5 bg-[#fefbea] border-b-4 border-black" id="messagesContainer"></div>

            <div class="px-5 py-4 bg-[#ceff4e] border-t border-black text-sm font-bold flex items-center gap-2">
                Online Users:
                <div id="onlineUsers"></div>
            </div>

            <div class="p-5 border-t-4 border-black flex gap-3">
                <input type="text"
                    id="messageInput"
                    placeholder="Ketik pesan..."
                    rows="1"
                    maxlength="500"
                    class="flex-1 p-4 border-4 border-black bg-white font-mono font-bold text-base resize-none"
                ></input>
                <button id="sendBtn" onclick="sendMessage()" class="px-5 py-3 bg-[#ceff4e] text-black border-4 border-black cursor-pointer font-black uppercase tracking-wide">KIRIM</button>
            </div>
        </div>
    </div>

    @auth
        <script>
            const messageInput = document.getElementById('messageInput');

            // Auto-resize textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 100) + 'px';
            });

            // Send message on Enter key
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Fetch existing messages
            function loadMessages() {
                fetch('/messages')
                    .then(r => r.json())
                    .then(messages => {
                        const container = document.getElementById('messagesContainer');
                        container.innerHTML = '';
                        messages.forEach(msg => appendMessage(msg));
                        container.scrollTop = container.scrollHeight;
                    });
            }

            // Append message to chat
            function appendMessage(msg) {
                const container = document.getElementById('messagesContainer');
                const isOwn = msg.user.id === {{ auth()->id() }};
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${isOwn ? 'own' : 'other'}`;

                const timeString = new Date(msg.created_at).toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                messageDiv.innerHTML = `
                    <div>
                        ${!isOwn ? `<div class="message-user">${msg.user.name}</div>` : ''}
                        <div class="message-content">
                            ${msg.body}
                            <span class="message-time">${timeString}</span>
                        </div>
                    </div>
                `;
                container.appendChild(messageDiv);
                container.scrollTop = container.scrollHeight;
            }

            // Send message
            function sendMessage() {
                const body = messageInput.value.trim();
                if (!body) return;

                fetch('/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ body })
                })
                .then(r => r.json())
                .then(() => {
                    messageInput.value = '';
                    messageInput.style.height = 'auto';
                });
            }

            // Initialize Echo listeners
            function initEcho() {
                // Listen to real-time messages
                window.Echo.channel('chat-channel')
                    .listen('.message.sent', (data) => {
                        appendMessage(data);
                    });

                // Listen to presence channel for online users
                window.Echo.join('chat-presence')
                    .here(users => updateOnlineUsers(users))
                    .joining(user => updatePresence())
                    .leaving(user => updatePresence());
            }

            // Update online users list
            function updatePresence() {
                window.Echo.join('chat-presence').here(users => updateOnlineUsers(users));
            }

            function updateOnlineUsers(users) {
                const container = document.getElementById('onlineUsers');
                container.innerHTML = '';
                if (users && users.length > 0) {
                    users.forEach(user => {
                        const badge = document.createElement('span');
                        badge.className = 'user-badge';
                        badge.textContent = user.name;
                        container.appendChild(badge);
                    });
                }
            }

            // Initialize on load
            loadMessages();
            setTimeout(() => window.Echo && initEcho(), 500);
        </script>
    @endauth
</body>
</html>
