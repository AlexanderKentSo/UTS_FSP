<?php
require_once("../class/classVoucher.php");
session_start();

$voucher = new classVoucher();
$message = "";

// Security check for admin access
if ($_SESSION["USER"] != "admin") {
    header("location: ../logout.php");
    exit();
}

// Get the voucher code from the URL
$voucher_code = $_GET['kode'] ?? null;

// Optional: You might want to fetch voucher name here for the title if desired
// $voucher_name = "";
// if ($voucher_code) {
//     $voucher_detail = $voucher->getVoucherByCode($voucher_code); // Assuming such a method exists
//     if ($voucher_detail && $voucher_detail->num_rows > 0) {
//         $row_detail = $voucher_detail->fetch_assoc();
//         $voucher_name = $row_detail['vnama']; // Adjust column name if different
//     }
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee StartBug - Member Klaim Voucher</title>
    <link rel="stylesheet" href="../css/index.css">   <link rel="stylesheet" href="../css/voucher.css"> </head>
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
        <h1 style="font-size: 80px; margin: 0;">Member Klaim Voucher: <?= htmlspecialchars($voucher_code) ?></h1>
        <p style="font-size: 24px; margin-top: 10px;"><?=$message?></p>

        <h2>Detail Klaim Voucher:</h2>
        <?php
        $res = $voucher->getVoucherClaimer($_GET['kode']);

        // Check if there are any results before echoing table structure
        if ($res && $res->num_rows > 0) {
            echo "<table>
                <thead>
                    <tr>
                        <th>Nama Member</th>
                        <th>Kode Unik</th>
                    </tr>
                </thead>
                <tbody>";
            while($row = $res->fetch_assoc()) {
                echo 
                "<tr>
                    <td>".$row["iduser"]."</td>
                    <td>".$row["kode_unik"]."</td>
                </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>Tidak ada member yang mengklaim voucher ini.</p>";
        }
        ?>
    </div>
</body>
</html>