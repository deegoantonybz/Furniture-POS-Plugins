<?php
// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    // Clear the current session data
    // session_unset(); // Unset all session variables
    // session_destroy(); // Destroy the session
    session_start();
}


// Get the raw JSON data from the POST request
$jsonData = file_get_contents('php://input');
// $jsonData = $_POST['data'];

// Decode the JSON data into an associative array
$data = json_decode($jsonData, true);

// Check if the JSON data contains the 'selectedStoreId' property
if (!isset($data['selectedStoreId'])) {
  echo json_encode(['error' => 'Missing selectedStoreId property']);
  exit;
}

// Extract the selected store ID from the decoded data
$selectedStoreId = $data['selectedStoreId'];

// Store the selected store ID in the server-side session
$_SESSION['selectedStoreId'] = $selectedStoreId;

// Respond to the AJAX request with a success status
echo json_encode(['status' => 'success']);

