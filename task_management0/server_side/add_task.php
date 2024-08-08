<?php
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$task = $data['task'];
$deadline = $data['deadline'];

$sql = "INSERT INTO tasks (task, deadline) VALUES ('$task', '$deadline')";
$result = $conn->query($sql);

$response = [];
if ($result === TRUE) {
    $response['success'] = true;
} else {
    $response['success'] = false;
}

echo json_encode($response);
