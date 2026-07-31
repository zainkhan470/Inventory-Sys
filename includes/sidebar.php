<?php
if (!isset($_SESSION)) session_start();
$role = $_SESSION['role'] ?? 'salesman'; // default to salesman if not set

// Detect base path - check if we're in a module or root
$current_file = $_SERVER['PHP_SELF'];
$base_path = '';
if (strpos($current_file, '/modules/') !== false) {
    $base_path = '../../'; // We're in a module, go up two levels
} else {
    $base_path = ''; // We're in root
}
?>

<style>
    /* Sidebar styles */
    .sidebar {
        width: 220px;
        background: #031f3bff;
        color: #ffffffff;
        display: flex;
        flex-direction: column;
        padding: 20px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 100;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 22px;
        color: #fff;
    }

    .sidebar a,
    .sidebar .submenu a {
        display: block;
        padding: 12px 15px;
        margin-bottom: 10px;
        color: #ecf0f1;
        text-decoration: none;
        border-radius: 6px;
        transition: 0.3s;
        cursor: pointer;
    }

    .sidebar a:hover,
    .sidebar .submenu a:hover {
        background: #00152bff;
    }

    .sidebar a.active,
    .sidebar .submenu a.active {
        background: #00152bff;
    }

    .sidebar .logout {
        margin-top: auto;
        background: #e74c3c;
        text-align: center;
    }

    .sidebar .logout:hover {
        background: #c0392b;
    }

    /* Submenu styles */
    .submenu {
        display: none;
        flex-direction: column;
        margin-left: 10px;
    }

    .submenu a {
        padding-left: 30px;
        font-size: 14px;
    }

    /* Top Navbar styles */
    .topnav {
        position: fixed;
        top: 0;
        left: 220px;
        right: 0;
        height: 60px;
        background: #031f3bff;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        z-index: 90;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .topnav .title {
        font-size: 18px;
        font-weight: bold;
    }

    .topnav .actions a {
        color: #fff;
        text-decoration: none;
        margin-left: 15px;
        padding: 6px 12px;
        border-radius: 4px;
        background: #013d7eff;
        transition: 0.3s;
    }

    .topnav .actions a:hover {
        background: #00152bff;
    }

    /* Main content adjustment */
    .main-content {
        margin-left: 220px;
        margin-top: 60px;
        padding: 20px;
    }

    @media (max-width: 768px) {
        .sidebar {
            position: relative;
            width: 100%;
            height: auto;
            flex-direction: row;
            overflow-x: auto;
        }

        .sidebar h2 {
            display: none;
        }

        .sidebar a {
            margin-right: 10px;
            margin-bottom: 0;
        }

        .topnav {
            left: 0;
        }

        .main-content {
            margin-left: 0;
            margin-top: 120px;
        }
    }
</style>

<!-- Sidebar -->
<div class="sidebar">
    <h2>
        <img src="<?= $base_path ?>assets/naicon.png" alt="Logo" style="width:100px; height:100px; vertical-align:middle; margin-right:8px;">
        Inventory
    </h2>

    <!-- Dashboard -->
    <a href="<?= $base_path ?>dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>

    <!-- Stock Menu -->
    <a class="toggle-btn">Stock &#9662;</a>
    <div class="submenu">
        <a href="<?= $base_path ?>modules/items/itemslist.php" class="<?= basename($_SERVER['PHP_SELF']) == 'itemslist.php' ? 'active' : '' ?>">Items List</a>
        <a href="<?= $base_path ?>modules/items/category.php" class="<?= basename($_SERVER['PHP_SELF']) == 'category.php' ? 'active' : '' ?>">Category</a>
    </div>

    <!-- Customers & Sales Menu -->
    <a class="toggle-btn">Customers & Sales &#9662;</a>
    <div class="submenu">
        <a href="<?= $base_path ?>modules/customers/customers.php" class="<?= basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : '' ?>">Customer List</a>

        <?php if (in_array($role, ['owner', 'salesman'])): ?>
            <a href="<?= $base_path ?>modules/invoices/cart.php" class="<?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : '' ?>">Create Invoice</a>
        <?php endif; ?>

        <?php if ($role === 'owner'): ?>
            <a href="<?= $base_path ?>modules/invoices/invoices.php" class="<?= basename($_SERVER['PHP_SELF']) == 'invoices.php' ? 'active' : '' ?>">Invoices List</a>
        <?php endif; ?>
    </div>

    <!-- Companies Menu -->
    <a class="toggle-btn">Comp/Vendors &#9662;</a>
    <div class="submenu">
        <a href="<?= $base_path ?>modules/companies/companies.php" class="<?= basename($_SERVER['PHP_SELF']) == 'companies.php' ? 'active' : '' ?>">Company List</a>
        <a href="<?= $base_path ?>modules/companies/company_items.php" class="<?= basename($_SERVER['PHP_SELF']) == 'company_items.php' ? 'active' : '' ?>">Company Items</a>

        <?php if ($role === 'owner'): ?>
            <a href="<?= $base_path ?>modules/companies/company_bills.php" class="<?= basename($_SERVER['PHP_SELF']) == 'company_bills.php' ? 'active' : '' ?>">Company Bills</a>
        <?php endif; ?>
    </div>
</div>

<!-- Top Navbar -->
<div class="topnav">
    <div class="title"></div>
    <div class="actions">
        <a href="<?= $base_path ?>modules/auth/logout.php">Logout</a>
    </div>
</div>

<script>
    // Expand/collapse submenu (only one open at a time)
    const toggles = document.querySelectorAll('.toggle-btn');
    toggles.forEach(btn => {
        btn.addEventListener('click', () => {
            const submenu = btn.nextElementSibling;
            const allSubmenus = document.querySelectorAll('.submenu');
            allSubmenus.forEach(sm => {
                if (sm !== submenu) sm.style.display = 'none';
            });
            submenu.style.display = submenu.style.display === 'flex' ? 'none' : 'flex';
        });
    });
</script>
