<?php
session_start();
include "config/database.php"; // database connection

// If already logged in, redirect to index
if (isset($_SESSION['user_id'])) {
        header("Location: index.php");
    exit;
}

$error = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from database
    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Password correct → start session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = strtolower(trim($user['role']));


        header("Location: dashboard.php"); // redirect to item list
        exit;
    } else {
        $error = "Invalid Username or Password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(#75abe9ff, #c5d1e7ff);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            background: #93bcddff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(243, 241, 241, 0.2);
            width: 350px;
            text-align: center;
        }

        .login-card h2 {
            margin-bottom: 25px;
            color: #333;
        }

        .login-card input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .login-card button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background-color: #2575fc;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-card button:hover {
            background-color: #6a11cb;
        }

        .login-card .footer-text {
            margin-top: 15px;
            font-size: 14px;
            color: #ffffffff;
        }

        .login-card .footer-text a {
            color: #2575fc;
            text-decoration: none;
            margin-left: 5px;
        }

        .login-card .footer-text a:hover {
            text-decoration: underline;
        }

        .login-card .error {
            color: red;
            font-size: 13px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <h2>Login</h2>

        <?php if ($error) echo "<p class='error'>$error</p>"; ?>

        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <div class="footer-text">
            Don't have an account? <a href="modules/auth/register.php">Register</a>
        </div>
    </div>

</body>

</html>