<style>
    :root {
        --cb-primary: #FF6B00;
        --cb-bg: #1A1A1A;
        --cb-msg-bot: #333333;
        --cb-text-light: #FFFFFF;
        --cb-text-dark: #000000;
        --cb-border: #444444;
    }

    #chatbot-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    #chatbot-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2A2A2A 0%, var(--cb-primary) 100%);
        color: var(--cb-text-light);
        border: 2px solid var(--cb-border);
        box-shadow: 0 4px 15px rgba(255, 107, 0, 0.4);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: transform 0.3s ease;
    }

    #chatbot-btn:hover {
        transform: scale(1.1);
    }

    #chatbot-window {
        display: none;
        flex-direction: column;
        width: 300px;
        height: 420px;
        max-height: 80vh;
        background-color: var(--cb-bg);
        border: 1px solid var(--cb-border);
        border-radius: 12px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        margin-bottom: 15px;
    }

    #chatbot-window.open {
        display: flex;
    }

    #cb-header {
        background-color: var(--cb-primary);
        color: var(--cb-text-light);
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: bold;
    }

    #cb-close {
        background: none;
        border: none;
        color: var(--cb-text-light);
        font-size: 20px;
        cursor: pointer;
    }

    #cb-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        scrollbar-width: thin;
        scrollbar-color: var(--cb-primary) var(--cb-bg);
    }

    #cb-messages::-webkit-scrollbar {
        width: 6px;
    }
    #cb-messages::-webkit-scrollbar-thumb {
        background-color: var(--cb-primary);
        border-radius: 3px;
    }

    .cb-msg {
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 15px;
        font-size: 14px;
        line-height: 1.4;
    }

    .cb-msg.bot {
        background-color: var(--cb-msg-bot);
        color: var(--cb-text-light);
        align-self: flex-start;
        border-bottom-left-radius: 2px;
        white-space: pre-line;
    }

    .cb-msg.user {
        background-color: var(--cb-primary);
        color: var(--cb-text-light);
        align-self: flex-end;
        border-bottom-right-radius: 2px;
    }

    .cb-suggest-btn {
        background: transparent;
        border: 1px solid var(--cb-primary);
        color: var(--cb-primary);
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 13px;
        cursor: pointer;
        margin-top: 5px;
        display: inline-block;
        transition: all 0.2s;
        text-align: left;
    }

    .cb-suggest-btn:hover {
        background-color: var(--cb-primary);
        color: var(--cb-text-dark);
    }

    #cb-input-area {
        display: flex;
        padding: 10px;
        border-top: 1px solid var(--cb-border);
        background-color: var(--cb-bg);
    }

    #cb-input {
        flex: 1;
        background-color: #2A2A2A;
        border: 1px solid #444;
        color: white;
        padding: 10px 15px;
        border-radius: 20px;
        outline: none;
    }

    #cb-input::placeholder {
        color: #888;
    }

    #cb-send {
        background-color: var(--cb-primary);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #cb-send:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .cb-loading {
        align-self: flex-start;
        display: inline-flex;
        align-items: center;
        background-color: var(--cb-msg-bot);
        padding: 10px 15px;
        border-radius: 15px;
        border-bottom-left-radius: 2px;
        margin-top: 5px;
    }

    .cb-dot {
        width: 6px;
        height: 6px;
        background-color: var(--cb-text-light);
        border-radius: 50%;
        margin: 0 3px;
        animation: cb-bounce 1.4s infinite ease-in-out both;
    }
    .cb-dot:nth-child(1) { animation-delay: -0.32s; }
    .cb-dot:nth-child(2) { animation-delay: -0.16s; }
    @keyframes cb-bounce {
        0%, 80%, 100% { transform: scale(0); opacity: 0.3; }
        40% { transform: scale(1); opacity: 1; }
    }
</style>

