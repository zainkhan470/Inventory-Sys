<?php
session_start();
include "../../config/database.php";

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Fetch all customers
$result = $conn->query("SELECT * FROM customers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Customers</title>
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

        /* Main content */
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

        .btn {
            display: inline-block;
            background: #007BFF;
            color: #fff;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .btn:hover {
            background: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
            margin-top: 10px;
        }

        th,
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            text-align: left;
        }

        th {
            background: #00264d;
            color: #fff;
            font-weight: 600;
        }

        tr:hover {
            background: #f1f8ff;
        }

        a.action-btn {
            color: #007BFF;
            text-decoration: none;
            margin-right: 10px;
            font-weight: 500;
        }

        a.action-btn:hover {
            text-decoration: underline;
        }

        @media(max-width:768px) {
            .main {
                margin-left: 0;
                margin-top: 120px;
                padding: 15px;
            }

            th,
            td {
                font-size: 13px;
                padding: 8px 10px;
            }
        }
    </style>
</head>

<body>
    <?php include "../../includes/sidebar.php"; ?>

    <div class="main">
        <h2 class="section-title">👥 Customers</h2>
        <a href="add_customer.php" class="btn">+ Add Customer</a>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="edit_customer.php?id=<?= $row['id'] ?>" class="action-btn">Edit</a>
                            <a href="delete_customer.php?id=<?= $row['id'] ?>" class="action-btn" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;color:#777;">No customers found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>