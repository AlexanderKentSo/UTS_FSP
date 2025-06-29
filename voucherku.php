<?php
session_start();
require_once("class/classVoucher.php");
$voucher = new classVoucher();

$message = "";
if (!isset($_SESSION["USER"])) {
    $message = "Silahkan login terlebih dahulu";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee StartBug</title>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <header id="header">
    <!-- Hamburger Button -->
    <div id="hamburger" onclick="toggleMenu()">
        ☰
    </div>

    <div  class="nav-links">
        <a href="index.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="promo.php">Promo</a>
        <a href="voucherku.php">Voucherku</a>
    </div>
    <?php
    echo (isset($_SESSION['USER']))?
    "<a href='logout.php'>Log out</a>":
    "<a href='login.php'>Log in</a>"
    ?>
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

    <div class="content-wrapper">
        <h1>Voucherku</h1>

        <div class="grid-template">
            <?php
            if (isset($_SESSION['USER'])) {
                $res = $voucher->getVoucherUser($_SESSION['USER']);
                while ($row = $res->fetch_assoc()) {
                    $sekarang = new DateTime();
                    $mulai_berlaku = new DateTime($row["mulai_berlaku"]);
                    $akhir_berlaku = new DateTime($row["akhir_berlaku"]);
                    $voucher_aktif = ($sekarang >= $mulai_berlaku && $sekarang <= $akhir_berlaku);

                    echo "
                    <div class='card'>
                    <div class='card-content'>
                        <h1 style='margin:30px 10px;'>" . $row["vnama"] . "</h1>
                        <h3 style='margin:10px;'>menu: " . (isset($row["mnama"]) ? $row["mnama"] : "-") . "</h3>
                        <h3 style='margin:10px;'>jenis menu: " . (isset($row["mjnama"]) ? $row["mjnama"] : "-") . "</h3>
                        <h3 style='margin:10px;''>diskon: " . $row["persen_diskon"] . "%</h3>
                        <h3 style='margin:10px;'>mulai berlaku: " . $row["mulai_berlaku"] . "</h3>
                        <h3 style='margin:10px;'>akhir berlaku: " . $row["akhir_berlaku"] . "</h3>
                    </div>";

                    if ($voucher_aktif) {
                        echo "
                    <div class='card-footer'>
                        <h3 style='margin:10px; color:#8D6E63;'>kode unik: " . $row["kode_unik"] . "</h3>
                    </div>
                    </div>";
                    } else {
                        echo "
                    <div class='card-footer'>
                        <h3 style='margin:10px;text-decoration:line-through;color:red;'>kode unik: " . $row["kode_unik"] . "</h3>
                    </div>
                    </div>";
                    }
                }
            } else {
                echo "<h1 style='text-align:center; font-size:80px;'>" . $message . "</h1>";
            }
            ?>
        </div>
    </div>
</body>

</html>