<?php
header('Content-Type: application/json');
include 'config.php';

$stmt = $conn->prepare("SELECT id, title, slug, created_at FROM blogs WHERE published = 1 ORDER BY created_at DESC");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$blogs = [];
while ($row = $result->fetch_assoc()) {
    $blogs[] = $row;
}

echo json_encode(['success' => true, 'blogs' => $blogs]);

$stmt->close();
$conn->close();
?>
