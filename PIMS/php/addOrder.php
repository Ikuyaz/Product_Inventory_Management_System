<?php
include 'dbconnect.php';

// Retrieve POST data sent from the form
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
$order_date = date('Y-m-d'); // Current date
$customer_name = isset($_POST['customer_name']) ? $_POST['customer_name'] : '';
$payment_type = isset($_POST['payment_type']) ? $_POST['payment_type'] : '';
$selected_products = json_decode($_POST['selected_products'], true);
$cust_address = isset($_POST['cust_address']) ? $_POST['cust_address'] : '';
$cust_number = isset($_POST['cust_number']) ? $_POST['cust_number'] : '';

// Function to generate a random 7-digit number
function generateOrderCode() {
    return mt_rand(1000000, 9999999); // Generate a random number between 1000000 and 9999999
}

// Start a transaction
$conn->begin_transaction();

try {
    // Initialize order amount
    $order_amount = 0;
    $product_quantities = [];

    // Generate order code
    $order_code = generateOrderCode();

    // Iterate through selected products and process them
    foreach ($selected_products as $product) {
        $product_name = $product['name'];
        $product_price = $product['price'];
        $product_quantity_selected = $product['quantity'];

        // Calculate the total price for each product and update order amount
        $order_amount += $product_price * $product_quantity_selected;

        // Update tbl_product to decrease the product quantity
        $sql_update_product = "UPDATE tbl_product 
        SET product_quantity = product_quantity - $product_quantity_selected 
        WHERE product_name = '$product_name' 
        AND user_id = '$user_id'";

        $conn->query($sql_update_product);

        // Add product details to product_quantities array
        $product_quantities[] = "$product_name ------ $product_quantity_selected";
    }

    // Convert product_quantities array to a newline-separated string
    $cust_items = implode("\n", $product_quantities);

    // Insert the order into tbl_order, including address, phone number, and order code
    $sql_insert_order = "INSERT INTO tbl_order (user_id, order_cust, payment_type, cust_address, cust_number, order_amount, cust_items, order_code)
                        VALUES ('$user_id', '$customer_name', '$payment_type', '$cust_address', '$cust_number', $order_amount, '$cust_items', $order_code)";
    $conn->query($sql_insert_order);

    // Commit the transaction
    $conn->commit();

    echo "Order placed successfully!";
} catch (Exception $e) {
    // Rollback the transaction if an error occurred
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn->close();
?>
