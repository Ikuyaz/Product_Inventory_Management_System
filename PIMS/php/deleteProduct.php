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
    $selectedProductSKUs = isset($_POST['selectedProductSKUs']) ? $_POST['selectedProductSKUs'] : [];

    if (empty($selectedProductSKUs)) {
        echo json_encode(array('error' => 'No products selected for deletion.'));
        exit;
    }

    // Convert array of SKUs to a comma-separated string for the SQL query
    $selectedProductSKUsString = implode("','", $selectedProductSKUs);

    // Delete selected products
    $deleteSql = "DELETE FROM tbl_product WHERE product_sku IN ('$selectedProductSKUsString') AND user_id = '$user_id'";

    if ($conn->query($deleteSql) === TRUE) {
        echo json_encode(array('success' => 'Products deleted successfully'));
    } else {
        echo json_encode(array('error' => 'Error deleting products: ' . $conn->error));
    }
} else {
    // If not a POST request, return an error message
    echo json_encode(array('error' => 'Invalid request method.'));
}

// Close connection
$conn->close();
?>
