<?php
session_start();
require_once("../class/classMenu.php");
require_once("../class/classJenisMenu.php");

$message = "";
$menu = new classMenu();
$jenisMenu = new classJenisMenu();

// Security check for admin access
if ($_SESSION["USER"] != "admin") {
    header("location: ../logout.php");
    exit();
}

$message = "";
if(isset($_GET['kode'])){
    $kode = $_GET['kode'];
    $res = $menu->getMenuKode($kode);
    $row = $res->fetch_assoc();
    $nama = $row['nama'];
    $jenis = $row['kode_jenis'];
    $harga = $row['harga_jual'];
    $gambar = $row['url_gambar'];
}

if(isset($_POST['update'])){
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $harga = $_POST['harga'];
    $gambar_old = $_POST['gambar_old'];
    $gambar_new = $_FILES['gambar_new'];

    $menu->updateMenu($kode, $nama, $jenis, $harga, $gambar_old, $gambar_new);
    header("Location: menu.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee StartBug - Ubah Menu</title>
    <link rel="stylesheet" href="../css/index.css">  
     <link rel="stylesheet" href="../css/menu.css">    </head>
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
        <pre><h1 style="font-size: 80px; margin: 0;">Update menu: 
<?=$nama?></h1></pre>
        <p style="font-size: 24px; margin-top: 10px;"><?=$message?></p>

        <div class="form-container">
            <h2>Ubah Detail Menu</h2>
            <form action="ubahmenu.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="kode" value="<?=$kode?>">
                <input type="hidden" name="gambar_old" value="<?=$gambar?>">

                <label for="nama">Nama Menu: </label>
                <input type="text" name="nama" id="nama" value="<?=$nama?>" required>
                
                <label for="jenis">Jenis Menu: </label>
                <select name="jenis" id="jenis">
                    <?php
                    // Fetch all menu types
                    $resJenis = $jenisMenu->getJenisMenu();
                    while($rowJenis = $resJenis->fetch_assoc()) {
                        // Check if current menu type matches the one from the database for 'selected' attribute
                        $selected = ($rowJenis["kode"] == $jenis) ? "selected" : "";
                        echo "<option value=\"".$rowJenis["kode"]."\" ".$selected.">".$rowJenis['nama']."</option>";
                    }
                    ?>
                </select>
                
                <label for="harga">Harga Jual: </label>
                <input type="number" name="harga" id="harga" value="<?=$harga?>" required min="0" step="any">
                
                <p style="margin-top: 20px; margin-bottom: 10px;">Gambar sekarang:</p>
                <?php if (!empty($gambar) && file_exists("../".$gambar)): ?>
                    <img src="../<?=$gambar?>" alt="Current Menu Image" style="width:150px; height:auto; display:block; margin-bottom: 20px; border-radius: 8px;">
                <?php else: ?>
                    <p>No image available.</p>
                <?php endif; ?>
                
                <label for="gambar_new">Gambar baru (kosongkan jika tidak ingin mengubah):</label>
                <input type="file" name="gambar_new" id="gambar_new" accept="image/jpeg, image/png">
                
                <input type="submit" value="Update" name="update">
            </form>
        </div>
    </div>
</body>
</html>