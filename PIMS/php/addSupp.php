<?php
include 'dbconnect.php';

// Start or resume the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, send an error response
    echo "error: User not logged in.";
    exit;
}

// Retrieve user_id from the session
$user_id = $_SESSION['user_id'];

// Generate new supplier ID
$new_supp_id = generateSupplierID($conn, $user_id);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize user inputs
    $supp_name = mysqli_real_escape_string($conn, $_POST['supp_name']);
    $supp_phone = mysqli_real_escape_string($conn, $_POST['supp_phone']);
    $supp_email = mysqli_real_escape_string($conn, $_POST['supp_email']);
    $supp_product = mysqli_real_escape_string($conn, $_POST['supp_product']);
    $product_price = (float)$_POST['product_price'];

    // Insert supplier data into the database, including user_id
    $sql = "INSERT INTO tbl_supplier (supp_id, supp_name, supp_phone, supp_email, supp_product, product_price, user_id)
            VALUES ('$new_supp_id', '$supp_name', '$supp_phone', '$supp_email', '$supp_product', '$product_price', '$user_id')";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        // If there is an error during insertion, return a descriptive error message
        echo "error: Failed to add supplier. Database error: " . $conn->error;
    }
}

// Close connection
$conn->close();

// Function to generate a new supplier ID
function generateSupplierID($conn) {
    // Find the maximum existing supplier ID
    $result = $conn->query("SELECT MAX(supp_id) AS max_id FROM tbl_supplier");
    if ($result && $row = $result->fetch_assoc()) {
        $max_id = $row['max_id'];
        // Increment the max ID to generate the new ID
        if ($max_id) {
            // Increment the number and format the new ID as S001, S002, and so on
            $num = intval(substr($max_id, 1)) + 1;
        } else {
            // If no existing max ID, start with 1
            $num = 1;
        }
        // Pad the number to 3 digits and prepend 'S'
        $new_supp_id = 'S' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        // If there was an error with the query or no existing max ID
        echo "error: Unable to generate a new supplier ID.";
        exit;
    }
    
    return $new_supp_id;
}
?>