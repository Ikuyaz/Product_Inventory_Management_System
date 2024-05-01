<?php
include 'dbconnect.php';

// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, send an error response
    echo 0;
    exit;
}

// Retrieve user_id from the session
$user_id = $_SESSION['user_id'];

// Fetch product count from the database for the specific user
$sql = "SELECT COUNT(*) as count FROM tbl_product WHERE user_id = '$user_id'";
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo $row['count'];
} else {
    echo 0;
}

// Close the database connection
$conn->close();
?>
