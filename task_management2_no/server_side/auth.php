<?php
session_start();
header('Content-Type: application/json');

include_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $response = ['success' => false];

    try {
        if ($action == 'login') {
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $password = htmlspecialchars(strip_tags($_POST['password']));

            $query = "SELECT id, password FROM users WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $response['success'] = true;
            } else {
                $response['message'] = 'Invalid credentials';
            }
        } elseif ($action == 'logout') {
            session_unset();
            session_destroy();
            $response['success'] = true;
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}
