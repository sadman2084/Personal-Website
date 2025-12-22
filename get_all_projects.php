<?php
header('Content-Type: application/json');
include 'config.php';

// Get all projects ordered by creation date (descending)
$stmt = $conn->prepare("SELECT id, title, description, github_link, live_link, image_url, tech_stack FROM projects ORDER BY created_at DESC");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

echo json_encode([
    'success' => true,
    'projects' => $projects
]);

$stmt->close();
$conn->close();
?>
