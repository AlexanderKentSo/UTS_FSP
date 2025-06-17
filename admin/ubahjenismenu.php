<?php 
require_once("../class/classJenisMenu.php"); 
session_start(); 
$jenisMenu = new classJenisMenu(); 
$message = ""; 

// Security check for admin access
if ($_SESSION["USER"] != "admin") { 
    header("location: ../logout.php"); 
    exit(); 
} 

$kode = $_GET['kode']; 
$nama = $jenisMenu->getJenisMenuKode($kode); 

if(isset($_POST['update'])){ 
    $kode = $_POST['kode']; 
    $nama = $_POST['nama']; 
    $jenisMenu->updateJenisMenu($nama, $kode); 
    header("Location: jenismenu.php"); 
    exit; 
} 
?> 

<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Koffee StartBug - Ubah Jenis Menu</title> 
    <link rel="stylesheet" href="../css/index.css"> 
</head> 
<body> 
    <header id="header">
        <div style="display: flex; gap: 20px;">
            <a href="index.php">Admin Home</a>
            <a href="voucher.php">Kelola Voucher</a>
            <a href="menu.php">Kelola Menu</a>
            <a href="jenismenu.php">Kelola Jenis Menu</a>
            <a href="member.php">Kelola Member</a>
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
    
    <div class="content-wrapper"> 
        <h1 style="font-size: 80px; margin: 0;">Update jenis menu: <?=$nama?></h1> 
        <p style="font-size: 24px; margin-top: 10px;"><?=$message?></p> 

        <div class="form-container">
            <h2>Ubah Data Jenis Menu</h2>
            <form action="ubahjenismenu.php?kode=<?=$kode?>" method="post"> 
                <input type="hidden" name="kode" value="<?=$kode?>"> 
                <label for="nama">Masukan Jenis Menu: </label> 
                <input type="text" name="nama" id="nama" value="<?=$nama?>"> 
                <input type="submit" value="update" name="update"> 
            </form> 
        </div>
    </div> 
</body> 
</html>