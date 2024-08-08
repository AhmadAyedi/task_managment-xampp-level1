<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

include_once '../config/db.php';
$database = new Database();
$db = $database->getConnection();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $response = ['success' => false];

    try {
        if ($action == 'add') {
            $task = htmlspecialchars(strip_tags($_POST['task']));
            $query = "INSERT INTO tasks (user_id, task) VALUES (:user_id, :task)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':task', $task);
            $stmt->execute();
            $response['success'] = true;
        } elseif ($action == 'delete') {
            $task_id = htmlspecialchars(strip_tags($_POST['task_id']));
            $query = "DELETE FROM tasks WHERE id = :task_id AND user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':task_id', $task_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $response['success'] = true;
        } elseif ($action == 'mark') {
            $task_id = htmlspecialchars(strip_tags($_POST['task_id']));
            $query = "UPDATE tasks SET is_completed = 1 WHERE id = :task_id AND user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':task_id', $task_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $response['success'] = true;
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}
