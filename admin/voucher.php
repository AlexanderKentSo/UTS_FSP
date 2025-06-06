<?php
require_once("../class/classMenu.php");
require_once("../class/classJenisMenu.php");
require_once("../class/classVoucher.php");
session_start();

$_JenisMenu = new classJenisMenu();
$_Menu = new classMenu();
$voucher = new classVoucher();
$message = "";

// buat insert voucher
if(isset($_POST['insert'])){
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'] ?? '';
    $menu = $_POST['menu'] ?? '';
    $start = $_POST['start'];
    $end = $_POST['end'];
    $kuota = $_POST['kuota'];
    $diskon = $_POST['diskon'];

    if($start > $end) {
        $message = "End date can't occur before start date";
    }
    else if($menu === '' && $jenis === '') {
        $message = "Please select either a menu or jenis";
    }
    else {
        try {
            $result = $voucher->insertVoucher($menu, $jenis, $nama, $start, $end, $kuota, $diskon);
            $message = "Data ".$nama." inserted successfully with ID: ".$result;
        } catch(Exception $e) {
            $message = "Error: ".$e->getMessage();
        }
    }
}

// buat delete voucher
if(isset($_GET['kode'])){
    $kode = $_GET['kode'];
    $voucher->deleteVoucher($kode);
    header("Location: voucher.php");
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
    <div>
    <a href="index.php">admin page</a>
    <h1>Kelola Voucher</h1>
    <form action="voucher.php" method="post">
        <label for="nama">Nama Voucher: </label>
        <input type="text" name="nama">

        <br>
        <label for="jenis">Jenis Menu yang diskon: </label>
        <select name="jenis">
            <?php
            echo "<option value='none' selected disabled hidden>Select an Option</option>";
            echo "<option value='none'></option>";
            $res = $_JenisMenu->getJenisMenu();
            while($row = $res->fetch_assoc()) { echo "<option value=".$row["kode"].">".$row['nama']."</option>";}
            ?>
        </select>

        <br>
        <label for="menu">Menu yang diskon: </label>
        <select name="menu">
            <?php
            echo "<option value='none' selected disabled hidden>Select an Option</option>";
            echo "<option value='none'></option>";
            $res = $_Menu->getMenu();
            while($row = $res->fetch_assoc()) 
            {echo "<option value=".$row["kode"].">".$row['nama_m']."</option>";}
            ?>
        </select>

        <br>
        <label for="start">Tanggal mulai: </label>
        <input type="date" name="start">

        <br>
        <label for="end">Tanggal berakhir: </label>
        <input type="date" name="end">

        <br>
        <label for="kuota">Kuota maks: </label>
        <input type="number" name="kuota" min="0">

        <br>
        <label for="diskon">Persen Diskon: </label>
        <input type="number" name="diskon" min="0" max="100">

        <br>
        <input type="submit" value="insert" name="insert">
    </form>
    <p><?=$message?></p>

    <h2>Voucher yang tersedia:</h2>
    <?php
    $jmlh = $voucher->getTotalData();

    $limit = 5;
    (isset($_GET['page']))? $page = $_GET['page'] : $page=1;
    $offset = ($page-1)*$limit;
    $res = $voucher->getVoucher($offset,$limit);

    echo "<table>
        <tr>
            <th>Nama</th>
            <th>Jenis Menu Diskon</th>
            <th>Menu Diskon</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Berakhir</th>
            <th>Kuota Maks</th>
            <th>Kuota Sisa</th>
            <th>Diskon</th>
            <th>Hapus</th>
            <th>Ubah</th>
        </tr>";
    while($row = $res->fetch_assoc()) {
        echo 
        "<tr>
            <td>".$row["vnama"]."</td>";
        
        echo (isset($row['mjnama']))?"<td>".$row["mjnama"]."</td>":"<td>---</td>";
        echo (isset($row['mnama']))?"<td>".$row["mnama"]."</td>":"<td>---</td>";

        echo "<td>".$row['mulai_berlaku']."</td>
            <td>".$row['akhir_berlaku']."</td>
            <td>".$row['kuota_max']."</td>
            <td>".$row['kuota_sisa']."</td>
            <td>".$row['persen_diskon']."</td>
            <td> <a href='voucher.php?kode=" . $row['kode'] . "'>Hapus Data</a> </td>
            <td> <a href='ubahvoucher.php?kode=" . $row['kode'] . "'>Ubah Data</a> </td>
        </tr>";
    }
    echo "</table>";

    $max_page = ceil($jmlh/$limit);
    
    if($max_page > 1){
        echo "<div>";
        if($page!=1){
            echo "<a href="."voucher.php?page=1> first </a>
            <a href="."menu.php?page=".($page-1)."> prev </a>";
        }
        for($i=1;$i<=$max_page;$i++){
            echo ($i!=$page)?"<a href="."voucher.php?page=".$i."> ".$i." </a>":"<a> ".$i." </a>";
        }
        if($page!=$max_page){
            echo "<a href="."voucher.php?page=".($page+1)."> next </a>
            <a href="."voucher.php?page=".$max_page."> last </a>";
        }
        echo "</div>";
    }
    ?>
    </div>
</body>
</html>