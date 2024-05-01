<?php
// Include the database connection file
include '../dbconnect.php';

// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, send an error response
    echo json_encode(array('success' => false, 'error' => 'User is not logged in'));
    exit;
}

// Retrieve user_id from the session
$user_id = $_SESSION['user_id'];

// Fetch product names from tbl_supplier
$sql = "SELECT DISTINCT supp_product FROM tbl_supplier WHERE user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // Fetch product names as an array
  $productNames = array();
  while ($row = $result->fetch_assoc()) {
    $productNames[] = $row['supp_product'];
  }

  // Return product names as JSON
  echo json_encode(array('success' => true, 'productNames' => $productNames));
} else {
  // Return an error response if no product names are found
  echo json_encode(array('success' => false, 'error' => 'No product names found'));
}

// Close the database connection
$conn->close();
?>

