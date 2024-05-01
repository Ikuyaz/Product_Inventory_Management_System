<?php
// Include the database connection file
include 'dbconnect.php';

// Retrieve the product name from the GET parameters
$productName = $_GET['productName'];

// Initialize the total quantity for the product
$totalQuantity = 0;

// Query to select all cust_items from tbl_order
$query = "SELECT cust_items FROM tbl_order";
$result = $conn->query($query);

// Check if there are any rows returned
if ($result->num_rows > 0) {
    // Iterate through each row in the result set
    while ($row = $result->fetch_assoc()) {
        // Each cust_items field contains strings in the format 'Product Name ------ Quantity'
        // Split the string by newline to get each product's entry
        $entries = explode("\n", $row['cust_items']);
        
        foreach ($entries as $entry) {
            // Split each entry by '------' to separate product name and quantity
            list($entryProductName, $quantity) = explode(" ------ ", $entry);
            
            // Trim to remove any leading/trailing whitespace
            $entryProductName = trim($entryProductName);
            $quantity = trim($quantity);
            
            // If the entry's product name matches the requested product name, add the quantity
            if ($entryProductName === $productName) {
                $totalQuantity += intval($quantity);
            }
        }
    }
} else {
    echo "No orders found.";
}

// Close the connection
$conn->close();

// Return JSON response with the total quantity
header('Content-Type: application/json');
echo json_encode(array('success' => true, 'custItems' => $totalQuantity));
?>