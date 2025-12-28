<?php
header('Content-Type: application/json');
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['title']) || !isset($data['content'])) {
    echo json_encode(['success' => false, 'message' => 'Title and content are required']);
    exit;
}

$admin_id = $_SESSION['admin_id'];
$title = trim($data['title']);
$content = trim($data['content']);
$slug = isset($data['slug']) ? trim($data['slug']) : '';
$published = isset($data['published']) ? (int) !!$data['published'] : 1;

// Generate slug if not provided
if ($slug === '') {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title));
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
}

// Ensure slug not empty
if ($slug === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid slug generated from title']);
    exit;
}

// Check slug uniqueness
$check = $conn->prepare("SELECT id FROM blogs WHERE slug = ? LIMIT 1");
if (!$check) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}
$check->bind_param("s", $slug);
$check->execute();
$res = $check->get_result();
if ($res && $res->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Slug already exists. Please choose another.']);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Insert blog
$stmt = $conn->prepare("INSERT INTO blogs (admin_id, title, slug, content, published) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}
$stmt->bind_param("isssi", $admin_id, $title, $slug, $content, $published);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Blog created successfully!', 'blog_id' => $conn->insert_id, 'slug' => $slug]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create blog: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
