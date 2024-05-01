<?php
include 'dbconnect.php';

// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, send an empty array as a response
    echo json_encode(array());
    exit;
}

// Retrieve user_id from the session
$user_id = $_SESSION['user_id'];

// Fetch suppliers from the database for the specific user
$sql = "SELECT supp_id, supp_name, supp_phone, supp_email, supp_product, product_price FROM tbl_supplier WHERE user_id = '$user_id'";
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Fetch suppliers as an associative array
    $suppliers = array();
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }

    // Return suppliers as JSON
    echo json_encode($suppliers);
} else {
    // Return an empty array if no suppliers are found
    echo json_encode(array());
}

// Close the database connection
$conn->close();
?>
