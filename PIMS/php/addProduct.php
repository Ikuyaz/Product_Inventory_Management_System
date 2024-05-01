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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize user inputs
    $productName = mysqli_real_escape_string($conn, $_POST['productName']);
    $productLocation = mysqli_real_escape_string($conn, $_POST['productLocation']);
    $productQuantity = (int)$_POST['productQuantity'];
    $productPrice = (float)$_POST['productPrice'];
    $supplierContact = mysqli_real_escape_string($conn, $_POST['supplierContact']);
    $productCost = (float)$_POST['productCost']; // Assuming product cost is provided in the form

    // Validate product quantity
    if ($productQuantity <= 10) {
        // Send an error response
        echo "error: Product quantity must be more than 10.";
        exit;
    }

    // Check if the product name is unique for the given user_id
    $checkName = $conn->query("SELECT COUNT(*) as count FROM tbl_product WHERE product_name = '$productName' AND user_id = '$user_id'");
    $rowName = $checkName->fetch_assoc();
    $existingNameCount = $rowName['count'];

    // Check if the product location is unique for the given user_id
    $checkLocation = $conn->query("SELECT COUNT(*) as count FROM tbl_product WHERE product_location = '$productLocation' AND user_id = '$user_id'");
    $rowLocation = $checkLocation->fetch_assoc();
    $existingLocationCount = $rowLocation['count'];

    // Check if either the name or location is not unique for the given user_id
    if ($existingNameCount > 0) {
        echo "error: Product name already exists.";
        exit;
    }

    if ($existingLocationCount > 0) {
        echo "error: Product location already exists.";
        exit;
    }

    // Generate unique random product SKU for the given user_id
    do {
        $newNumber = mt_rand(1, 99999); // Generate a random number between 1 and 99999
        $newSKU = 'SKU-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        // Check if the generated SKU already exists for the given user_id
        $checkExisting = $conn->query("SELECT COUNT(*) as count FROM tbl_product WHERE product_sku = '$newSKU' AND user_id = '$user_id'");
        $row = $checkExisting->fetch_assoc();
        $existingCount = $row['count'];
    } while ($existingCount > 0); // Loop until a unique SKU is generated

    // Insert into the database
    $sql = "INSERT INTO tbl_product (user_id, product_sku, product_name, product_location, product_cost, product_price, product_quantity, supplier_contact) 
            VALUES ('$user_id', '$newSKU', '$productName', '$productLocation', '$productCost', $productPrice, $productQuantity, '$supplierContact')";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>
