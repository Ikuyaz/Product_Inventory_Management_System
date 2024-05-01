<?php
include '../dbconnect.php';

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

// Fetch complete orders from the database for the specific user
$sql = "SELECT order_id, order_date, order_cust, order_address, cust_number, order_courier, order_amount, order_status, complete_date FROM tbl_completeorder WHERE user_id = '$user_id'";
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Fetch orders as an associative array
    $orders = array();
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    // Return orders as JSON
    echo json_encode($orders);
} else {
    // Return an empty array if no orders are found
    echo json_encode(array());
}

// Close the database connection
$conn->close();
?>
