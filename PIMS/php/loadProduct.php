<?php
include 'dbconnect.php';

// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, send an error response
    echo json_encode(array());
    exit;
}

// Retrieve user_id from the session
$user_id = $_SESSION['user_id'];

// Fetch products from the database for the specific user
$sql = "SELECT product_name, product_sku, product_quantity, product_price FROM tbl_product WHERE user_id = '$user_id'";
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Fetch products as an associative array
    $products = array();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    // Return products as JSON
    echo json_encode($products);
} else {
    // Return an empty array if no products are found
    echo json_encode(array());
}

// Close the database connection
$conn->close();
?>
