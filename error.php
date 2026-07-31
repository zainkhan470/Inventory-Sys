<?php
/**
 * Error Page
 * Displays error messages for 404, 500, etc.
 */

$error_code = $_GET['code'] ?? '404';
$error_messages = [
    '404' => 'Page Not Found',
    '500' => 'Internal Server Error',
    '403' => 'Forbidden',
];

$error_title = $error_messages[$error_code] ?? 'Error';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $error_title ?> - Inventory Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(#75abe9ff, #c5d1e7ff);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }
        
        .error-container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
        }
        
        h1 {
            font-size: 72px;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        
        p {
            color: #666;
            margin-bottom: 30px;
        }
        
        a {
            display: inline-block;
            padding: 12px 24px;
            background: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        }
        
        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1><?= $error_code ?></h1>
        <h2><?= $error_title ?></h2>
        <p>Sorry, the page you are looking for could not be found or an error occurred.</p>
        <a href="index.php">Go to Home Page</a>
    </div>
</body>
</html>
