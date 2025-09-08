<!-- resources/views/components/chatbot.blade.php -->

<div class="chat-popup card shadow" id="chatWindow">
    <div class="card shadow h-100 w-100">
        <div class="card-header custom-green text-white d-flex justify-content-between align-items-center">
            <span>Chat with Gemini AI</span>
            <button class="btn btn-sm btn-light" onclick="toggleChat()">✖</button>
        </div>
        <div class="card-body chat-container" id="chatBody">
            @foreach ($chatHistory as $msg)
                @php
                    $userMessage = is_array($msg) ? $msg['user_message'] ?? null : $msg->user_message;
                    $botReply = is_array($msg) ? $msg['bot_reply'] ?? null : $msg->bot_reply;
                @endphp

                @if ($userMessage)
                    <div class="message user-message text-end">
                        {!! nl2br(e($userMessage)) !!}
                    </div>
                @endif

                @if ($botReply)
                    <div class="message bot-message">
                        {!! nl2br(e($botReply)) !!}
                    </div>
                @endif
            @endforeach


            @if (session('typing'))
                <div class="message bot-message typing">
                    Gemini is typing...
                </div>
            @endif
        </div>

        <div class="card-footer">
            <form id="chatForm">
                @csrf
                <div class="input-group">
                    <input type="text" name="message" class="form-control" placeholder="Type your message..."
                        required autocomplete="off">
                    <button type="submit" class="btn custom-green text-white">Send</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 👇 Custom resizers -->
    <div class="resize-handle resize-handle-top"></div>
    <div class="resize-handle resize-handle-left"></div>
</div>

<button class="btn btn-primary rounded-pill chat-toggle" onclick="toggleChat()">💬 Chat with Gemini</button>

<style>
    
</style>

<!-- ✅ Load Marked.js for Markdown parsing -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
    function toggleChat() {
        const chat = document.getElementById('chatWindow');
        chat.style.display = chat.style.display === 'none' || chat.style.display === '' ? 'block' : 'none';
    }

    const chatBody = document.getElementById('chatBody');
    const form = document.getElementById('chatForm');

    function scrollToBottom() {
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const input = form.querySelector('input[name="message"]');
        const userMessage = input.value.trim();

        if (userMessage === '') return;

        // Add user message
        const userMsg = document.createElement('div');
        userMsg.className = 'message user-message text-end';
        userMsg.textContent = userMessage;
        chatBody.appendChild(userMsg);

        input.value = '';

        // Add typing indicator
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot-message typing';
        typingDiv.textContent = 'Gemini is typing...';
        typingDiv.id = 'typing-indicator';
        chatBody.appendChild(typingDiv);

        scrollToBottom();

        try {
            const response = await fetch("/send", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                        "content"),
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify({
                    message: userMessage
                })
            });

            const data = await response.json();

            document.getElementById('typing-indicator').remove();

            // ✅ Render bot reply with Markdown
            const botMsg = document.createElement('div');
            botMsg.className = 'message bot-message';
            botMsg.innerHTML = marked.parse(data.reply);
            chatBody.appendChild(botMsg);

            scrollToBottom();

        } catch (error) {
            console.error('Error sending message:', error);
            document.getElementById('typing-indicator').textContent = "Error: Could not get reply.";
        }
    });

    window.onload = () => {
        if (@json(count($chatHistory) > 0)) {
            document.getElementById('chatWindow').style.display = 'block';
            scrollToBottom();
        }
    };

    // 👇 Custom resizer logic
    const chatWindow = document.getElementById('chatWindow');
    let isResizing = false;
    let startX, startY, startWidth, startHeight;

    function initResize(e, direction) {
        e.preventDefault();
        isResizing = direction;
        startX = e.clientX;
        startY = e.clientY;
        startWidth = chatWindow.offsetWidth;
        startHeight = chatWindow.offsetHeight;

        window.addEventListener('mousemove', doResize);
        window.addEventListener('mouseup', stopResize);
    }

    function doResize(e) {
        if (!isResizing) return;

        if (isResizing === 'left') {
            const newWidth = startWidth + (startX - e.clientX);
            if (newWidth > 200) {
                chatWindow.style.width = newWidth + 'px';
            }
        }

        if (isResizing === 'top') {
            const newHeight = startHeight + (startY - e.clientY);
            if (newHeight > 200) {
                chatWindow.style.height = newHeight + 'px';
            }
        }
    }

    function stopResize() {
        isResizing = false;
        window.removeEventListener('mousemove', doResize);
        window.removeEventListener('mouseup', stopResize);
    }

    document.querySelector('.resize-handle-top').addEventListener('mousedown', e => initResize(e, 'top'));
    document.querySelector('.resize-handle-left').addEventListener('mousedown', e => initResize(e, 'left'));
</script>
