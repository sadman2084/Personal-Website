<?php
header('Content-Type: application/json');
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Message ID required']);
    exit;
}

$message_id = intval($data['id']);

$stmt = $conn->prepare("UPDATE messages SET read_status = 1 WHERE id = ?");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $message_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Message marked as read']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update message']);
}

$stmt->close();
$conn->close();
?>
