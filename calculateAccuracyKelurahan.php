<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Allow CORS if needed (adjust the domain as required)
header('Access-Control-Allow-Origin: *'); // Change * to your specific frontend domain
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

try {
    // Check if the required POST data is set
    $inputData = json_decode(file_get_contents('php://input'), true);
    if (!isset($inputData['firstString']) || !isset($inputData['secondString'])) {
        throw new Exception('Invalid input: Both firstString and secondString are required.');
    }

    $firstString = trim($inputData['firstString']);
    $secondString = trim($inputData['secondString']);

    // Calculate the similarity percentage using similar_text
    similar_text($firstString, $secondString, $accuracy);

    // Return the result as JSON
    echo json_encode(['accuracy' => round($accuracy, 2)]);
} catch (Exception $e) {
    // Return a JSON error response with a 500 status code
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
