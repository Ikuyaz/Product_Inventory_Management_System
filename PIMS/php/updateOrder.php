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
    $orderId = mysqli_real_escape_string($conn, $_POST['orderId']);
    $customerName = mysqli_real_escape_string($conn, $_POST['customerName']);
    $paymentType = isset($_POST['paymentType']) ? mysqli_real_escape_string($conn, $_POST['paymentType']) : null;
    $orderAmount = isset($_POST['orderAmount']) ? (float)$_POST['orderAmount'] : null;
    $custAddress = mysqli_real_escape_string($conn, $_POST['custAddress']);
    $custNumber = mysqli_real_escape_string($conn, $_POST['custNumber']);

    // Fetch existing order details from the database
    $selectSql = "SELECT * FROM tbl_order WHERE order_id = '$orderId' AND user_id = '$user_id'";
    $result = $conn->query($selectSql);

    if ($result->num_rows > 0) {
        $existingOrderDetails = $result->fetch_assoc();

        // Compare existing values with the submitted values
        $updateFields = array();
        if ($customerName !== $existingOrderDetails['order_cust']) {
            $updateFields[] = "order_cust = '$customerName'";
        }
        if ($paymentType !== null && $paymentType !== $existingOrderDetails['payment_type']) {
            $updateFields[] = "payment_type = '$paymentType'";
        }
        if ($orderAmount !== null && $orderAmount !== $existingOrderDetails['order_amount']) {
            $updateFields[] = "order_amount = $orderAmount";
        }
        if ($custAddress !== $existingOrderDetails['cust_address']) {
            $updateFields[] = "cust_address = '$custAddress'";
        }
        if ($custNumber !== $existingOrderDetails['cust_number']) {
            $updateFields[] = "cust_number = '$custNumber'";
        }

        // Construct the dynamic update SQL query
        if (!empty($updateFields)) {
            $updateSql = "UPDATE tbl_order SET " . implode(', ', $updateFields) . " WHERE order_id = '$orderId' AND user_id = '$user_id'";

            if ($conn->query($updateSql) === TRUE) {
                echo json_encode(array('success' => 'Order updated successfully'));
            } else {
                echo json_encode(array('error' => 'Error updating order: ' . $conn->error));
            }
        } else {
            // No fields were changed
            echo json_encode(array('success' => 'No changes made to the order'));
        }
    } else {
        // Return an error message if no order is found
        echo json_encode(array('error' => 'Order not found.'));
    }
} else {
    // If not a POST request, fetch the order details based on the selected order ID
    $selectedOrderId = isset($_GET['order_id']) ? mysqli_real_escape_string($conn, $_GET['order_id']) : '';

    if ($selectedOrderId !== '') {
        // Fetch order details from the database
        $selectSql = "SELECT * FROM tbl_order WHERE order_id = '$selectedOrderId' AND user_id = '$user_id'";
        $result = $conn->query($selectSql);

        if ($result->num_rows > 0) {
            // Fetch order details as an associative array
            $orderDetails = $result->fetch_assoc();

            // Return order details as JSON
            echo json_encode($orderDetails);
        } else {
            // Return an error message if no order is found
            echo json_encode(array('error' => 'Order not found.'));
        }
    } else {
        // Return an error message if order ID is not set
        echo json_encode(array('error' => 'Order ID not provided.'));
    }
}

// Close connection
$conn->close();
?>