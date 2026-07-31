<?php
session_start();
include "../../config/database.php";

// Protect page: only logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Fetch all items from DB
$result = $conn->query("SELECT * FROM items ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Item Listing</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: #c5d1e7ff;
        }

        .main {
            margin-left: 220px;
            /* sidebar width */
            margin-top: 60px;
            /* top navbar height if any */
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
            padding: 12px 15px;
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
    <?php include '../../includes/sidebar.php'; ?>

    <div class="main">
        <h2 class="section-title">📦 Item List</h2>
        <a href="add_item.php" class="btn">+ Add Item</a>

        <table>
            <tr>
                <th>ID</th>
                <th>Company</th>
                <th>Category</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Expiry Date</th>
                <th>Actions</th>
            </tr>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['company']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>$<?= number_format($row['price'], 2) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= $row['expiry_date'] ?></td>
                        <td>
                            <a href="edit_item.php?id=<?= $row['id'] ?>" class="action-btn">Edit</a>
                            <a href="delete_item.php?id=<?= $row['id'] ?>" class="action-btn" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center;color:#777;">No items found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>