<?php
include '../dbconnect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(array('error' => 'User not logged in'));
    exit;
}

$user_id = $_SESSION['user_id'];

// Function to calculate revenue
function calculateRevenue($items, &$revenueByDate, $orderDate, $conn) {
    foreach ($items as $item) {
        $parts = explode(" ------ ", $item);
        if (count($parts) == 2) {
            $productName = trim($parts[0]);
            $quantity = intval(trim($parts[1]));
            // Fetch product price and cost from tbl_product
            list($price, $cost) = getProductPriceAndCost($productName, $conn);
            $netRevenue = ($price - $cost) * $quantity;
            if (!isset($revenueByDate[$orderDate])) {
                $revenueByDate[$orderDate] = 0;
            }
            $revenueByDate[$orderDate] += $netRevenue;
        }
    }
}

// Fetch orders and their items from both tables
$tables = ['tbl_order', 'tbl_completeorder'];
$revenueByDate = [];

foreach ($tables as $table) {
    $sql = "SELECT order_date, cust_items FROM $table WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $orderDate = substr($row['order_date'], 0, 10); // Extract just the date part
            $items = explode("\n", $row['cust_items']);
            calculateRevenue($items, $revenueByDate, $orderDate, $conn);
        }
    }
}

header('Content-Type: application/json');
echo json_encode([
    'dates' => array_keys($revenueByDate),
    'revenues' => array_values($revenueByDate)
]);

mysqli_stmt_close($stmt);
mysqli_close($conn);

function getProductPriceAndCost($productName, $conn) {
    $sql = "SELECT product_price, product_cost FROM tbl_product WHERE product_name = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $productName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return [$row['product_price'], $row['product_cost']];
    }
    return [0, 0]; // Return 0 if no price or cost is found
}
?>