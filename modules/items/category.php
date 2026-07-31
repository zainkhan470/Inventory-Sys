<?php
if (!isset($_SESSION)) session_start();
include "../../config/database.php"; // Your DB connection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $category_name = trim($_POST['category_name']);
    if ($category_name != '') {
        $stmt = $conn->prepare("INSERT INTO categories (name, created_at) VALUES (?, NOW())");
        $stmt->bind_param("s", $category_name);
        $stmt->execute();
        $stmt->close();
        header("Location: category.php");
        exit;
    }
}

// Fetch categories
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Categories</title>
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

        /* Add Category Form */
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        form input[type="text"] {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 250px;
        }

        form button {
            padding: 8px 15px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        form button:hover {
            background: #0056b3;
        }

        /* Table */
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

        tr:nth-child(even) {
            background: #f9f9f9;
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

            form input[type="text"] {
                width: 100%;
            }

            form button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <?php include "../../includes/sidebar.php"; ?>

    <div class="main">
        <h2 class="section-title">📂 Categories</h2>

        <!-- Add Category Form -->
        <form method="POST">
            <input type="text" name="category_name" placeholder="Enter category name" required>
            <button type="submit">Add Category</button>
        </form>

        <!-- Category Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= $row['created_at'] ?></td>
                            <td>
                                <a href="edit_category.php?id=<?= $row['id'] ?>" class="action-btn">Edit</a>
                                <a href="delete_category.php?id=<?= $row['id'] ?>" class="action-btn" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center;color:#777;">No categories found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>