<?php
session_start();
require_once("../class/classMenu.php");
require_once("../class/classJenisMenu.php");

$message = "";
$menu = new classMenu();
$jenisMenu = new classJenisMenu();

// Redirect if not admin (Crucial security check)
if ($_SESSION["USER"] != "admin") {
    header("location: ../logout.php");
    exit();
}

// buat masukin menu
if(isset($_POST['insert'])){
    $jenis = $_POST['jenis'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $gambar = $_FILES['gambar'];

    // Basic validation for required fields
    if (empty($jenis) || $jenis == 'none' || empty($nama) || empty($harga) || $gambar['error'] != UPLOAD_ERR_OK) {
        $message = "Please fill all required fields and upload an image.";
    } else {
        if (!is_null($menu->insertMenu($jenis, $nama, $harga, $gambar))) {
            $message = "Data " . htmlspecialchars($nama) . " inserted successfully!";
        } else {
            $message = "Failed to insert data. Please check file permissions or class logic.";
        }
    }
}

// buat hapus menu
if(isset($_GET['kode'])){
    $kode = $_GET['kode'];
    $menu->deleteMenu($kode);
    header("Location: menu.php"); // Redirect after delete
    exit(); // Always exit after header redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee StartBug - Kelola Menu</title>
    <link rel="stylesheet" href="../css/index.css"> 
    <link rel="stylesheet" href="../css/menu.css">   </head>
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
    
    <div class="content-wrapper"> <h1 style="font-size: 80px; margin: 0;">Kelola Menu</h1>
        <p style="font-size: 24px; margin-top: 10px;"><?=$message?></p>

        <div class="form-container">
            <h2>Tambah Menu Baru</h2>
            <form action="menu.php" method="post" enctype="multipart/form-data">
                <label for="nama">Nama Menu: </label>
                <input type="text" name="nama" id="nama" required>
                
                <label for="jenis">Jenis Menu: </label>
                <select name="jenis" id="jenis" required>
                    <option value='none' selected disabled hidden>Select an Option</option>
                    <?php
                    $resJenis = $jenisMenu->getJenisMenu(0, 100);
                    while($rowJenis = $resJenis->fetch_assoc()) {
                        echo "<option value='". htmlspecialchars($rowJenis["kode"]) ."'>". htmlspecialchars($rowJenis['nama']) ."</option>";
                    }
                    ?>
                </select>
                
                <label for="harga">Harga Jual: </label>
                <input type="number" name="harga" id="harga" required min="0" step="any">
                
                <label for="gambar">Gambar:</label>
                <input type="file" name="gambar" id="gambar" accept="image/jpeg, image/png" required>
                
                <input type="submit" value="Insert" name="insert">
            </form>
        </div>

        <h2>Menu yang Tersedia:</h2>
        <?php
        $jmlh = $menu->getTotalData();

        $limit = 5;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page-1)*$limit;
        $res = $menu->getMenu($offset, $limit);

        if ($res && $res->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Harga</th>
                        <th>Gambar</th>
                        <th>Hapus</th>
                        <th>Ubah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["nama_m"]) ?></td>
                        <td><?= htmlspecialchars($row["nama_mj"]) ?></td>
                        <td><?= htmlspecialchars($row["harga_jual"]) ?></td>
                        <td>
                            <?php if (!empty($row['url_gambar']) && file_exists("../".$row['url_gambar'])): ?>
                                <img src="../<?= htmlspecialchars($row["url_gambar"]) ?>" alt="<?= htmlspecialchars($row["nama_m"]) ?>">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href='menu.php?kode=<?= htmlspecialchars($row['kode']) ?>' class="delete-link" onclick="return confirm('Are you sure you want to delete this menu item?');">Hapus Data</a>
                        </td>
                        <td>
                            <a href='ubahmenu.php?kode=<?= htmlspecialchars($row['kode']) ?>'>Ubah Data</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No menu items available.</p>
        <?php endif; ?>
        
        <?php
        $max_page = ceil($jmlh/$limit);
        if($max_page > 1): ?>
            <div class="pagination">
                <?php if($page != 1): ?>
                    <a href="menu.php?page=1">First</a>
                    <a href="menu.php?page=<?= $page-1 ?>">Prev</a>
                <?php endif; ?>
                
                <?php for($i=1; $i<=$max_page; $i++): ?>
                    <?php if($i != $page): ?>
                        <a href="menu.php?page=<?= $i ?>"><?= $i ?></a>
                    <?php else: ?>
                        <span><?= $i ?></span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if($page != $max_page): ?>
                    <a href="menu.php?page=<?= $page+1 ?>">Next</a>
                    <a href="menu.php?page=<?= $max_page ?>">Last</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>