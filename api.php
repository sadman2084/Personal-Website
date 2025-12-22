<?php
header('Content-Type: application/json');
require_once 'config.php';

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle different request types
switch ($method) {
    case 'GET':
        getInventory();
        break;
    case 'POST':
        addItem();
        break;
    case 'PUT':
        updateQuantity();
        break;
    case 'PATCH':
        editItem();
        break;
    case 'DELETE':
        deleteItem();
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

// Get all inventory items
function getInventory() {
    global $conn;
    
    $sql = "SELECT id, name, description, quantity, image_type, created_at FROM hardware_inventory ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $items = [];
        while ($row = $result->fetch_assoc()) {
            // Get image as base64
            $imageSql = "SELECT image FROM hardware_inventory WHERE id = ?";
            $imageStmt = $conn->prepare($imageSql);
            $imageStmt->bind_param("i", $row['id']);
            $imageStmt->execute();
            $imageResult = $imageStmt->get_result();
            $imageRow = $imageResult->fetch_assoc();
            
            $base64Image = base64_encode($imageRow['image']);
            $mimeType = $row['image_type'] ? $row['image_type'] : 'image/jpeg';
            
            $items[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'quantity' => (int)$row['quantity'],
                'image' => 'data:' . $mimeType . ';base64,' . $base64Image,
                'created_at' => $row['created_at']
            ];
            $imageStmt->close();
        }
        echo json_encode($items);
    } else {
        echo json_encode([]);
    }
}

// Add new item
function addItem() {
    global $conn;
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($input['name']) || !isset($input['image'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Name and image are required']);
        return;
    }
    
    $name = trim($input['name']);
    $description = isset($input['description']) ? trim($input['description']) : null;
    $quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;
    $imageData = $input['image']; // base64 string
    
    // Parse base64 image
    if (strpos($imageData, 'data:') === 0) {
        // Extract MIME type
        preg_match('/data:([^;]+)/', $imageData, $matches);
        $mimeType = $matches[1] ?? 'image/jpeg';
        
        // Remove data:image/...;base64, prefix
        $imageData = preg_replace('#^data:image/[^;]+;base64,#', '', $imageData);
        $imageData = base64_decode($imageData);
    } else {
        $mimeType = 'image/jpeg';
        $imageData = base64_decode($imageData);
    }
    
    // Prepare statement
    $sql = "INSERT INTO hardware_inventory (name, description, quantity, image, image_type) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
        return;
    }
    
    // Bind parameters
    $stmt->bind_param("ssisb", $name, $description, $quantity, $imageData, $mimeType);
    
    // Execute
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'id' => $conn->insert_id,
            'message' => 'Item added successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
    }
    
    $stmt->close();
}

// Update quantity
function updateQuantity() {
    global $conn;
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($input['id']) || !isset($input['quantity'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID and quantity are required']);
        return;
    }
    
    $id = (int)$input['id'];
    $quantity = (int)$input['quantity'];
    
    // Ensure quantity is at least 1
    if ($quantity < 1) {
        $quantity = 1;
    }
    
    // Prepare statement
    $sql = "UPDATE hardware_inventory SET quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
        return;
    }
    
    // Bind parameters
    $stmt->bind_param("ii", $quantity, $id);
    
    // Execute
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Quantity updated']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Item not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
    }
    
    $stmt->close();
}

// Delete item
function deleteItem() {
    global $conn;
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID is required']);
        return;
    }
    
    $id = (int)$input['id'];
    
    // Prepare statement
    $sql = "DELETE FROM hardware_inventory WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
        return;
    }
    
    // Bind parameters
    $stmt->bind_param("i", $id);
    
    // Execute
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Item deleted']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Item not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
    }
    
    $stmt->close();
}

// Edit item
function editItem() {
    global $conn;
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID is required']);
        return;
    }
    
    $id = (int)$input['id'];
    $name = isset($input['name']) ? trim($input['name']) : null;
    $description = isset($input['description']) ? trim($input['description']) : null;
    $quantity = isset($input['quantity']) ? (int)$input['quantity'] : null;
    $imageData = isset($input['image']) ? $input['image'] : null;
    $mimeType = null;
    
    // Parse base64 image if provided
    if ($imageData !== null) {
        if (strpos($imageData, 'data:') === 0) {
            // Extract MIME type
            preg_match('/data:([^;]+)/', $imageData, $matches);
            $mimeType = $matches[1] ?? 'image/jpeg';
            
            // Remove data:image/...;base64, prefix
            $imageData = preg_replace('#^data:image/[^;]+;base64,#', '', $imageData);
            $imageData = base64_decode($imageData);
        } else {
            $mimeType = 'image/jpeg';
            $imageData = base64_decode($imageData);
        }
    }
    
    // Build dynamic SQL
    $updates = [];
    $types = '';
    $params = [];
    
    if ($name !== null) {
        $updates[] = 'name = ?';
        $types .= 's';
        $params[] = $name;
    }
    
    if ($description !== null) {
        $updates[] = 'description = ?';
        $types .= 's';
        $params[] = $description;
    }
    
    if ($quantity !== null) {
        $updates[] = 'quantity = ?';
        $types .= 'i';
        $params[] = $quantity;
    }
    
    if ($imageData !== null) {
        $updates[] = 'image = ?';
        $updates[] = 'image_type = ?';
        $types .= 'bs';
        $params[] = $imageData;
        $params[] = $mimeType;
    }
    
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        return;
    }
    
    $sql = "UPDATE hardware_inventory SET " . implode(', ', $updates) . " WHERE id = ?";
    $types .= 'i';
    $params[] = $id;
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
        return;
    }
    
    // Bind parameters dynamically - need to pass by reference for binary data
    $bindParams = array_merge([$types], $params);
    call_user_func_array([$stmt, 'bind_param'], $bindParams);
    
    // Execute
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Item updated']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Item not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
    }
    
    $stmt->close();
}
$conn->close();
?>
