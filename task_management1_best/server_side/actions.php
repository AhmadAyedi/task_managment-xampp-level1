<?php
// Start a new session or resume the existing session
session_start();

// Check if the user is logged in by verifying if 'user_id' is set in the session
if (!isset($_SESSION['user_id'])) {
    // If 'user_id' is not set, redirect the user to the login page
    header("Location: ../client_side/login.html");
    // Terminate the script execution to prevent further code execution
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

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the action type from the POST request
    $action = $_POST['action'];

    // Handle different actions based on the 'action' value
    if ($action == 'add') {
        // Sanitize and validate the task input from the POST request
        $task = htmlspecialchars(strip_tags($_POST['task']));
        // Prepare an SQL query to insert a new task into the database
        $query = "INSERT INTO tasks (user_id, task) VALUES (:user_id, :task)";
        // Prepare the SQL statement
        $stmt = $db->prepare($query);
        // Bind the parameters to the SQL query
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':task', $task);
        // Execute the SQL statement to insert the new task
        $stmt->execute();
    } elseif ($action == 'delete') {
        // Sanitize and validate the task ID from the POST request
        $task_id = htmlspecialchars(strip_tags($_POST['task_id']));
        // Prepare an SQL query to delete a task from the database
        $query = "DELETE FROM tasks WHERE id = :task_id AND user_id = :user_id";
        // Prepare the SQL statement
        $stmt = $db->prepare($query);
        // Bind the parameters to the SQL query
        $stmt->bindParam(':task_id', $task_id);
        $stmt->bindParam(':user_id', $user_id);
        // Execute the SQL statement to delete the task
        $stmt->execute();
    } elseif ($action == 'mark') {
        // Sanitize and validate the task ID from the POST request
        $task_id = htmlspecialchars(strip_tags($_POST['task_id']));
        // Prepare an SQL query to mark a task as completed
        $query = "UPDATE tasks SET is_completed = 1 WHERE id = :task_id AND user_id = :user_id";
        // Prepare the SQL statement
        $stmt = $db->prepare($query);
        // Bind the parameters to the SQL query
        $stmt->bindParam(':task_id', $task_id);
        $stmt->bindParam(':user_id', $user_id);
        // Execute the SQL statement to update the task status
        $stmt->execute();
    }
}
