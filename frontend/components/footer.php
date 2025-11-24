<!-- Floating Chat Button -->
<div id="chatbot-toggle">💬</div>

<!-- Floating Chat Window -->
<div id="chatbot-box">
    <div class="chatbot-header">
        <span>Symvan Assistant</span>
        <button id="chatbot-close">✖</button>
    </div>

    <div class="chatbot-body" id="chatbotMessages">
        <!-- Initial AI message -->
        <div class="chat-message bot">
            <div class="chat-bubble">
                👋 Hi! I'm the Symvan assistant. How can I help?
            </div>
        </div>
    </div>

    <div class="chatbot-input-row">
        <textarea id="chatbotInput" placeholder="Ask something..."></textarea>
        <button id="chatbotSend">Send</button>
    </div>
</div>

<style>
    #chatbot-toggle {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 60px;
        height: 60px;
        background: #c41230;
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 28px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        z-index: 99999;
    }

    #chatbot-close {
        background: transparent;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        transition: background 0.2s ease, transform 0.15s ease;
    }

    #chatbot-close:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: scale(1.1);
    }

    #chatbot-close:active {
        transform: scale(0.95);
    }


    #chatbot-box {
        position: fixed;
        bottom: 100px;
        right: 25px;
        width: 350px;
        height: 450px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.25);
        display: none;
        flex-direction: column;
        overflow: hidden;
        z-index: 99999;
    }

    .chatbot-header {
        background: #c41230;
        color: white;
        padding: 10px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chatbot-body {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
        background: #fafafa;
    }

    .chatbot-input-row {
        display: flex;
        gap: 8px;
        padding: 10px;
        background: #fff;
        border-top: 1px solid #ddd;
    }

    #chatbotInput {
        flex: 1;
        height: 45px;
        resize: none;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 8px;
    }

    #chatbotSend {
        padding: 0 18px;
        border: none;
        background: #c41230;
        color: white;
        border-radius: 8px;
        cursor: pointer;
    }

    .chat-message {
        margin-bottom: 10px;
        max-width: 80%;
    }

    .chat-message.user {
        margin-left: auto;
        text-align: right;
    }

    .chat-bubble {
        padding: 8px 12px;
        border-radius: 10px;
        display: inline-block;
    }

    .chat-message.bot .chat-bubble {
        background: #EEE;
    }

    .chat-message.user .chat-bubble {
        background: #c41230;
        color: white;
    }
</style>

<script>
    const toggle = document.getElementById("chatbot-toggle");
    const box = document.getElementById("chatbot-box");
    const closeBtn = document.getElementById("chatbot-close");
    const messages = document.getElementById("chatbotMessages");
    const input = document.getElementById("chatbotInput");
    const sendBtn = document.getElementById("chatbotSend");

    toggle.onclick = () => box.style.display = "flex";
    closeBtn.onclick = () => box.style.display = "none";

    function appendMsg(role, text) {
        const wrapper = document.createElement("div");
        wrapper.classList.add("chat-message", role);

        const bubble = document.createElement("div");
        bubble.classList.add("chat-bubble");
        bubble.textContent = text;

        wrapper.appendChild(bubble);
        messages.appendChild(wrapper);
        messages.scrollTop = messages.scrollHeight;
    }

    async function sendMsg() {
        const text = input.value.trim();
        if (!text) return;
        input.value = "";
        appendMsg("user", text);

        appendMsg("bot", "Thinking...");

        const res = await fetch("chatbot.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ prompt: text })
        });

        const data = await res.json();
        messages.lastChild.remove();
        appendMsg("bot", data.reply || "Error.");
    }

    sendBtn.onclick = sendMsg;

    input.addEventListener("keydown", e => {
        if (e.key === "Enter") {
            e.preventDefault();
            sendMsg();
        }
    });
</script>
