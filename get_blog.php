<?php
header('Content-Type: application/json');
include 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : null;

if (!$id && !$slug) {
    echo json_encode(['success' => false, 'message' => 'id or slug is required']);
    exit;
}

if ($id) {
    $stmt = $conn->prepare("SELECT id, title, slug, content, created_at, updated_at FROM blogs WHERE id = ? AND published = 1 LIMIT 1");
    $stmt->bind_param("i", $id);
} else {
    $stmt = $conn->prepare("SELECT id, title, slug, content, created_at, updated_at FROM blogs WHERE slug = ? AND published = 1 LIMIT 1");
    $stmt->bind_param("s", $slug);
}

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Blog not found']);
} else {
    $blog = $result->fetch_assoc();
    echo json_encode(['success' => true, 'blog' => $blog]);
}

$stmt->close();
$conn->close();
?>
