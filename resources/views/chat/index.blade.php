<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>AI Chatbot</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; background:#f5f5f5; }
        #chat-box { border: 1px solid #ccc; background:#fff; height: 400px; overflow-y: auto; padding: 10px; margin-bottom: 10px; border-radius: 8px; }
        .msg-user { text-align: right; color: #fff; background:#007bff; padding:6px 10px; border-radius:10px; display:inline-block; margin:5px 0; float:right; clear:both; }
        .msg-ai { text-align: left; color:#000; background:#e9e9e9; padding:6px 10px; border-radius:10px; display:inline-block; margin:5px 0; float:left; clear:both; }
        #chat-form { display: flex; gap: 5px; }
        #message { flex: 1; padding: 8px; }
        button { padding: 8px 16px; }
    </style>
</head>
<body>
    <h2>AI Chatbot</h2>
    <div id="chat-box"></div>

    <form id="chat-form">
        <input type="text" id="message" placeholder="Ketik pesan..." autocomplete="off" required>
        <button type="submit">Kirim</button>
    </form>

    <script>
        const form = document.getElementById('chat-form');
        const chatBox = document.getElementById('chat-box');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = document.getElementById('message');
            const message = input.value;
            appendMessage('user', message);
            input.value = '';
            appendMessage('ai', '...mengetik...', 'typing');

            const res = await fetch("{{ route('chat.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ message }),
            });

            document.getElementById('typing')?.remove();
            const data = await res.json();
            appendMessage('ai', data.reply ?? data.error);
        });

        function appendMessage(sender, text, id = null) {
            const div = document.createElement('div');
            div.className = sender === 'user' ? 'msg-user' : 'msg-ai';
            if (id) div.id = id;
            div.style.width = '100%';
            div.style.display = 'block';
            div.textContent = text;
            const wrapper = document.createElement('div');
            wrapper.style.overflow = 'auto';
            wrapper.appendChild(div);
            chatBox.appendChild(wrapper);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>
</body>
</html>