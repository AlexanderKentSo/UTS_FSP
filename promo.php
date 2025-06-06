<?php
session_start();
require_once("class/classVoucher.php");
$voucher = new classVoucher();

$message = "";
if(isset($_GET['kode'])){
    if(isset($_SESSION['USER'])){
        $voucher->claimVoucher($_SESSION['USER'],$_GET['kode']);
        header("location: voucherku.php");
    } else { $message = "HARAP LOGIN SEEBLUM CLAIM VOUCHER"; }
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
    <!-- header -->
    <header id="header">
        <div style="display: flex; gap: 20px;">
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="promo.php">Promo</a>
            <a href="voucherku.php">Voucherku</a>
        </div>
        <a href="logout.php" style="position: absolute; right: 30px;">Log out</a>
    </header>

    <div id="content">
        <h1>Promo</h1>

        <?="<h1>".$message."</h1>"?>

        <div class="grid-template">
            <?php
            $res = $voucher->getVoucher();
            while($row = $res->fetch_assoc()){
                $sekarang = new DateTime();
                $mulai_berlaku = new DateTime($row["mulai_berlaku"]);
                $akhir_berlaku = new DateTime($row["akhir_berlaku"]);
                
                $voucher_aktif = ($sekarang >= $mulai_berlaku && $sekarang <= $akhir_berlaku);

                echo "
                <div class='card'>
                <div class='card-content'>
                    <h1 style='margin:30px 10px;'>".$row["vnama"]."</h1>
                    <h3 style='margin:10px;'>menu: ".(isset($row["mnama"]) ? $row["mnama"] : "-")."</h3>
                    <h3 style='margin:10px;'>jenis menu: ".(isset($row["mjnama"]) ? $row["mjnama"] : "-")."</h3>
                    <h3 style='margin:10px;'>diskon: ".$row["persen_diskon"]."%</h3>
                    <h3 style='margin:10px;'>mulai berlaku: ".$row["mulai_berlaku"]."</h3>
                    <h3 style='margin:10px;'>akhir berlaku: ".$row["akhir_berlaku"]."</h3>
                </div>";

                if ($voucher_aktif) {
                    echo "
                    <div class='card-footer'>
                    <a href='promo.php?kode=".$row['kode']."'>
                        <button class='btn-claim'>Claim Voucher</button>
                    </a>
                    </div>";
                } else {
                    echo "
                    <div class='card-footer'>
                        <button class='btn-claim' disabled>Voucher Tidak Berlaku</button>
                    </div>";
                }
                echo "</div>";
            }
            ?>
        </div>
    </div>
</body>
</html>