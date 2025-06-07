<?php
require_once("../class/classVoucher.php");
session_start();

$voucher = new classVoucher();
$message = "";
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
    <a href="index.php">admin page</a>
    <h1>Kelola Voucher</h1>
    
    <p><?=$message?></p>

    <h2>Member yang sudah klaim voucher:</h2>
    <?php
    $res = $voucher->getVoucherClaimer($_GET['kode']);

    echo "<table>
        <tr>
            <th>Nama Member</th>
            <th>kode unik</th>
        </tr>";
    while($row = $res->fetch_assoc()) {
        echo 
        "<tr>
            <td>".$row["iduser"]."</td>
            <td>".$row["kode_unik"]."</td>
        </tr>";
    }
    echo "</table>";
    ?>
    </div>
</body>
</html>