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
        $message = "Tolong isi semua data dan upload gambar yang vaild";
    } else {
        if (!is_null($menu->insertMenu($jenis, $nama, $harga, $gambar))) {
            $message = "Data " . htmlspecialchars($nama) . " berhasil dimasukan!";
        } else {
            $message = "Insert data gagal, periksa kembali format input dan file nya";
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
    <!-- Tombol Hamburger -->
    <div id="hamburger" onclick="toggleMenu()">☰</div>

    <!-- Navigasi Admin -->
    <nav class="nav-links">
        <a href="index.php">Admin Home</a>
        <a href="voucher.php">Kelola Voucher</a>
        <a href="menu.php">Kelola Menu</a>
        <a href="jenismenu.php">Kelola Jenis Menu</a>
        <a href="member.php">Kelola Member</a>
    </nav>
    
    <a href="../logout.php">Log out</a>
    </header>
    <script>
    function toggleMenu() {
        const nav = document.querySelector('.nav-links');
        nav.classList.toggle('show');
    }
    </script>
    
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
        $offset = ($page - 1) * $limit;
        $res = $menu->getMenu($offset, $limit);
        ?>

        <?php if ($res && $res->num_rows > 0): ?>
        <div class="grid-wrapper">
            <div class="grid-template">
                <?php while($row = $res->fetch_assoc()): ?>
                <div class="card">
                    <?php if (!empty($row['url_gambar']) && file_exists("../" . $row['url_gambar'])): ?>
                        <img src="../<?= htmlspecialchars($row["url_gambar"]) ?>" alt="<?= htmlspecialchars($row["nama_m"]) ?>">
                    <?php else: ?>
                        <img src="placeholder.png" alt="No Image">
                    <?php endif; ?>
                    <div class="card-content">
                        <h1><?= htmlspecialchars($row["nama_m"]) ?></h1>
                        <h3>Jenis: <?= htmlspecialchars($row["nama_mj"]) ?></h3>
                        <h3>Harga: Rp<?= htmlspecialchars(number_format($row["harga_jual"], 0, ',', '.')) ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="voucher-action">
                        <a href='menu.php?kode=<?= htmlspecialchars($row['kode']) ?>' class="delete-link" onclick="return confirm('Yakin ingin menghapus menu <?= htmlspecialchars(addslashes($row['nama_m'])) ?>?');">Hapus</a> |
                        <a href='ubahmenu.php?kode=<?= htmlspecialchars($row['kode']) ?>'>Ubah</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php else: ?>
            <p>No menu items available.</p>
        <?php endif; ?>

        <!-- Pagination -->
        <?php
        $max_page = ceil($jmlh / $limit);
        if ($max_page > 1): ?>
            <div class="pagination" style="text-align:center; margin-top:20px;">
                <?php if ($page != 1): ?>
                    <a href="menu.php?page=1">First</a>
                    <a href="menu.php?page=<?= $page - 1 ?>">Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $max_page; $i++): ?>
                    <?php if ($i != $page): ?>
                        <a href="menu.php?page=<?= $i ?>"><?= $i ?></a>
                    <?php else: ?>
                        <span><strong><?= $i ?></strong></span>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page != $max_page): ?>
                    <a href="menu.php?page=<?= $page + 1 ?>">Next</a>
                    <a href="menu.php?page=<?= $max_page ?>">Last</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
</body>
</html>