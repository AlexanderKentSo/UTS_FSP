<?php
session_start();
require_once("class/classMenu.php");
$menu = new classMenu();

require_once("class/classJenisMenu.php");
$jenisMenu = new classJenisMenu(); // Changed variable name for clarity
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee StartBug</title>
    <script type="text/javascript" src="js/jquery-3.7.1.js"></script>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>

    <!-- header -->
    <header id="header">
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="promo.php">Promo</a>
            <a href="voucherku.php">Voucherku</a>
        </div>
        <a href="logout.php">Log out</a>
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
        <h1>Menu</h1>

        <div style="margin:30px 0;">
            <label for="cari_menu">Cari menu:</label>
            <input type="text" id="cari_menu" name="keyword"
                value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
            <br>
            <label for="jenis">Jenis Menu: </label>
            <select id="jenis">
                <option value='' selected>All Categories</option>
                <?php
                $res = $jenisMenu->getJenisMenu();
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row["nama"]) . "'>" . htmlspecialchars($row['nama']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="grid-wrapper">
            <div class="grid-template" style="width:80%">
            <?php
            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
            $res = $menu->getSearchMenu($keyword, "");
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    echo "
                <div class='card'>
                    <img src='" . htmlspecialchars($row["url_gambar"]) . "'>
                    <div class='card-content'>
                        <h1 style='margin:0px;'>" . htmlspecialchars($row["nama_m"]) . "</h1>
                        <h3 style='margin:0px;'>" . htmlspecialchars($row["nama_mj"]) . "</h3>
                        <h3 style='margin:0px;'>price: " . htmlspecialchars($row["harga_jual"]) . "</h3>
                    </div>
                </div>";
                }
            } else {
                echo "<p>No menu items found</p>";
            }
            ?>
            </div>
        </div>
    </div>

    <script>
        function search() {
            var keyword = $('#cari_menu').val();
            var jenis = $('#jenis').val();

            // Clear previous results and show loading
            $('.grid-template').empty().html('<p>Searching...</p>');

            $.post('backend/be_menu.php', { keyword: keyword, jenis: jenis }).done(function (json) {
                var data = JSON.parse(json);
                $('.grid-template').empty();
                for (var i = 0; i < data.length; i++) {
                    $('.grid-template').append(data[i]);
                }
            })
        }

        $('#cari_menu').on('input', function () {
            search();
        });
        $('#jenis').on('input', function () {
            search();
        });
    </script>
</body>

</html>