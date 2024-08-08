<?php
// Start a new session or resume the existing session
session_start();

// Check if the user is logged in by verifying if 'user_id' is set in the session
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: ../client_side/login.html");
    // Stop script execution after the redirect
    exit();
}

// Include the database configuration file to establish a database connection
include_once '../config/db.php';

// Create a new instance of the Database class
$database = new Database();
// Get the database connection object
$db = $database->getConnection();

// Retrieve the user ID from the session
$user_id = $_SESSION['user_id'];

// Prepare an SQL query to select tasks associated with the logged-in user
$query = "SELECT id, task, is_completed FROM tasks WHERE user_id = :user_id";
// Prepare the SQL statement for execution
$stmt = $db->prepare($query);
// Bind the user ID parameter to the SQL query
$stmt->bindParam(':user_id', $user_id);
// Execute the SQL statement to fetch the tasks
$stmt->execute();
// Fetch all tasks associated with the user as an associative array
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Encode the tasks array as a JSON string and output it
echo json_encode($tasks);
