

<?php
// done ✔
// Define a function to handle the response
function sendResponse($code, $message) {
    http_response_code($code);
    header("Content-Type: application/json");
    echo json_encode($message);
    exit();
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendResponse(405, ["message" => "Move To Home Page"]);
}

// Check if the content type is application/json
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : "";
if (strpos($contentType, "application/json") !== 0) {
    sendResponse(415, ["message" => "Move To Home Page"]);
}

// Define the key for validation
$key = "n5zqpf9ul7LPWMjDO6ePozakUwjdjQLL";

try {
    // Decode the input data
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate the key
    if (!isset($data["key"]) || $data["key"] !== $key) {
        sendResponse(400, ["status" => "failed", "message" => "key"]);
    }

    // Remove the key from the data array after validation
    unset($data["key"]);

    // Prepare the query string and URL
    $query_string = http_build_query($data);
    $url = "https://api.roundpay.net//API/FetchBill?" . $query_string;

    // Initialize cURL and set options
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the cURL request
    $response = curl_exec($ch);
    $response_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    // Decode the response and send it back
    $response = json_decode($response, true);
    sendResponse($response_code, $response);
} catch (Exception $e) {
    sendResponse(500, ["status" => "failed", "error" => $e->getMessage()]);
}
?>
