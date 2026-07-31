<?php
session_start();
include "../../config/database.php";

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: itemslist.php");
    exit;
}

$id = $_GET['id'];

// Delete item
$conn->query("DELETE FROM items WHERE id='$id'");

// Redirect back to item list
header("Location: itemslist.php");
exit;
