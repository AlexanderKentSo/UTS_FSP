<?php
require_once("../class/classVoucher.php");
require_once("../class/classJenisMenu.php");
require_once("../class/classMenu.php");
session_start();

// Security check for admin access
if ($_SESSION["USER"] != "admin") {
    header("location: ../logout.php");
    exit();
}

$voucher = new classVoucher();
$_JenisMenu = new classJenisMenu();
$_Menu = new classMenu();
$message = "";

// buat inisiasi data
if(isset($_GET['kode'])){
    $kode = $_GET['kode'];
    $res = $voucher->getVoucherKode($kode);
    $row = $res->fetch_assoc();

    $nama = $row['nama'];
    $jenis = $row['kode_jenis'] ?? "";
    $menu = $row['kode_menu'] ?? "";
    $start = $row["mulai_berlaku"];
    $end = $row["akhir_berlaku"];
    $kuota = $row["kuota_max"];
    $diskon = $row["persen_diskon"];
    $start = date('Y-m-d', strtotime($start));
    $end = date('Y-m-d', strtotime($end));
}

// buat update voucher
if(isset($_POST['update'])){
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $jenis = ($_POST['jenis'] === 'none') ? NULL : $_POST['jenis'];
    $menu = ($_POST['menu'] === 'none') ? NULL : $_POST['menu'];
    $start = $_POST["start"];
    $end = $_POST["end"];
    $kuota = $_POST["kuota"];
    $diskon = $_POST["diskon"];
    $voucher->updateVoucher($menu, $jenis, $nama, $start, $end, $kuota, $diskon, $kode);
    header("Location: voucher.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee StartBug - Ubah Voucher</title>
    <link rel="stylesheet" href="../css/index.css">   <link rel="stylesheet" href="../css/voucher.css"> </head>
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
        <h1 style="font-size: 80px; margin: 0;">Update Voucher: <?=$nama?></h1>
        <p style="font-size: 24px; margin-top: 10px;"><?=$message?></p>

        <div class="form-container">
            <h2>Ubah Detail Voucher</h2>
            <form action="ubahvoucher.php" method="post">
                <input type="hidden" name="kode" value="<?=$kode?>">

                <label for="nama">Nama Voucher: </label>
                <input type="text" name="nama" id="nama" value="<?=$nama?>">

                <label for="jenis">Jenis Menu yang diskon: </label>
                <select name="jenis" id="jenis">
                    <?php
                    // Pastikan value 'none' tidak diset sebagai selected disabled hidden bersamaan
                    if($jenis === "" || $jenis === null){ // Use strict comparison for empty/null check
                        echo "<option value='none' selected disabled hidden>Select an Option</option>";
                    }
                    echo "<option value='none'></option>"; // Empty option for unsetting
                    $resJenis = $_JenisMenu->getJenisMenu();
                    while($rowJenis = $resJenis->fetch_assoc()) { 
                        $selected = ($rowJenis['kode'] == $jenis) ? "selected" : "";
                        echo "<option value=\"".$rowJenis["kode"]."\" ".$selected.">".$rowJenis['nama']."</option>";
                    }
                    ?>
                </select>

                <label for="menu">Menu yang diskon: </label>
                <select name="menu" id="menu">
                    <?php
                    if($menu === "" || $menu === null){ // Use strict comparison for empty/null check
                        echo "<option value='none' selected disabled hidden>Select an Option</option>";
                    }
                    echo "<option value='none'></option>"; // Empty option for unsetting
                    $resMenu = $_Menu->getMenu();
                    while($rowMenu = $resMenu->fetch_assoc()) { 
                        $selected = ($rowMenu['kode'] == $menu) ? "selected" : "";
                        echo "<option value=\"".$rowMenu["kode"]."\" ".$selected.">".$rowMenu['nama_m']."</option>";
                    }
                    ?>
                </select>

                <label for="start">Tanggal mulai: </label>
                <input type="date" name="start" id="start" value="<?=$start?>">

                <label for="end">Tanggal berakhir: </label>
                <input type="date" name="end" id="end" value="<?=$end?>">

                <label for="kuota">Kuota maks: </label>
                <input type="number" name="kuota" id="kuota" min="0" value="<?=$kuota?>">

                <label for="diskon">Persen Diskon: </label>
                <input type="number" name="diskon" id="diskon" min="0" max="100" value="<?=$diskon?>">

                <input type="submit" value="update" name="update">
            </form>
        </div>
    </div>
</body>
</html>