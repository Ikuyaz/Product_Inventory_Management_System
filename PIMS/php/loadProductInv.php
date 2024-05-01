<?php
// Include the database connection file
include 'dbconnect.php';

// Start the session to access session variables
session_start();

// Check if the user is logged in and has a user_id set
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, send an error response
    echo json_encode(array('error' => 'User not logged in'));
    exit;
}

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Initialize an array to store the product data
$productData = array();

// SQL query to retrieve product information and join with supplier details to get supp_id
// Adjust the query to perform an INNER JOIN based on supplier_contact matching supp_phone
// and filter the results by the logged-in user's user_id
$sql = "SELECT p.product_sku, s.supp_id, p.product_name, p.product_location, p.product_quantity, p.product_cost, p.product_price
        FROM tbl_product p
        INNER JOIN tbl_supplier s ON p.supplier_contact COLLATE utf8mb4_unicode_ci = s.supp_phone COLLATE utf8mb4_unicode_ci
        WHERE p.user_id = ?"; // Use a prepared statement placeholder for security

// Prepare the SQL statement to prevent SQL injection
$stmt = mysqli_prepare($conn, $sql);

// Bind the user_id parameter to the prepared statement
mysqli_stmt_bind_param($stmt, "s", $user_id);

// Execute the prepared statement
mysqli_stmt_execute($stmt);

// Get the result of the query
$result = mysqli_stmt_get_result($stmt);

try {
    if ($result) {
        // Check if any rows are returned
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $productData[] = $row;
            }
        }
        // Return JSON response with product data
        header('Content-Type: application/json');
        echo json_encode($productData);
    } else {
        // If the query fails, throw an exception with mysqli_error
        throw new Exception("Query failed: " . mysqli_error($conn));
    }
} catch (Exception $e) {
    // Return a JSON object with an error message
    header('Content-Type: application/json');
    ob_clean(); // Clean (erase) the output buffer
    echo json_encode(array('error' => $e->getMessage()));
    exit;
}
?>