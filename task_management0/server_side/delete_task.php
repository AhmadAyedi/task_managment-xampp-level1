<?php
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$dbname = 'task_management';
$username = 'root';
$password = '';

// Establish database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

$response = array();

if (isset($data['id'])) {
    $id = $data['id'];

    // Prepare the DELETE statement
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Execute the statement
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to delete task.';
    }

    // Close the statement
    $stmt->close();
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid task ID.';
}

// Close the connection
$conn->close();

// Output the response in JSON format
echo json_encode($response);
