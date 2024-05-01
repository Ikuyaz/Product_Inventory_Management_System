<?php
include 'dbconnect.php';

// Fetch all products from the database
$sql = "SELECT user_id, product_name, product_sku, product_quantity, product_price FROM tbl_product";
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
