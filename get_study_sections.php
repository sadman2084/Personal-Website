<?php
header('Content-Type: application/json');
include 'config.php';

$stmt = $conn->prepare("SELECT id, name, created_at, updated_at FROM study_sections ORDER BY created_at DESC");
if (!$stmt) {
  echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
  exit;
}
$stmt->execute();
$res = $stmt->get_result();
$sections = [];
while ($row = $res->fetch_assoc()) { $sections[] = $row; }
echo json_encode(['success' => true, 'sections' => $sections]);
$stmt->close();
$conn->close();
?>
