<?php
session_start();
header("Content-Type: application/json");

// PayMongo API Secret Key (replace with your own)
$paymongoSecretKey = "sk_test_GdBx7VoYBSQr95zY5Rr8VkXK";

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST data
    $amount = $_POST['amount'] ?? null;
    $description = $_POST['description'] ?? '';

    // Validate inputs
    if (!is_numeric($amount) || $amount <= 0 || empty(trim($description))) {
        echo json_encode(["error" => "Invalid amount or description."]);
        exit();
    }

    // Prepare payload for PayMongo API
    $payload = [
        "data" => [
            "attributes" => [
                "amount" => (int) $amount, // Ensure the amount is an integer (in centavos)
                "description" => $description
            ]
        ]
    ];

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/links"); // Correct URL for payment links
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: application/json",
        "Authorization: Basic " . base64_encode("$paymongoSecretKey:"),
        "Content-Type: application/json"
    ]);

    // Execute cURL request
    $response = curl_exec($ch);

    // Handle cURL errors
    if (curl_errno($ch)) {
        echo json_encode(["error" => curl_error($ch)]);
        curl_close($ch);
        exit();
    }

    // Get HTTP response code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Handle PayMongo API responses
    if ($httpCode === 200) { // OK
        $responseData = json_decode($response, true);

        // Check if the checkout_url is in the response
        $paymentLink = $responseData['data']['attributes']['checkout_url'] ?? null;

        if ($paymentLink) {
            echo json_encode(["redirect" => $paymentLink]);
        } else {
            echo json_encode(["error" => "Failed to retrieve payment link."]);
        }
    } else {
        // Decode error response from PayMongo
        $errorResponse = json_decode($response, true);
        echo json_encode([
            "error" => "PayMongo API returned HTTP code $httpCode.",
            "details" => $errorResponse['errors'] ?? $response
        ]);
    }
} else {
    // Invalid request method
    echo json_encode(["error" => "Invalid request method. Only POST is allowed."]);
}
?>
