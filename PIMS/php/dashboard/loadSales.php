<?php
// Include the database connection file
include '../dbconnect.php';

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

// SQL query to retrieve product names
$sql = "SELECT p.product_name
        FROM tbl_product p
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
                // Get the quantity sold for each product
                $quantitySold = getQuantitySold($row['product_name'], $conn);

                $productData[] = array(
                    'product_name' => $row['product_name'],
                    'quantity_sold' => $quantitySold
                );
            }
        }
        // Format data for pie chart
        $barChartData = formatForBarChart($productData);
        // Return JSON response with product data formatted for pie chart
        header('Content-Type: application/json');
        echo json_encode($barChartData);
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

// Function to format data for pie chart
function formatForBarChart($data) {
    // Directly return the data array without additional formatting
    return $data;
}

// Function to get the quantity sold for a specific product
function getQuantitySold($productName, $conn) {
    $totalQuantity = 0;

    // Queries to get cust_items from both tbl_order and tbl_completeorder
    $queries = [
        "SELECT cust_items FROM tbl_order WHERE user_id = ?",
        "SELECT cust_items FROM tbl_completeorder WHERE user_id = ?"
    ];

    foreach ($queries as $query) {
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $entries = explode("\n", $row['cust_items']);
                foreach ($entries as $entry) {
                    $parts = explode(" ------ ", $entry);
                    if (count($parts) == 2) { // Ensure that both product name and quantity are present
                        list($entryProductName, $quantity) = $parts;
                        if (trim($entryProductName) === $productName) {
                            $totalQuantity += intval(trim($quantity));
                        }
                    }
                }
            }
        }
    }

    return $totalQuantity;
}
?>