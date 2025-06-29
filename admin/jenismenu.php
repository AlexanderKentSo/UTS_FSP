<?php
require_once("../class/classJenisMenu.php");
session_start();
$jenisMenu = new classJenisMenu();

$message = "";

// Redirect if not admin
if ($_SESSION["USER"] != "admin") {
    header("location: ../logout.php");
    exit();
}

if (isset($_POST['insert'])) {
    $nama = $_POST['nama'];
    if (!is_null($jenisMenu->insertJenisMenu($nama))) {
        $message = "Data " . htmlspecialchars($nama) . " berhasil dimasukan"; // Sanitize output
    } else {
        $message = "Failed to insert data."; // Add a failure message
    }
}

if (isset($_GET['kode'])) {
    $kode = $_GET['kode'];
    $jenisMenu->deleteJenisMenu($kode);
    header("Location: jenismenu.php");
    exit(); // Always call exit after a header redirect
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee StartBug - Kelola Jenis Menu</title>
    <link rel="stylesheet" href="../css/index.css">
    <style>

    </style>
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

    <div style="
        position: relative;
        z-index: 1;
        padding: 60px 20px 20px;
        color: antiquewhite;">
        <h1 style="font-size: 80px; margin: 0;">Kelola Jenis Menu</h1>
        <p style="font-size: 24px; margin-top: 10px;"><?= $message ?></p>

        <div class="form-container">
            <h2>Tambah Jenis Menu</h2>
            <form action="jenismenu.php" method="post">
                <label for="nama">Masukan Jenis Menu:</label>
                <input type="text" name="nama" id="nama" required>
                <input type="submit" value="Insert" name="insert">
            </form>
        </div>

        <h2>Jenis Menu yang Tersedia:</h2>
        <?php
        $jmlh = $jenisMenu->getTotalData();

        $limit = 5;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $res = $jenisMenu->getJenisMenu($offset, $limit);

        if ($res->num_rows > 0) {
            echo "<table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Hapus</th>
                        <th>Ubah</th>
                    </tr>
                </thead>
                <tbody>";
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['nama']) . "</td>
                        <td> <a href='jenismenu.php?kode=" . htmlspecialchars($row['kode']) . "' class='delete-link' onclick='return confirm(\"Yakin ingin menghapus data ".$row['nama']."?\");'>Hapus Data</a> </td>
                        <td> <a href='ubahjenismenu.php?kode=" . htmlspecialchars($row['kode']) . "'>Ubah Data</a> </td>
                    </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No menu types available.</p>";
        }

        $max_page = ceil($jmlh / $limit);

        if ($max_page > 1) {
            echo "<div class='pagination'>";
            if ($page != 1) {
                echo "<a href='jenismenu.php?page=1'>First</a>";
                echo "<a href='jenismenu.php?page=" . ($page - 1) . "'>Prev</a>";
            }
            for ($i = 1; $i <= $max_page; $i++) {
                echo ($i != $page) ? "<a href='jenismenu.php?page=" . $i . "'>" . $i . "</a>" : "<span>" . $i . "</span>";
            }
            if ($page != $max_page) {
                echo "<a href='jenismenu.php?page=" . ($page + 1) . "'>Next</a>";
                echo "<a href='jenismenu.php?page=" . $max_page . "'>Last</a>";
            }
            echo "</div>";
        }
        ?>
    </div>
</body>

</html>