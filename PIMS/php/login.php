<?php
session_start(); // Start the session at the beginning of the script
include 'dbconnect.php';

// Start output buffering
ob_start();

$loginSuccess = false;
$user_id = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $query = "SELECT user_id, user_password FROM tbl_user WHERE user_email = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Directly compare plain text password
            if ($password === $row['user_password']) {
                $loginSuccess = true;
                $user_id = $row['user_id'];
                $_SESSION['user_id'] = $user_id; // Set session variable
            }
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);

    // Clear all previous output
    ob_end_clean();

    if ($loginSuccess) {
        // Respond with JSON data indicating success
        header('Content-Type: application/json');
        $redirectUrl = 'pages/home.html';
        echo json_encode(['success' => true, 'redirect' => $redirectUrl, 'user_id' => $user_id]);
    } else {
        // Respond with JSON data indicating failure
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Login failed: Wrong Password and Email']);
    }
    exit();
}
?>