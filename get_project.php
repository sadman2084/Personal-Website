<?php
header('Content-Type: application/json');
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id']) || !isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized or missing ID']);
    exit;
}

$admin_id = $_SESSION['admin_id'];
$project_id = intval($_GET['id']);

// Get specific project
$stmt = $conn->prepare("SELECT id, title, description, github_link, live_link, image_url, tech_stack FROM projects WHERE id = ? AND admin_id = ?");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $project_id, $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Project not found']);
    exit;
}

$project = $result->fetch_assoc();
echo json_encode([
    'success' => true,
    'project' => $project
]);

$stmt->close();
$conn->close();
?>
