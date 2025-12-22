<?php
header('Content-Type: application/json');
include 'config.php';

// Get JSON data from contact form
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['name']) || !isset($data['email']) || !isset($data['message'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$name = trim($data['name']);
$email = trim($data['email']);
$message = trim($data['message']);

// Validate inputs
if (strlen($name) < 2) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid name']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit;
}

if (strlen($message) < 10) {
    echo json_encode(['success' => false, 'message' => 'Message must be at least 10 characters long']);
    exit;
}

// Prevent spam - check if same email sent message recently (within 5 minutes)
$check_spam = $conn->prepare("SELECT id FROM messages WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
$check_spam->bind_param("s", $email);
$check_spam->execute();
$spam_result = $check_spam->get_result();

if ($spam_result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Please wait a few minutes before sending another message']);
    $check_spam->close();
    exit;
}
$check_spam->close();

// Insert message into database
$stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("sss", $name, $email, $message);

if ($stmt->execute()) {
    $message_id = $conn->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully! Thank you for contacting me.',
        'message_id' => $message_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again.']);
}

$stmt->close();
$conn->close();
?>
