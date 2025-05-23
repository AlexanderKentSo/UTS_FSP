<?php
require_once("../class/classMenu.php");
$menu = new classMenu();

require_once("../class/classJenisMenu.php");
$jenis = new classJenisMenu();

$keyword = isset($_POST['keyword'])? $_POST['keyword']: "";
$jenis = isset($_POST['jenis'])? $_POST['jenis']: "";
$res = $menu->getSearchMenu($keyword, $jenis);
$data = array();
while($row = $res->fetch_assoc()){
$data[] = sprintf('
        <div class="card">
            <img src="%s">
            <div class="card-content">
                <h1 style="margin:0px;">%s</h1>
                <h3 style="margin:0px;">%s</h3>
                <h3 style="margin:0px;">price: %s</h3>
            </div>
        </div>',
        htmlspecialchars($row["url_gambar"] ?? ''),
        htmlspecialchars($row["nama_m"] ?? ''),
        htmlspecialchars($row["nama_mj"] ?? ''),
        htmlspecialchars($row["harga_jual"] ?? '')
    );
}
echo json_encode($data);
?>