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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Form</title>
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

    form {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 300px;
      text-align: center;
      animation: slide-in 0.5s ease-out forwards;
    }

    @keyframes slide-in {
      0% {
        transform: translateY(50px);
        opacity: 0;
      }
      100% {
        transform: translateY(0);
        opacity: 1;
      }
    }

    h2 {
      color: #333;
    }

    label {
      display: block;
      margin-bottom: 8px;
      color: #555;
    }

    input {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    input[type="submit"] {
      padding: 10px;
      background-color: #2ecc71;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
      background-color: #27ae60;
    }

    .form-message {
      margin-top: 10px;
      font-size: 0.8rem;
      color: green;
    }

    .error-message {
      margin-top: 10px;
      font-size: 0.8rem;
      color: red;
    }
  </style>
  <script>
    function validateForm() {
      var password = document.getElementById("password").value;
      var confirmPassword = document.getElementById("confirmPassword").value;
      var formMessage = document.querySelector('.form-message');
      var errorMessage = document.querySelector('.error-message');

      if (password != confirmPassword) {
        errorMessage.textContent = "Passwords do not match.";
        clearInputs();
        return false;
      }

      // Use AJAX to submit the form data to register.php
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "register.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
          if (xhr.responseText === "EMAIL_EXISTS") {
            errorMessage.textContent = "Email already exists. Please choose a different email.";
          } else if (xhr.responseText === "ERROR") {
            errorMessage.textContent = "An error occurred. Please try again.";
          } else {
  var user_id = xhr.responseText;
  formMessage.textContent = "Registration successful! Your User ID is: " + user_id;
  setTimeout(function() {
    window.location.href = "../index.html"; // Adjust the path based on your directory structure
  }, 2000);
}
        }
      };

      // Get form data and send it as a URL-encoded string
      var formData = "email=" + encodeURIComponent(document.getElementById("email").value) +
                     "&password=" + encodeURIComponent(password) +
                     "&confirmPassword=" + encodeURIComponent(confirmPassword);

      xhr.send(formData);

      return false;
    }

    function clearInputs() {
      document.getElementById("password").value = "";
      document.getElementById("confirmPassword").value = "";
      document.querySelector('.form-message').textContent = "";
      document.querySelector('.error-message').textContent = "";
    }
  </script>
</head>
<body>

  <form action="register.php" method="post" onsubmit="return validateForm()">

    <h2>Registration Form</h2>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <label for="confirmPassword">Confirm Password:</label>
    <input type="password" id="confirmPassword" name="confirmPassword" required>
    <div class="form-message"></div>
  <div class="error-message"></div>
    <input type="submit" value="Register">

  </form>



</body>
</html>