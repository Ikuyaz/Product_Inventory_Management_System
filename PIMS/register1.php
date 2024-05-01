<?php
include 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check if email already exists
    $checkEmailQuery = "SELECT COUNT(*) as count FROM tbl_user WHERE user_email = ?";
    if ($checkEmailStmt = mysqli_prepare($conn, $checkEmailQuery)) {
        mysqli_stmt_bind_param($checkEmailStmt, "s", $email);
        mysqli_stmt_execute($checkEmailStmt);
        $result = mysqli_stmt_get_result($checkEmailStmt);
        $row = mysqli_fetch_assoc($result);

        if ($row['count'] > 0) {
            echo "EMAIL_EXISTS";
            mysqli_stmt_close($checkEmailStmt);
            mysqli_close($conn);
            exit();
        }

        mysqli_stmt_close($checkEmailStmt);
    } else {
        echo "ERROR";
        mysqli_close($conn);
        exit();
    }

    // Generate user_id in the format A001
    $query = "SELECT MAX(user_id) as max_id FROM tbl_user";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $maxId = $row['max_id'];
    $numericPart = (int)substr($maxId, 1);
    $nextId = "A" . sprintf('%03d', $numericPart + 1);

    $insertQuery = "INSERT INTO tbl_user (user_id, user_email, user_password) VALUES (?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $insertQuery)) {
        mysqli_stmt_bind_param($stmt, "sss", $nextId, $email, $password);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            echo $nextId; // Send the user_id to JavaScript
            exit();
        } else {
            echo "ERROR";
        }
    } else {
        echo "ERROR";
    }

    mysqli_close($conn);
}
?>