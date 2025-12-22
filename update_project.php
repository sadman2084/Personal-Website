<?php
header('Content-Type: application/json');
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['id']) || !isset($data['title']) || !isset($data['description'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$admin_id = $_SESSION['admin_id'];
$project_id = intval($data['id']);
$title = trim($data['title']);
$description = trim($data['description']);
$github_link = isset($data['github_link']) ? trim($data['github_link']) : null;
$live_link = isset($data['live_link']) ? trim($data['live_link']) : null;
$image_url = isset($data['image_url']) ? trim($data['image_url']) : null;
$tech_stack = isset($data['tech_stack']) ? trim($data['tech_stack']) : null;

// Validate URLs if provided
if ($github_link && !filter_var($github_link, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid GitHub URL']);
    exit;
}

if ($live_link && !filter_var($live_link, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Live Demo URL']);
    exit;
}

// Update project (check ownership first)
$check = $conn->prepare("SELECT id FROM projects WHERE id = ? AND admin_id = ?");
$check->bind_param("ii", $project_id, $admin_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Project not found or unauthorized']);
    exit;
}

// Update the project
$stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, github_link = ?, live_link = ?, image_url = ?, tech_stack = ? WHERE id = ? AND admin_id = ?");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ssssssii", $title, $description, $github_link, $live_link, $image_url, $tech_stack, $project_id, $admin_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Project updated successfully!'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update project: ' . $conn->error]);
}

$stmt->close();
$check->close();
$conn->close();
?>
