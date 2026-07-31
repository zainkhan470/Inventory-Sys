<?php
session_start();
include "../../config/database.php";

if(!isset($_SESSION['user_id'])){ header("Location: ../index.php"); exit; }

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM companies WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();

if(!$company) die("Company not found");

$error = '';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $name=trim($_POST['name']);
    $email=trim($_POST['email']);
    $phone=trim($_POST['phone']);
    $address=trim($_POST['address']);

    if(empty($name)) $error="Company name is required";
    else{
        $stmt = $conn->prepare("UPDATE companies SET name=?, email=?, phone=?, address=? WHERE id=?");
        $stmt->bind_param("ssssi",$name,$email,$phone,$address,$id);
        $stmt->execute();
        header("Location: companies.php"); exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Company</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:'Segoe UI', sans-serif;}
        body{display:flex; min-height:100vh; background: #c5d1e7ff;}
        .main{margin-left:220px; margin-top:60px; padding:20px; flex:1;}
        .card{max-width:500px;background:#fff;padding:25px;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.08);}
        h2{margin-bottom:20px;color:#2c3e50;}
        input,textarea,button{width:100%;padding:10px;margin-top:10px;border-radius:6px;border:1px solid #ccc;}
        button{background:#007BFF;color:#fff;border:none;cursor:pointer;margin-top:15px;}
        button:hover{background:#0056b3;}
        .error{color:#c0392b;background:#ffe5e5;padding:10px;border-radius:6px;margin-bottom:10px;}
    </style>
</head>
<body>
<?php include "../../includes/sidebar.php"; ?>

<div class="main">
    <div class="card">
        <h2>Edit Company</h2>
        <?php if($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="post">
            <input type="text" name="name" placeholder="Company Name" value="<?= htmlspecialchars($company['name']) ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($company['email']) ?>">
            <input type="text" name="phone" placeholder="Phone" value="<?= htmlspecialchars($company['phone']) ?>">
            <textarea name="address" placeholder="Address"><?= htmlspecialchars($company['address']) ?></textarea>
            <button type="submit">Update Company</button>
        </form>
    </div>
</div>
</body>
</html>
