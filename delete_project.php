<?php
header('Content-Type: application/json');
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Project ID required']);
    exit;
}

$admin_id = $_SESSION['admin_id'];
$project_id = intval($data['id']);

// Check ownership first
$check = $conn->prepare("SELECT id FROM projects WHERE id = ? AND admin_id = ?");
$check->bind_param("ii", $project_id, $admin_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Project not found or unauthorized']);
    exit;
}

// Delete the project
$stmt = $conn->prepare("DELETE FROM projects WHERE id = ? AND admin_id = ?");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $project_id, $admin_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Project deleted successfully!'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete project: ' . $conn->error]);
}

$stmt->close();
$check->close();
$conn->close();
?>
