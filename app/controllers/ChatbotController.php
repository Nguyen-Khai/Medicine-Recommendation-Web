<?php
// app/controllers/ChatbotController.php

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// API Key - KHÔNG commit key thật lên repo
$api_key = ""; // <-- Đổi lại key mới an toàn

$input = json_decode(file_get_contents("php://input"), true);
$userMessage = trim($input['message'] ?? '');

if (!$userMessage) {
    http_response_code(400);
    echo json_encode(["error" => "No input provided"]);
    exit;
}

$data = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        [
            "role" => "system",
            "content" => "You are a helpful health assistant. Only provide reliable health-related advice."
        ],
        [
            "role" => "user",
            "content" => $userMessage
        ]
    ],
    "temperature" => 0.7
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer " . $api_key 
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["error" => "Curl error: " . curl_error($ch)]);
    exit;
}

$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($http_status);
echo $response;
