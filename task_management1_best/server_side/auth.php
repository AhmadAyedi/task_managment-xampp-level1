<?php
// Start a new session or resume the existing session
session_start();

// Include the database configuration file to establish a database connection
include_once '../config/db.php';

// Create a new instance of the Database class
$database = new Database();
// Get the database connection object
$db = $database->getConnection();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the action type from the POST request
    $action = $_POST['action'];

    // Handle the signup action
    if ($action == 'signup') {
        // Sanitize and validate the username from the POST request
        $username = htmlspecialchars(strip_tags($_POST['username']));
        // Sanitize and validate the email from the POST request
        $email = htmlspecialchars(strip_tags($_POST['email']));
        // Hash the password using a secure hashing algorithm
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Prepare an SQL query to insert a new user into the database
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        // Prepare the SQL statement
        $stmt = $db->prepare($query);
        // Bind the parameters to the SQL query
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        // Execute the SQL statement to insert the new user
        if ($stmt->execute()) {
            // On success, redirect the user to the login page
            header("Location: ../client_side/login.html");
        } else {
            // On failure, display an error message
            echo "Error: Could not sign up.";
        }
    } elseif ($action == 'login') {
        // Sanitize and validate the email from the POST request
        $email = htmlspecialchars(strip_tags($_POST['email']));
        // Retrieve the password from the POST request
        $password = $_POST['password'];

        // Prepare an SQL query to select the user based on the email
        $query = "SELECT id, password FROM users WHERE email = :email";
        // Prepare the SQL statement
        $stmt = $db->prepare($query);
        // Bind the email parameter to the SQL query
        $stmt->bindParam(':email', $email);
        // Execute the SQL statement to fetch the user data
        $stmt->execute();

        // Check if a user was found
        if ($stmt->rowCount() > 0) {
            // Fetch the user data
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verify the password against the hashed password stored in the database
            if (password_verify($password, $row['password'])) {
                // On successful login, set the user ID in the session and redirect to the tasks page
                $_SESSION['user_id'] = $row['id'];
                header("Location: ../client_side/tasks.html");
            } else {
                // On password mismatch, display an error message
                echo "Invalid email or password.";
            }
        } else {
            // If no user is found with the provided email, display an error message
            echo "Invalid email or password.";
        }
    }
}
