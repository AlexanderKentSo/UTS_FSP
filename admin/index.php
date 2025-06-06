<?php
session_start();

$message = "Welcome";

if($_SESSION["USER"]=="admin"){
    $message = "Welcome, ".$_SESSION["USER"];
} else{
    header("location: ../logout.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee StartBug</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
    <div id="content">
        <a href="../logout.php">logout</a>
        <h1>Admin</h1>
        <p><?=$message?></p>
        <a href="jenismenu.php">Kelola jenis menu</a>
        <br>
        <a href="menu.php">Kelola menu</a>
        <br>
        <a href="voucher.php">Kelola voucher</a>
        <br>
        <a href="member.php">Kelola member</a>
    </div>
</body>
</html>