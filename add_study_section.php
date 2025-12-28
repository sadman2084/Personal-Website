<?php
header('Content-Type: application/json');
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['name']) || !trim($data['name'])) {
  echo json_encode(['success' => false, 'message' => 'Section name is required']);
  exit;
}

$admin_id = intval($_SESSION['admin_id']);
$name = trim($data['name']);

$stmt = $conn->prepare("INSERT INTO study_sections (admin_id, name) VALUES (?, ?)");
if (!$stmt) {
  echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
  exit;
}
$stmt->bind_param("is", $admin_id, $name);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Section created', 'section_id' => $conn->insert_id]);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to create section: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
