<?php
header('Content-Type: application/json');
include 'config.php';

// Get all messages ordered by date (newest first)
$stmt = $conn->prepare("SELECT id, name, email, message, read_status, created_at FROM messages ORDER BY created_at DESC");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    // Format the date
    $date = new DateTime($row['created_at']);
    $row['formatted_date'] = $date->format('M d, Y h:i A');
    $messages[] = $row;
}

// Get unread count
$unread_stmt = $conn->prepare("SELECT COUNT(*) as unread_count FROM messages WHERE read_status = 0");
$unread_stmt->execute();
$unread_result = $unread_stmt->get_result();
$unread_row = $unread_result->fetch_assoc();

echo json_encode([
    'success' => true,
    'messages' => $messages,
    'unread_count' => $unread_row['unread_count'],
    'total_count' => count($messages)
]);

$stmt->close();
$unread_stmt->close();
$conn->close();
?>
