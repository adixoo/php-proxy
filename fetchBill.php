<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Content-Type: application/json");
    echo json_encode(["error" => "This endpoint only accepts POST requests"]);
    exit();
}

$contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : "";
if (strpos($contentType, "application/json") !== 0) {
    http_response_code(415);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Content-Type must be application/json"]);
    exit();
}

try {
    $data = json_decode(file_get_contents("php://input"), true);

    $query_string = http_build_query($data);
    $url = "https://jsonplaceholder.typicode.com/users?" . $query_string;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        throw new Exception("cURL error: $error_msg");
    }

    $response = curl_exec($ch);
    $response_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    $response = json_decode($response, true)[0];
    http_response_code($response_code);
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    echo json_encode($response);
    exit();
} catch (Exception $e) {
    http_response_code(500);
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    $response = ["status" => "failed", "error" => $e->getMessage()];
    echo json_encode($response);
    exit();
}

?>
