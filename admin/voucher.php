<?php
require_once("../class/classMenu.php");
require_once("../class/classJenisMenu.php");
require_once("../class/classVoucher.php");
session_start();

// Security check for admin access
if ($_SESSION["USER"] != "admin") {
    header("location: ../logout.php");
    exit();
}

$_JenisMenu = new classJenisMenu();
$_Menu = new classMenu();
$voucher = new classVoucher();
$message = "";

// buat insert voucher
if(isset($_POST['insert'])){
    $nama = trim($_POST['nama']); // Trim whitespace
    $jenis = $_POST['jenis'] ?? '';
    $menu = $_POST['menu'] ?? '';
    $start = $_POST['start'];
    $end = $_POST['end'];
    $kuota = $_POST['kuota'];
    $diskon = $_POST['diskon'];

    // Basic validation
    if (empty($nama) || empty($start) || empty($end) || !is_numeric($kuota) || !is_numeric($diskon)) {
        $message = "Tolong isi data dengan format yang sesuai";
    } else if ($start > $end) {
        $message = "End date can't occur before start date.";
    } else if ($menu === '' && $jenis === '') {
        $message = "Tolong pilih jenis menu atau menu yang didiskon";
    } else {
        try {
            // Adjust 'none' string to actual NULL for database if your insertVoucher expects NULL
            $menu_id = ($menu === 'none' || $menu === '') ? NULL : $menu;
            $jenis_id = ($jenis === 'none' || $jenis === '') ? NULL : $jenis;

            $result = $voucher->insertVoucher($menu_id, $jenis_id, $nama, $start, $end, $kuota, $diskon);
            $message = "Data " . htmlspecialchars($nama) . " berhasil dimasukan";
        } catch(Exception $e) {
            $message = "Error: " . htmlspecialchars($e->getMessage()) . ".";
        }
    }
}

// buat delete voucher
if(isset($_GET['kode'])){
    $kode = $_GET['kode'];
    $voucher->deleteVoucher($kode);
    header("Location: voucher.php");
    exit(); // Always exit after a header redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee StartBug - Kelola Voucher</title>
    <link rel="stylesheet" href="../css/index.css">  
    <link rel="stylesheet" href="../css/voucher.css"> 
</head>
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
    
    <div class="content-wrapper"> <h1 style="font-size: 80px; margin: 0;">Kelola Voucher</h1>
        <p style="font-size: 24px; margin-top: 10px;"><?=$message?></p>

        <div class="form-container">
            <h2>Buat Voucher Baru</h2>
            <form action="voucher.php" method="post">
                <label for="nama">Nama Voucher: </label>
                <input type="text" name="nama" id="nama" required>

                <label for="jenis">Jenis Menu yang diskon: </label>
                <select name="jenis" id="jenis">
                    <option value='none' selected disabled hidden>Pilih jenis menu yang diskon (Opsional)</option>
                    <option value='none'></option> <?php
                    $resJenis = $_JenisMenu->getJenisMenu(0, 100); // Fetch all for dropdown
                    while($row = $resJenis->fetch_assoc()) {
                        echo "<option value='". htmlspecialchars($row["kode"]) ."'>". htmlspecialchars($row['nama']) ."</option>";
                    }
                    ?>
                </select>

                <label for="menu">Menu yang diskon: </label>
                <select name="menu" id="menu">
                    <option value='none' selected disabled hidden>Pilih menu yang diskon (Opsional)</option>
                    <option value='none'></option> <?php
                    $resMenu = $_Menu->getMenu(0, 100); // Fetch all for dropdown
                    while($row = $resMenu->fetch_assoc()) {
                        echo "<option value='". htmlspecialchars($row["kode"]) ."'>". htmlspecialchars($row['nama_m']) ."</option>";
                    }
                    ?>
                </select>

                <label for="start">Tanggal Mulai: </label>
                <input type="date" name="start" id="start" required>

                <label for="end">Tanggal Berakhir: </label>
                <input type="date" name="end" id="end" required>

                <label for="kuota">Kuota Maksimal: </label>
                <input type="number" name="kuota" id="kuota" min="1" required> <label for="diskon">Persen Diskon: </label>
                <input type="number" name="diskon" id="diskon" min="0" max="100" required>

                <input type="submit" value="Insert" name="insert">
            </form>
        </div>

        <h2>Voucher yang Tersedia:</h2>

        <?php
        $jmlh = $voucher->getTotalData();

        $limit = 5;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $res = $voucher->getVoucher($offset, $limit);

        if ($res && $res->num_rows > 0): ?>
            <div class="grid-template">
                <?php while($row = $res->fetch_assoc()):
                    $sekarang = new DateTime();
                    $mulai_berlaku = new DateTime($row["mulai_berlaku"]);
                    $akhir_berlaku = new DateTime($row["akhir_berlaku"]);
                    $voucher_aktif = ($sekarang >= $mulai_berlaku && $sekarang <= $akhir_berlaku);
                ?>
                <div class="card">
                    <div class="card-content">
                        <h1 style="margin-bottom: 10px;"><?= htmlspecialchars($row["vnama"]) ?></h1>
                        <h3>Jenis Menu: <?= isset($row["mjnama"]) ? htmlspecialchars($row["mjnama"]) : "-" ?></h3>
                        <h3>Menu Diskon: <?= isset($row["mnama"]) ? htmlspecialchars($row["mnama"]) : "-" ?></h3>
                        <h3>Diskon: <?= htmlspecialchars($row["persen_diskon"]) ?>%</h3>
                        <h3>Mulai: <?= htmlspecialchars($row["mulai_berlaku"]) ?></h3>
                        <h3>Berakhir: <?= htmlspecialchars($row["akhir_berlaku"]) ?></h3>
                        <h3>Kuota Maks: <?= htmlspecialchars($row["kuota_max"]) ?></h3>
                        <h3>Kuota Sisa: <?= htmlspecialchars($row["kuota_sisa"]) ?></h3>
                    </div>
                    <div class="card-footer">
                    <div class="voucher-status">
                        <?php if ($voucher_aktif): ?>
                            <h4>Voucher Aktif</h4>
                        <?php else: ?>
                            <h4 class="inactive">Voucher Tidak Aktif</h4>
                        <?php endif; ?>
                    </div>
                    <div class="voucher-actions">
                        <a href="voucher.php?kode=<?= htmlspecialchars($row['kode']) ?>" class="delete-link" onclick="return confirm('Yakin ingin menghapus voucher ini?');">Hapus</a>
                        <a href="ubahvoucher.php?kode=<?= htmlspecialchars($row['kode']) ?>">Ubah</a>
                        <a href="membervoucher.php?kode=<?= htmlspecialchars($row['kode']) ?>">Member klaim</a>
                    </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Tidak ada voucher tersedia.</p>
        <?php endif; ?>

        <!-- Pagination -->
        <?php
        $max_page = ceil($jmlh / $limit);
        if ($max_page > 1): ?>
            <div class="pagination" style="text-align:center; margin-top:20px;">
                <?php if ($page != 1): ?>
                    <a href="voucher.php?page=1">First</a>
                    <a href="voucher.php?page=<?= $page - 1 ?>">Prev</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $max_page; $i++): ?>
                    <?php if ($i != $page): ?>
                        <a href="voucher.php?page=<?= $i ?>"><?= $i ?></a>
                    <?php else: ?>
                        <span><strong><?= $i ?></strong></span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page != $max_page): ?>
                    <a href="voucher.php?page=<?= $page + 1 ?>">Next</a>
                    <a href="voucher.php?page=<?= $max_page ?>">Last</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>