<div id="chatbot-widget">
    <div id="chatbot-window">
        <div id="cb-header">
            <span>AI Trợ lý Rạp phim</span>
            <button id="cb-close">&times;</button>
        </div>
        <div id="cb-messages">
            <div class="cb-msg bot">
                Xin chào! Tôi là Trợ lý AI của rạp. Tôi có thể giúp gì cho bạn?
            </div>
            <div class="cb-suggest-container" style="display: flex; flex-direction: column; gap: 5px; align-items: flex-start;">
                <button class="cb-suggest-btn">Phim nào đang hot nhất?</button>
                <button class="cb-suggest-btn">Lịch chiếu hôm nay thế nào?</button>
            </div>
            <!-- Messages go here -->
        </div>
        <div id="cb-input-area">
            <input type="text" id="cb-input" placeholder="Nhập câu hỏi của bạn..." autocomplete="off">
            <button id="cb-send">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
                </svg>
            </button>
        </div>
    </div>
    <button id="chatbot-btn">
        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
            <path d="M5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
            <path d="m2.165 15.803.02-.004c1.83-.363 2.948-.842 3.468-1.105A9.06 9.06 0 0 0 8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6a10.437 10.437 0 0 1-.524 2.318l-.003.011a10.722 10.722 0 0 1-.244.637c-.079.186.074.394.273.362a21.673 21.673 0 0 0 .693-.125zm.8-3.108a1 1 0 0 0-.287-.801C1.618 10.83 1 9.468 1 8c0-3.192 3.004-6 7-6s7 2.808 7 6-3.004 6-7 6a8.06 8.06 0 0 1-2.088-.272 1 1 0 0 0-.711.074c-.387.196-1.24.57-2.634.893a10.97 10.97 0 0 0 .398-2z"/>
        </svg>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('chatbot-btn');
        const windowDiv = document.getElementById('chatbot-window');
        const closeBtn = document.getElementById('cb-close');
        const messagesDiv = document.getElementById('cb-messages');
        const inputField = document.getElementById('cb-input');
        const sendBtn = document.getElementById('cb-send');

        // Toggle window
        btn.addEventListener('click', () => {
            windowDiv.classList.add('open');
            btn.style.display = 'none';
        });

        closeBtn.addEventListener('click', () => {
            windowDiv.classList.remove('open');
            btn.style.display = 'flex';
        });

        // Add event listener to initial suggestions
        document.querySelectorAll('.cb-suggest-container .cb-suggest-btn').forEach(btn => {
            btn.addEventListener('click', () => sendMessage(btn.innerText));
        });

        // Send message
        const sendMessage = async (text) => {
            if (!text.trim()) return;

            // Xóa tất cả các nút suggest hiện tại
            document.querySelectorAll('.cb-suggest-container').forEach(el => el.remove());

            // Add user message
            appendMessage(text, 'user');
            inputField.value = '';
            
            // Add loading (3 chấm)
            const loadingId = 'loading-' + Date.now();
            const loadingHtml = `
                <div id="${loadingId}" class="cb-loading">
                    <div class="cb-dot"></div>
                    <div class="cb-dot"></div>
                    <div class="cb-dot"></div>
                </div>`;
            messagesDiv.insertAdjacentHTML('beforeend', loadingHtml);
            scrollToBottom();

            try {
                const response = await fetch('{{ route("chatbot.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: text })
                });

                document.getElementById(loadingId)?.remove();

                if (!response.ok) throw new Error('API Error');
                
                const data = await response.json();
                handleBotResponse(data.reply);

            } catch (error) {
                document.getElementById(loadingId)?.remove();
                appendMessage('Xin lỗi, đã có lỗi xảy ra khi kết nối. Vui lòng thử lại sau.', 'bot');
            }
        };

        const handleBotResponse = (replyText) => {
            // Xóa dấu ** (markdown bold) nếu AI lỡ sinh ra
            replyText = replyText.replace(/\*\*/g, '');

            // Tách các thẻ [SUGGEST]
            const lines = replyText.split('\n');
            let mainText = '';
            let suggests = [];

            lines.forEach(line => {
                if (line.includes('[SUGGEST]')) {
                    suggests.push(line.replace('[SUGGEST]', '').trim());
                } else {
                    mainText += line + '\n';
                }
            });

            appendMessage(mainText.trim(), 'bot');

            if (suggests.length > 0) {
                // Thêm một div chứa các nút suggest
                const suggestContainer = document.createElement('div');
                suggestContainer.className = 'cb-suggest-container';
                suggestContainer.style.display = 'flex';
                suggestContainer.style.flexDirection = 'column';
                suggestContainer.style.gap = '5px';
                suggestContainer.style.alignItems = 'flex-start';

                suggests.forEach(sug => {
                    if(!sug) return;
                    const btn = document.createElement('button');
                    btn.className = 'cb-suggest-btn';
                    btn.innerText = sug;
                    btn.onclick = () => sendMessage(sug);
                    suggestContainer.appendChild(btn);
                });
                
                messagesDiv.appendChild(suggestContainer);
                scrollToBottom();
            }
        };

        const appendMessage = (text, sender) => {
            const div = document.createElement('div');
            div.className = `cb-msg ${sender}`;
            div.innerText = text;
            messagesDiv.appendChild(div);
            scrollToBottom();
        };

        const scrollToBottom = () => {
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        };

        // Event listeners
        sendBtn.addEventListener('click', () => sendMessage(inputField.value));
        inputField.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage(inputField.value);
        });
    });
</script>
