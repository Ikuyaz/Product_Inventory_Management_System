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
    $updateProductSKU = mysqli_real_escape_string($conn, $_POST['updateProductSKU']);
    $updateProductName = mysqli_real_escape_string($conn, $_POST['updateProductName']);
    $updateProductLocation = mysqli_real_escape_string($conn, $_POST['updateProductLocation']);
    $updateProductQuantity = (int)$_POST['updateProductQuantity'];
    $updateProductPrice = (float)$_POST['updateProductPrice'];
    $updateProductCost = (float)$_POST['updateProductCost']; // Added field for product cost
    $updateSupplierContact = mysqli_real_escape_string($conn, $_POST['updateSupplierContact']);

    // Fetch existing product details from the database
    $selectSql = "SELECT * FROM tbl_product WHERE product_sku = '$updateProductSKU' AND user_id = '$user_id'";
    $result = $conn->query($selectSql);

    if ($result->num_rows > 0) {
        $existingProductDetails = $result->fetch_assoc();

        // Check if the location is unique for the current user across all products
        $checkLocationSql = "SELECT * FROM tbl_product WHERE product_location = '$updateProductLocation' AND user_id = '$user_id' AND product_sku != '$updateProductSKU'";
        $locationResult = $conn->query($checkLocationSql);

        // Check if the name is unique for the current user across all products
        $checkNameSql = "SELECT * FROM tbl_product WHERE product_name = '$updateProductName' AND user_id = '$user_id' AND product_sku != '$updateProductSKU'";
        $nameResult = $conn->query($checkNameSql);

        if ($locationResult->num_rows > 0 || $nameResult->num_rows > 0) {
            echo json_encode(array('error' => 'Error updating product: Name and Location must be unique for the current user.'));
            exit;
        }

        // Compare existing values with the submitted values
        $updateFields = array();
        if ($updateProductName !== $existingProductDetails['product_name']) {
            $updateFields[] = "product_name = '$updateProductName'";
        }
        if ($updateProductLocation !== $existingProductDetails['product_location']) {
            $updateFields[] = "product_location = '$updateProductLocation'";
        }
        if ($updateProductQuantity !== $existingProductDetails['product_quantity']) {
            $updateFields[] = "product_quantity = $updateProductQuantity";
        }
        if ($updateProductPrice !== $existingProductDetails['product_price']) {
            $updateFields[] = "product_price = $updateProductPrice";
        }
        if ($updateProductCost !== $existingProductDetails['product_cost']) {
            $updateFields[] = "product_cost = $updateProductCost"; // Add field for product cost
        }
        if ($updateSupplierContact !== $existingProductDetails['supplier_contact']) {
            $updateFields[] = "supplier_contact = '$updateSupplierContact'";
        }

        // Construct the dynamic update SQL query
        if (!empty($updateFields)) {
            $updateSql = "UPDATE tbl_product SET " . implode(', ', $updateFields) . " WHERE product_sku = '$updateProductSKU' AND user_id = '$user_id'";

            if ($conn->query($updateSql) === TRUE) {
                echo json_encode(array('success' => 'Product updated successfully'));
            } else {
                echo json_encode(array('error' => 'Error updating product: ' . $conn->error));
            }
        } else {
            // No fields were changed
            echo json_encode(array('success' => 'No changes made to the product'));
        }
    } else {
        // Return an error message if no product is found
        echo json_encode(array('error' => 'Product not found.'));
    }
} else {
    // If not a POST request, fetch the product details based on the selected product SKU
    $selectedProductSKU = isset($_GET['product_sku']) ? mysqli_real_escape_string($conn, $_GET['product_sku']) : '';

    if ($selectedProductSKU !== '') {
        // Fetch product details from the database
        $selectSql = "SELECT * FROM tbl_product WHERE product_sku = '$selectedProductSKU' AND user_id = '$user_id'";
        $result = $conn->query($selectSql);

        if ($result->num_rows > 0) {
            // Fetch product details as an associative array
            $productDetails = $result->fetch_assoc();

            // Return product details as JSON
            echo json_encode($productDetails);
        } else {
            // Return an error message if no product is found
            echo json_encode(array('error' => 'Product not found.'));
        }
    } else {
        // Return an error message if product SKU is not set
        echo json_encode(array('error' => 'Product SKU not provided.'));
    }
}

// Close connection
$conn->close();
?>
