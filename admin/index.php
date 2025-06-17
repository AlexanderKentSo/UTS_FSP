<?php
session_start();

$message = "Welcome";

if($_SESSION["USER"]=="admin"){
    $message = "Welcome, ".$_SESSION["USER"];
} else{
    header("location: ../logout.php");
    exit(); // Always call exit after a header redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee StartBug - Admin</title>
    <link rel="stylesheet" href="../css/index.css"> <style>

    </style>
</head>
<body>
    <header id="header">
        <div style="display: flex; gap: 20px;">
            <a href="index.php">Admin Home</a>
        </div>
        <a href="../logout.php" style="position: absolute; right: 30px;">Log out</a>
    </header>
    
    <div style="
        height: 100vh;
        width: 100%;
        position: fixed;
        left: 0;
        top: 0;
        background-image: url('https://i.pinimg.com/736x/1c/cc/8c/1ccc8c68fd5d9f7b283b8cd64c5dc567.jpg');
        background-size: cover;
        background-position: center;
        z-index: -1;">
    </div>
    
    <div style="
        background-color: rgba(0,0,0,0.6);
        height: 100vh;
        width: 100%;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 0;">
    </div>
    
    <div style="
        position: relative;
        padding: 60px 20px 20px;
        color: antiquewhite;">
    
        <p style="font-size: 40px;"><?=$message?></p>

        <div class="admin-links">
            <a href="jenismenu.php">Kelola Jenis Menu</a>
            <a href="menu.php">Kelola Menu</a>
            <a href="voucher.php">Kelola Voucher</a>
            <a href="member.php">Kelola Member</a>
        </div>
    </div>
</body>
</html>