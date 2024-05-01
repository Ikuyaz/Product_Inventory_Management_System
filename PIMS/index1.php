<?php
// Assuming your database connection code is in a file named dbconnect.php
include 'dbconnect.php';

// Initialize variables
$loginSuccess = false;
$user_id = '';
$email = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validate the user credentials
    $query = "SELECT * FROM users WHERE email = ? AND password = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Authentication successful, you can redirect or set session variables
            $loginSuccess = true;
            $user_id = $row['user_id'];
        } else {
            echo "Invalid email or password.";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "ERROR: Could not prepare query: $query. " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background: linear-gradient(to right, #3498db, #2c3e50);
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .center {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 300px;
      text-align: center;
    }

    h2 {
      color: #333;
    }

    form {
      margin-top: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      color: #555;
    }

    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 4px;
      transition: border-color 0.3s ease;
    }

    input:focus {
      border-color: #2ecc71;
    }

    .button-container {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }

    button, input[type="submit"] {
      padding: 12px;
      background-color: #2ecc71;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      width: 45%;
      transition: background-color 0.3s ease;
    }

    button:hover, input[type="submit"]:hover {
      background-color: #27ae60;
    }

    .notification {
      margin-top: 20px;
      padding: 10px;
      border-radius: 4px;
      color: #fff;
      font-size: 14px;
      display: none;
    }

    .notification.success {
      background-color: #2ecc71;
    }

    .notification.error {
      background-color: #e74c3c;
    }
  </style>
  <script>
    function showPrompt() {
      <?php
      if ($loginSuccess) {
        echo "showNotification('Login Successful', 'success');";
      } else {
        echo "showNotification('Invalid email or password.', 'error');";
      }
      ?>
    }

    function showNotification(message, type) {
      var notification = document.getElementById('notification');
      notification.textContent = message;
      notification.className = 'notification ' + type;
      notification.style.display = 'block';
      setTimeout(function () {
        notification.style.display = 'none';
      }, 3000);
    }
  </script>
</head>
<body>

<div class="center">
  <h2>Login Form</h2>

  <form action="dashboard.html" method="post" onsubmit="showPrompt()">
    <label for="email">Email:</label>
    <input type="text" id="email" name="email" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <div class="button-container">
      <input type="submit" value="Login">
      <button onclick="location.href='register.php'" type="button">Register</button>
    </div>
  </form>

  <div id="notification" class="notification"></div>
</div>

</body>
</html>


