<?php
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$completed = $data['completed'] ? 1 : 0;

$sql = "UPDATE tasks SET completed = $completed WHERE id = $id";
$result = $conn->query($sql);

$response = [];
if ($result === TRUE) {
    $response['success'] = true;
} else {
    $response['success'] = false;
}

echo json_encode($response);
