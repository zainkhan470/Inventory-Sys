<?php include "../../config/database.php"; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Username or Email already exists!";
    } else {
        $insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $username, $email, $password);
        $insert->execute();
        header("Location: ../index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <style>
        /* Reset some basic styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(#75abe9ff, #c5d1e7ff);
        }

        .container {
             background: #93bcddff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        form input {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            transition: 0.3s;
        }

        form input:focus {
            border-color: #007BFF;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #007BFF;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        .error {
            background: #ffe5e5;
            color: #ff0000;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }

        @media(max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Create Account</h2>

        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <h3 style="text-align: center;">OR</h3>
        <button onclick="window.location.href='../index.php'" class="btn">Login</button>
    </div>

</body>

</html>
