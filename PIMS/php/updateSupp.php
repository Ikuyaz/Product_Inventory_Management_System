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
    $supp_id = mysqli_real_escape_string($conn, $_POST['supp_id']);
    $supp_name = mysqli_real_escape_string($conn, $_POST['supp_name']);
    $supp_phone = mysqli_real_escape_string($conn, $_POST['supp_phone']);
    $supp_email = mysqli_real_escape_string($conn, $_POST['supp_email']);
    $supp_product = mysqli_real_escape_string($conn, $_POST['supp_product']);
    $product_price = (float)$_POST['product_price'];

    // Fetch existing supplier details from the database
    $selectSql = "SELECT * FROM tbl_supplier WHERE supp_id = '$supp_id' AND user_id = '$user_id'";
    $result = $conn->query($selectSql);

    if ($result->num_rows > 0) {
        $existingSupplierDetails = $result->fetch_assoc();

        // Compare existing values with the submitted values
        $updateFields = array();
        if ($supp_name !== $existingSupplierDetails['supp_name']) {
            $updateFields[] = "supp_name = '$supp_name'";
        }
        if ($supp_phone !== $existingSupplierDetails['supp_phone']) {
            $updateFields[] = "supp_phone = '$supp_phone'";
        }
        if ($supp_email !== $existingSupplierDetails['supp_email']) {
            $updateFields[] = "supp_email = '$supp_email'";
        }
        if ($supp_product !== $existingSupplierDetails['supp_product']) {
            $updateFields[] = "supp_product = '$supp_product'";
        }
        if ($product_price !== $existingSupplierDetails['product_price']) {
            $updateFields[] = "product_price = $product_price";
        }

        // Construct the dynamic update SQL query
        if (!empty($updateFields)) {
            $updateSql = "UPDATE tbl_supplier SET " . implode(', ', $updateFields) . " WHERE supp_id = '$supp_id' AND user_id = '$user_id'";

            if ($conn->query($updateSql) === TRUE) {
                echo json_encode(array('success' => 'Supplier updated successfully'));
            } else {
                echo json_encode(array('error' => 'Error updating supplier: ' . $conn->error));
            }
        } else {
            // No fields were changed
            echo json_encode(array('success' => 'No changes made to the supplier'));
        }
    } else {
        // Return an error message if no supplier is found
        echo json_encode(array('error' => 'Supplier not found.'));
    }
} else {
    // If not a POST request, fetch the supplier details based on the selected supplier ID
    $selectedSuppID = isset($_GET['supp_id']) ? mysqli_real_escape_string($conn, $_GET['supp_id']) : '';

    if ($selectedSuppID !== '') {
        // Fetch supplier details from the database
        $selectSql = "SELECT * FROM tbl_supplier WHERE supp_id = '$selectedSuppID' AND user_id = '$user_id'";
        $result = $conn->query($selectSql);

        if ($result->num_rows > 0) {
            // Fetch supplier details as an associative array
            $supplierDetails = $result->fetch_assoc();

            // Return supplier details as JSON
            echo json_encode($supplierDetails);
        } else {
            // Return an error message if no supplier is found
            echo json_encode(array('error' => 'Supplier not found.'));
        }
    } else {
        // Return an error message if supplier ID is not set
        echo json_encode(array('error' => 'Supplier ID not provided.'));
    }
}

// Close the database connection
$conn->close();
?>
