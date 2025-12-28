<?php
header('Content-Type: application/json');
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = isset($data['id']) ? intval($data['id']) : 0;
if ($id <= 0) {
  echo json_encode(['success' => false, 'message' => 'Invalid item id']);
  exit;
}

$stmt = $conn->prepare("DELETE FROM study_items WHERE id = ?");
if (!$stmt) {
  echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
  exit;
}
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Item deleted']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to delete item: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
