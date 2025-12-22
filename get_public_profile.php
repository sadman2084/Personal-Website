<?php
header('Content-Type: application/json');
include 'config.php';

// Get the primary admin's public profile information
// This fetches the admin with the lowest ID (first admin)
$stmt = $conn->prepare("SELECT username, email, first_name, last_name, bio, phone, location, github_url, linkedin_url, portfolio_title, profile_image FROM admins ORDER BY id ASC LIMIT 1");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Return default profile if no admin exists
    echo json_encode([
        'success' => true,
        'profile' => [
            'username' => 'Sadman',
            'email' => 'sadmankabir897@gmail.com',
            'first_name' => 'Sadman',
            'last_name' => '',
            'bio' => 'Web Developer, Designer, and Problem Solver',
            'phone' => '+88 01539568935',
            'location' => 'Dhaka, Bangladesh',
            'github_url' => 'https://github.com/sadman2084',
            'linkedin_url' => 'https://www.linkedin.com/in/md-sadman-kabir-414b4b270/',
            'portfolio_title' => 'Portfolio',
            'profile_image' => null
        ]
    ]);
    $stmt->close();
    $conn->close();
    exit;
}

$profile = $result->fetch_assoc();
echo json_encode([
    'success' => true,
    'profile' => $profile
]);

$stmt->close();
$conn->close();
?>
