<?php
session_start();

$mysqli = new mysqli("localhost","root","","fullstack");
if($mysqli->connect_errno){ die("Failed to connect t MySQL: ".$mysqli->connect_error);}
$message = "";
$kode = $_GET['kode'];
$stmt = $mysqli->prepare("SELECT * FROM `menu` WHERE (`kode` = ?);");
$stmt->bind_param('i',$kode);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$nama = $row['nama'];
$jenis = $row['kode_jenis'];
$harga = $row['harga_jual'];
$gambar = $row['url_gambar'];
$stmt->close();

if(isset($_POST['update'])){
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $harga = $_POST['harga'];
    $gambar_old = $_POST['gambar_old'];
    $stmt = $mysqli->prepare(
        "UPDATE `menu` SET 
        `kode_jenis` = ?, 
        `nama` = ?, 
        `harga_jual` = ? 
        WHERE (`kode` = ?);");
    $stmt->bind_param('isdi',$jenis,$nama, $harga, $kode);
    $stmt->execute();
    $stmt->close();

    if(isset($_FILES['gambar_new']) && $_FILES['gambar_new']['size']>0) {
        $old_image_path = "../" . $gambar_old;
        if (file_exists($old_image_path)) {
            unlink($old_image_path);
        }
        
        $new_filename = time() . '_' . $_FILES['gambar_new']['name'];
        $new_path = "images/" . $new_filename;
        
        if(move_uploaded_file($_FILES['gambar_new']['tmp_name'], "../" . $new_path)) {
            $stmt = $mysqli->prepare("UPDATE `menu` SET `url_gambar` = ? WHERE (`kode` = ?);");
            $stmt->bind_param('si', $new_path, $kode);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: menu.php");
    exit;
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
    <a href="admin.php">admin page</a>
    <h1>Update menu: <?=$nama?></h1>
    <form action="ubahmenu.php"  method="post" enctype="multipart/form-data">
        <input type="hidden" name="kode" value="<?=$kode?>">
        <input type="hidden" name="gambar_old" value="<?=$gambar?>">

        <label for="nama">Nama Menu: </label>
        <input type="text" name="nama" value="<?=$nama?>" required>
        <br>
        <label for="nama">Jenis Menu: </label>
        <select name="jenis">
            <?php
            $stmt = $mysqli->prepare("SELECT * FROM menu_jenis");
            $stmt->execute();
            $res = $stmt->get_result();
            while($row = $res->fetch_assoc()) 
            { echo ($row["kode"]==$jenis)?
            "<option value=".$row["kode"]." selected>".$row['nama']."</option>":
            "<option value=".$row["kode"].">".$row['nama']."</option>";}
            $stmt->close();
            ?>
        </select>
        <br>
        <label for="harga">Harga Jual: </label>
        <input type="number" name="harga" value="<?=$harga?>" required>
        <br>
        <br>
        <p>Gambar sekarang:</p>
        <img src="../<?=$gambar?>" alt="" style="width:100px;">
        <br>
        <br>
        <label for="gambar">Gambar baru</label>
        <input type="file" name="gambar_new" accept="image/jpeg, image/png">
        <br>
        <br>
        <input type="submit" value="update" name="update">
    </form>
    <p><?=$message?></p>
    </div>
</body>
</html>

<?php $mysqli->close();?>