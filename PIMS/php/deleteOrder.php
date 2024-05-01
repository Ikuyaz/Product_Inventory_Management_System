<?php
include 'dbconnect.php';

// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, send an error response
    echo json_encode(array('error' => 'User not logged in.'));
    exit;
}

// Retrieve user_id from the session
$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize user inputs
    $orderId = isset($_POST['orderId']) ? $_POST['orderId'] : '';

    // Check if orderId is provided
    if (empty($orderId)) {
        echo json_encode(array('error' => 'No order ID provided for deletion.'));
        exit;
    }

    // Prepare and execute the DELETE query to remove the order
    $deleteStmt = $conn->prepare("DELETE FROM tbl_order WHERE order_id = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $orderId, $user_id);

    if ($deleteStmt->execute()) {
        // If the query was successful
        if ($deleteStmt->affected_rows > 0) {
            echo json_encode(array('success' => 'Order deleted successfully.'));
        } else {
            echo json_encode(array('error' => 'Order not found or user not authorized.'));
        }
    } else {
        echo json_encode(array('error' => 'Error deleting order: ' . $deleteStmt->error));
    }

    // Close the prepared statement
    $deleteStmt->close();
} else {
    // If not a POST request, return an error message
    echo json_encode(array('error' => 'Invalid request method.'));
}

// Close the database connection
$conn->close();
?>
