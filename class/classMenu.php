<?php
require_once("classDB.php");
class classMenu extends classDB{
    public function __construct() {
        parent::__construct();
    }

    public function getMenu($offset=null, $limit=null) {
        $sql = "SELECT m.kode, m.nama as nama_m,mj.nama 
                AS nama_mj,m.harga_jual,m.url_gambar 
                FROM menu_jenis AS mj 
                INNER JOIN menu AS m 
                ON m.kode_jenis = mj.kode";
		if(!is_null($offset) && !is_null($limit)) $sql .= " LIMIT ?,?";

		$stmt = $this->mysqli->prepare($sql);
		if(!is_null($offset) && !is_null($limit)) 
			$stmt->bind_param('ii', $offset, $limit);

		$stmt->execute();
		$res = $stmt->get_result();
        $stmt->close();
		return $res;
    }
    public function getTotalData() {
		$res = $this->getMenu();
		return $res->num_rows;
	}
    public function getMenuKode($kode) {
        $kode = $_GET['kode'];
        $stmt = $this->mysqli->prepare("SELECT * FROM `menu` WHERE (`kode` = ?);");
        $stmt->bind_param('i',$kode);
        $stmt->execute();
        $res = $stmt->get_result();
		return $res;
    }
    public function getRandomMenu() {
        $sql = "SELECT m.kode, m.nama as nama_m,mj.nama 
                AS nama_mj,m.harga_jual,m.url_gambar 
                FROM menu_jenis AS mj 
                INNER JOIN menu AS m 
                ON m.kode_jenis = mj.kode
                ORDER BY RAND() LIMIT 10";
		$stmt = $this->mysqli->prepare($sql);
		$stmt->execute();
		$res = $stmt->get_result();
        $stmt->close();
		return $res;
    }
    public function getSearchMenu($keyword, $jenis) {
        $sql = "SELECT m.kode, m.nama as nama_m,mj.nama 
                AS nama_mj,m.harga_jual,m.url_gambar 
                FROM menu_jenis AS mj 
                INNER JOIN menu AS m 
                ON m.kode_jenis = mj.kode
                WHERE m.nama LIKE ?";
        if($jenis!=""){$sql .= " AND mj.nama = ?";}
		$stmt = $this->mysqli->prepare($sql);
        $keyword = "%" . $keyword . "%";
        ($jenis!="")? $stmt->bind_param("ss", $keyword, $jenis) : $stmt->bind_param("s", $keyword);
		$stmt->execute();
		$res = $stmt->get_result();
        $stmt->close();
		return $res;
    }
    

    public function insertMenu($jenis, $nama, $harga, $gambar){
        $stmt = $this->mysqli->prepare("INSERT INTO `menu` (`kode_jenis`,`nama`,`harga_jual`) VALUES (?,?,?);");
        $stmt->bind_param('isd',$jenis,$nama, $harga);
        if ($stmt->execute()) {
            $last_id = $stmt->insert_id;
            $url = "images/".time() . '_' . $_FILES['gambar']['name'];
    
            $stmt2 = $this->mysqli->prepare("UPDATE `menu` SET `url_gambar` = ? WHERE `kode` = ?;");
            $stmt2->bind_param('si',$url,$last_id);
            $stmt2->execute();
            $stmt2->close();
    
            move_uploaded_file($gambar['tmp_name'],"../".$url);
            echo "Data $nama inserted successfully!";
        } else { echo "Error: " . $stmt->error; }

        $last_id = $stmt->insert_id;
        $stmt->close();
        return $last_id;
    }

    public function deleteMenu($kode){
        $stmt = $this->mysqli->prepare("SELECT `url_gambar` FROM `menu` WHERE (`kode` = ?);");
        $stmt->bind_param('i',$kode);
        $stmt->execute();
        $gambar = $stmt->get_result();
        $gambar = $gambar->fetch_assoc();
        $stmt->close();
        if(file_exists("../".$gambar['url_gambar'])){unlink("../".$gambar['url_gambar']);}
        $stmt = $this->mysqli->prepare("DELETE FROM `menu` WHERE (`kode` = ?);");
        $stmt->bind_param('i',$kode);
        $stmt->execute();
        $stmt->close();
    }

    public function updateMenu($kode, $nama, $jenis, $harga, $gambar_old, $gambar_new=null){
        if(!is_null($gambar_new) && $gambar_new['size'] > 0) {
            $old_image_path = "../" . $gambar_old;
            if (file_exists($old_image_path)) {unlink($old_image_path);}
            
            $new_filename = time() . '_' . $gambar_new['name'];
            $new_path = "images/" . $new_filename;
            
            if(move_uploaded_file($gambar_new['tmp_name'], "../" . $new_path)) {
                $stmt = $this->mysqli->prepare(
                "UPDATE `menu` SET 
                        `kode_jenis` = ?, 
                        `nama` = ?, 
                        `harga_jual` = ?, 
                        `url_gambar` = ? 
                        WHERE (`kode` = ?);");
                $stmt->bind_param('isisi', $jenis, $nama, $harga, $new_path, $kode);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            $stmt = $this->mysqli->prepare(
            "UPDATE `menu` SET 
                    `kode_jenis` = ?, 
                    `nama` = ?, 
                    `harga_jual` = ? 
                    WHERE (`kode` = ?);");
            $stmt->bind_param('isdi',$jenis,$nama, $harga, $kode);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>