<?php

session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}



error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php'; // <-- corrected path

// load .env
$dotenv = Dotenv\Dotenv::createImmutable('/var/www/');
$dotenv->load();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");

    $apiKey = $_ENV['OPENAI_API_KEY'];
    $input = json_decode(file_get_contents('php://input'), true);
    $userPrompt = $input['prompt'] ?? '';

    if (!$userPrompt) {
        echo json_encode(['error' => 'No prompt provided']);
        exit;
    }

    $data = [
        "model" => "gpt-4o-mini",
        "messages" => [
            ["role" => "system", "content" => "You are Symvan, an event assistant. You must ALWAYS keep replies short, less than 200 characters with NO EXCEPTIONS."],
            ["role" => "user", "content" => $userPrompt]
        ],
        "temperature" => 0.7
    ];

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey"
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo json_encode(['error' => curl_error($ch)]);
        exit;
    }
    curl_close($ch);

    $result = json_decode($response, true);

    // extract model text
    $reply = $result['choices'][0]['message']['content'] ?? "No response";

    // *** ABSOLUTE HARD LIMIT HERE ***
    $reply = mb_substr($reply, 0, 200);

    echo json_encode(['reply' => $reply]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Planning Chatbot - Symvan</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* === Chatbot-specific layout additions (works with your main stylesheet) === */
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 75vh;
            background-color: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: var(--spacing-md);
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            margin-bottom: var(--spacing-md);
            padding-right: var(--spacing-sm);
        }

        .chat-message {
            margin-bottom: var(--spacing-sm);
            max-width: 80%;
        }

        .chat-message.bot {
            align-self: flex-start;
        }

        .chat-message.user {
            align-self: flex-end;
            text-align: right;
        }

        .chat-bubble {
            display: inline-block;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            font-size: 1rem;
        }

        .chat-message.bot .chat-bubble {
            background-color: var(--very-light-grey);
            color: var(--dark-grey);
            border: 1px solid var(--light-grey);
        }

        .chat-message.user .chat-bubble {
            background-color: var(--cardinal-red);
            color: var(--white);
        }

        .chat-input-row {
            display: flex;
            gap: var(--spacing-sm);
        }

        .chat-input-row textarea {
            flex: 1;
            padding: var(--spacing-sm);
            border-radius: var(--radius-md);
            border: 2px solid var(--light-grey);
            resize: none;
            font-size: 1rem;
            height: 60px;
        }

        .chat-input-row textarea:focus {
            border-color: var(--cardinal-red);
            outline: none;
        }

        .chat-sidebar {
            background-color: var(--white);
            padding: var(--spacing-md);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            height: fit-content;
        }

        .quick-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-sm);
            margin-top: var(--spacing-sm);
        }

        .quick-buttons button {
            font-size: 0.9rem;
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--radius-md);
            cursor: pointer;
        }

        @media (max-width: 900px) {
            .chat-layout {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
        
    <!-- ===================================
        NAVIGATION BAR
        =================================== -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">Symvan</a>
            <ul class="navbar-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="myevents.php">My Events</a></li>
                <li><a href="enroll.php">Enroll</a></li>
                <li><a href="organization.php">Organizations</a></li>
                <li><a href="create_event.php" class="active">Create Event</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
            <div class="user-session">
                <?php if (isset($_SESSION['email'])): ?>
                    <span class="welcome-text">👋 <?= htmlspecialchars($_SESSION['email']) ?></span>
                    <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>


    <!-- ===================================
         CHATBOT PAGE
         =================================== -->
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Symvan Event Assistant</h1>
            <p class="page-subtitle">
                Use the AI assistant to brainstorm event ideas, plan logistics, and improve engagement.
            </p>
        </div>

        <div class="grid grid-2">
            <!-- Sidebar -->
            <aside class="chat-sidebar">
                <h3 class="card-title">What can I ask?</h3>
                <ul>
                    <li>Plan or name an event</li>
                    <li>Pick good times or venues</li>
                    <li>Generate descriptions for flyers</li>
                    <li>Brainstorm giveaways or themes</li>
                </ul>

                <h4 class="mt-md">Quick prompts</h4>
                <div class="quick-buttons">
                    <button class="btn btn-outline" data-prompt="Suggest a fun theme for a spring festival.">Spring theme</button>
                    <button class="btn btn-outline" data-prompt="Write a catchy description for a fundraising dinner.">Event description</button>
                    <button class="btn btn-outline" data-prompt="What is the best time for a commuter event?">Timing</button>
                    <button class="btn btn-outline" data-prompt="Ideas for student engagement at a concert night.">Engagement ideas</button>
                </div>
            </aside>

            <!-- Chat Window -->
            <section class="chat-container">
                <div id="chatMessages" class="chat-messages">
                    <div class="chat-message bot">
                        <div class="chat-bubble">
                            👋 Hi there! I'm your Symvan event assistant. Tell me what kind of event you're planning and I’ll help with ideas, descriptions, and timing suggestions.
                        </div>
                    </div>
                </div>

                <form id="chatForm" class="chat-input-row" onsubmit="return false;">
                    <textarea id="chatInput" placeholder="Ask something like: 'Help me plan a volunteer fair next month.'"></textarea>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </section>
        </div>
    </div>

    <!-- ===================================
         BASIC FRONT-END LOGIC
         =================================== -->
    <script>
        const chatForm = document.getElementById('chatForm');
        const chatInput = document.getElementById('chatInput');
        const chatMessages = document.getElementById('chatMessages');
        const quickButtons = document.querySelectorAll('.quick-buttons button');

        function appendMessage(role, text) {
            const msg = document.createElement('div');
            msg.classList.add('chat-message', role);
            const bubble = document.createElement('div');
            bubble.classList.add('chat-bubble');
            bubble.textContent = text;
            msg.appendChild(bubble);
            chatMessages.appendChild(msg);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        async function sendToAI(userText) {
            appendMessage('user', userText);

            // Temporary "thinking" bubble
            const thinking = document.createElement('div');
            thinking.classList.add('chat-message', 'bot');
            const bubble = document.createElement('div');
            bubble.classList.add('chat-bubble');
            bubble.textContent = "🤔 Thinking...";
            thinking.appendChild(bubble);
            chatMessages.appendChild(thinking);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            try {
                const res = await fetch("chatbot.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ prompt: userText })
                });

                const data = await res.json();
                thinking.remove();

                if (data.reply) {
                    appendMessage('bot', data.reply);
                } else {
                    appendMessage('bot', "⚠️ No reply from AI.");
                    console.error("No reply field in response:", data);
                }
            } catch (err) {
                thinking.remove();
                appendMessage('bot', "⚠️ Error connecting to AI server.");
                console.error(err);
            }
        }

        chatForm.addEventListener('submit', () => {
            const text = chatInput.value.trim();
            if (!text) return;
            chatInput.value = '';
            sendToAI(text);
        });


        quickButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const prompt = btn.getAttribute('data-prompt');
                chatInput.value = prompt;
                chatInput.focus();
            });
        });
    </script>
</body>
</html>
