<?php
// Include database connection
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
    $supp_id = isset($_POST['supp_id']) ? $_POST['supp_id'] : '';

    // Check if supp_id is provided
    if (empty($supp_id)) {
        echo json_encode(array('error' => 'No supplier ID provided for deletion.'));
        exit;
    }

    // Prepare and execute the DELETE query to remove the supplier
    $deleteStmt = $conn->prepare("DELETE FROM tbl_supplier WHERE supp_id = ? AND user_id = ?");
    
    // Check if the prepared statement was created successfully
    if (!$deleteStmt) {
        echo json_encode(array('error' => 'Prepare failed: ' . $conn->error));
        exit;
    }

    // Bind parameters to the prepared statement
    $deleteStmt->bind_param("si", $supp_id, $user_id); // Assuming supp_id is a string and user_id is an integer

    // Execute the prepared statement
    if ($deleteStmt->execute()) {
        // If the query was successful
        if ($deleteStmt->affected_rows > 0) {
            echo json_encode(array('success' => 'Supplier deleted successfully.'));
        } else {
            echo json_encode(array('error' => 'Supplier not found or user not authorized.'));
        }
    } else {
        echo json_encode(array('error' => 'Error deleting supplier: ' . $deleteStmt->error));
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
