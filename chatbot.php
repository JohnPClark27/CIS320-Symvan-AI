<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php'; // <-- corrected path

// load .env from backend
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../backend');
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
            ["role" => "system", "content" => "You are Symvan, an event planning assistant."],
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
    echo json_encode(['reply' => $result['choices'][0]['message']['content'] ?? 'No response from AI']);
    exit;
}
