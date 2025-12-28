<?php
header('Content-Type: application/json');
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$section_id = isset($data['section_id']) ? intval($data['section_id']) : 0;
$title = isset($data['title']) ? trim($data['title']) : '';
$description = isset($data['description']) ? trim($data['description']) : '';
$links = isset($data['links']) && is_array($data['links']) ? $data['links'] : [];
$blog_link = isset($data['blog_link']) ? trim($data['blog_link']) : null;
$is_active = isset($data['is_active']) ? (int)!!$data['is_active'] : 1;

if ($section_id <= 0 || $title === '') {
  echo json_encode(['success' => false, 'message' => 'section_id and title are required']);
  exit;
}

// Validate URLs
foreach ($links as $l) {
  if ($l && !filter_var($l, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid URL in links']);
    exit;
  }
}
if ($blog_link && !filter_var($blog_link, FILTER_VALIDATE_URL)) {
  echo json_encode(['success' => false, 'message' => 'Invalid blog_link URL']);
  exit;
}

$links_json = json_encode(array_values(array_filter(array_map('trim', $links))));

$stmt = $conn->prepare("INSERT INTO study_items (section_id, title, description, links_json, blog_link, is_active) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
  echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
  exit;
}
$stmt->bind_param("issssi", $section_id, $title, $description, $links_json, $blog_link, $is_active);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Item created', 'item_id' => $conn->insert_id]);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to create item: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
