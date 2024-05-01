<?php
// Include the database connection file
include '../dbconnect.php';

// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, send an error response
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// Retrieve user_id from the session
$user_id = $_SESSION['user_id'];

// Check if productName is set in the GET request
if(isset($_GET['productName'])) {
    $productName = $_GET['productName'];

    // SQL query to retrieve supplier contacts for the specific user and product name
    $sql = "SELECT product_price
            FROM tbl_supplier 
            WHERE user_id = '$user_id' AND supp_product = ?";
    
    // Prepare and bind parameters
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $productName);
    
    // Execute the query
    if(mysqli_stmt_execute($stmt)) {
        // Bind result variables
        mysqli_stmt_bind_result($stmt, $productCost);
        
        // Fetch the results
        $prices = array();
        while(mysqli_stmt_fetch($stmt)) {
            $prices[] = $productCost;
        }
        
        // Return the product price as JSON
        echo json_encode(['success' => true, 'product_price' => $prices]);
    } else {
        // Handle query error
        echo json_encode(['success' => false, 'error' => 'Query execution failed: ' . mysqli_error($conn)]);
    }
    
    // Close statement
    mysqli_stmt_close($stmt);
} else {
    // If productName is not set in the GET request
    echo json_encode(['success' => false, 'error' => 'Product price not provided']);
}

// Close connection
mysqli_close($conn);
?>

