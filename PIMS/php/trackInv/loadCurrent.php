<?php
include '../dbconnect.php';

// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, send an error response
    echo json_encode(array('success' => false, 'error' => 'User is not logged in'));
    exit;
}

// Retrieve user_id from the session
$user_id = $_SESSION['user_id'];

// Fetch sum of product quantities for each product SKU
$sql = "SELECT product_sku, SUM(product_quantity) AS total_quantity FROM tbl_product WHERE user_id = '$user_id' GROUP BY product_sku";
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Fetch product data as an associative array
    $products = array();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    // Return product data as JSON
    echo json_encode(array('success' => true, 'products' => $products));
} else {
    // Return an error response if no results are found
    echo json_encode(array('success' => false, 'error' => 'No products found'));
}

// Close the database connection
$conn->close();
?>
