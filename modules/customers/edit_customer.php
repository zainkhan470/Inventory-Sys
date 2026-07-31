<?php
session_start();
include "../../config/database.php";

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Get customer ID
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: customer.php");
    exit;
}

// Fetch customer data
$stmt = $conn->prepare("SELECT * FROM customers WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: customer.php");
    exit;
}
$customer = $result->fetch_assoc();
$stmt->close();

$errors = [];
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validate
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";

    // Check for unique email
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM customers WHERE email=? AND id<>? LIMIT 1");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors[] = "Email already exists";
        $stmt->close();
    }

    // Update in DB
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE customers SET name=?, email=?, phone=?, address=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $address, $id);
        if ($stmt->execute()) {
            $success = "Customer updated successfully!";
            $customer = ['name' => $name, 'email' => $email, 'phone' => $phone, 'address' => $address]; // update form values
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Customer</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: #c5d1e7ff;
        }

        .main {
            margin-left: 220px;
            margin-top: 60px;
            padding: 20px;
            flex: 1;
        }

        h2.section-title {
            margin-bottom: 15px;
            color: #2c3e50;
        }

        form {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
            max-width: 600px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        button {
            padding: 10px 18px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 6px;
        }

        .error {
            background: #f8d7da;
            color: #842029;
        }

        .success {
            background: #d1e7dd;
            color: #0f5132;
        }

        @media(max-width:768px) {
            .main {
                margin-left: 0;
                margin-top: 120px;
                padding: 15px;
            }

            form {
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <?php include "../../includes/sidebar.php"; ?>
    <div class="main">
        <h2 class="section-title">✏ Edit Customer</h2>

        <?php if (!empty($errors)): ?>
            <div class="message error">
                <?php foreach ($errors as $err) echo $err . "<br>"; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Name *</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($customer['name']) ?>" required>

            <label for="email">Email *</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($customer['email']) ?>" required>

            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($customer['phone']) ?>">

            <label for="address">Address</label>
            <textarea name="address" id="address"><?= htmlspecialchars($customer['address']) ?></textarea>

            <button type="submit">Update Customer</button>
        </form>
    </div>
</body>

</html>