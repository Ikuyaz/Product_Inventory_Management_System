<?php
session_start();
include '../dbconnect.php';

function generateRandomCourier() {
    $randomNumber = mt_rand(0, 100) / 100; // Generate a random number between 0 and 1
    if ($randomNumber < 0.5) {
        return 'JNT';
    } else {
        return 'DHL';
    }
}

// Function to add information to tbl_completeorder based on order_code and user_id
function addToCompleteOrder($order_code) {
    global $conn;

    $user_id = $_SESSION['user_id'] ?? null; // Use null coalescing operator to handle cases where user_id is not set

    if (!$user_id) {
        echo "User is not logged in.";
        return; // Stop execution if user_id is not found
    }

    // Fetch order details based on order_code and user_id
    $sql_fetch_order = "SELECT * FROM tbl_order WHERE order_code = '$order_code' AND user_id = '$user_id'";
    $result = $conn->query($sql_fetch_order);

    if ($result->num_rows > 0) {
        // Fetch order details as an associative array
        $order_details = $result->fetch_assoc();

        // Extract order details
        $order_date = $order_details['order_date']; // Assuming order_date is already in DATETIME format
        $order_id = $order_details['order_id'];
        $order_cust = $order_details['order_cust'];
        $order_address = $order_details['cust_address'];
        $order_amount = $order_details['order_amount'];
        $cust_number = $order_details['cust_number'];
        $order_status = 'Complete'; // Set order status to 'Complete'
        $order_courier = generateRandomCourier(); // Generate random courier
        $cust_items = $order_details['cust_items'];
        $complete_date = date('Y-m-d H:i:s'); // Get current datetime in the required format

        // Start a transaction
        $conn->begin_transaction();

        try {
            // Insert order details into tbl_completeorder
            $sql_insert_complete_order = "INSERT INTO tbl_completeorder (user_id, order_date, order_id, order_cust, order_address, order_amount, cust_number, order_status, order_courier, order_code, cust_items, complete_date)
                                          VALUES ('$user_id', '$order_date', '$order_id', '$order_cust', '$order_address', $order_amount, '$cust_number', '$order_status', '$order_courier', '$order_code', '$cust_items', '$complete_date')";

            $conn->query($sql_insert_complete_order);

            // Delete the order from tbl_order
            $sql_delete_order = "DELETE FROM tbl_order WHERE order_code = '$order_code' AND user_id = '$user_id'";
            $conn->query($sql_delete_order);

            // Commit the transaction
            $conn->commit();

            echo "Order Shipped Out Successfully!";
        } catch (Exception $e) {
            // Rollback the transaction if an error occurred
            $conn->rollback();
            echo "Error" . $e->getMessage();
        }
    } else {
        echo "No order found with the provided order code and user ID.";
    }
}

// Example usage:
$order_code = isset($_POST['order_code']) ? $_POST['order_code'] : '';
if (!empty($order_code)) {
    addToCompleteOrder($order_code);
} else {
    echo "Please provide an order code.";
}

$conn->close();
?>
