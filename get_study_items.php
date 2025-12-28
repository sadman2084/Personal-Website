<?php
header('Content-Type: application/json');
include 'config.php';

$section_id = isset($_GET['section_id']) ? intval($_GET['section_id']) : 0;
if ($section_id <= 0) {
  echo json_encode(['success' => false, 'message' => 'section_id is required']);
  exit;
}

$stmt = $conn->prepare("SELECT id, title, description, links_json, blog_link, created_at FROM study_items WHERE section_id = ? AND is_active = 1 ORDER BY created_at DESC");
if (!$stmt) {
  echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
  exit;
}
$stmt->bind_param("i", $section_id);
$stmt->execute();
$res = $stmt->get_result();
$items = [];
while ($row = $res->fetch_assoc()) { $items[] = $row; }
echo json_encode(['success' => true, 'items' => $items]);
$stmt->close();
$conn->close();
?>
