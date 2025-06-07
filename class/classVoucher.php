<?php
require_once("classDB.php");
class classVoucher extends classDB{
    public function __construct() {
        parent::__construct();
    }

    public function getVoucher($offset=null, $limit=null) {
        $sql = "SELECT v.nama AS vnama, m.nama AS mnama, mj.nama AS mjnama, v.*
                    FROM voucher AS v 
                    LEFT JOIN menu AS m 
                    ON v.kode_menu = m.kode
                    LEFT JOIN menu_jenis AS mj
                    ON v.kode_jenis = mj.kode";
		if(!is_null($offset) && !is_null($limit)) $sql .= " LIMIT ?,?";

        $stmt = $this->mysqli->prepare($sql);
		if(!is_null($offset) && !is_null($limit)) 
			$stmt->bind_param('ii', $offset, $limit);
        
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();
		return $res;
    }
    public function getVoucherUser($iduser) {
        $sql = "SELECT v.nama AS vnama, m.nama AS mnama, mj.nama AS mjnama, v.*, kv.kode_unik
                    FROM voucher AS v 
                    LEFT JOIN menu AS m 
                    ON v.kode_menu = m.kode
                    LEFT JOIN menu_jenis AS mj
                    ON v.kode_jenis = mj.kode
                    LEFT JOIN kepemilikan_voucher AS kv
                    ON v.kode = kv.kode_voucher
                    LEFT JOIN member AS mem
                    ON mem.kode = kv.kode_member
                    LEFT JOIN users AS u
                    ON u.iduser = mem.iduser
                    WHERE u.iduser=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('s',$iduser);
        
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();
		return $res;
    }
    public function getTotalData() {
		$res = $this->getVoucher();
		return $res->num_rows;
	}
    public function getVoucherKode($kode) {
        $sql = "SELECT * FROM voucher WHERE kode=?";
		$stmt = $this->mysqli->prepare($sql);
		$stmt->bind_param('i', $kode);

		$stmt->execute();
		$res = $stmt->get_result();
        $stmt->close();
		return $res;
    }
    public function getVoucherClaimer($idvoucher) {
        $sql = "SELECT v.nama AS vnama, m.nama AS mnama, mj.nama AS mjnama, kv.kode_unik, mem.iduser
                    FROM voucher AS v 
                    LEFT JOIN menu AS m 
                    ON v.kode_menu = m.kode
                    LEFT JOIN menu_jenis AS mj
                    ON v.kode_jenis = mj.kode
                    LEFT JOIN kepemilikan_voucher AS kv
                    ON v.kode = kv.kode_voucher
                    LEFT JOIN member AS mem
                    ON mem.kode = kv.kode_member
                    LEFT JOIN users AS u
                    ON u.iduser = mem.iduser
                    WHERE v.kode=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('s',$idvoucher);
        
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();
		return $res;
    }

    public function claimVoucher($iduser, $idvoucher)
    {
        try {
            // Ambil kode member
            $stmt = $this->mysqli->prepare("
                SELECT m.kode 
                FROM member m
                JOIN users u ON m.iduser = u.iduser 
                WHERE u.iduser = ?
            ");
            $stmt->bind_param("s", $iduser);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                return ['success' => false, 'error' => 'User is not a registered member'];
            }
            $kode_member = $result->fetch_assoc()['kode'];
            $stmt->close();

            // Cek kuota voucher
            $stmt = $this->mysqli->prepare("SELECT kuota_sisa FROM voucher WHERE kode = ?");
            $stmt->bind_param("s", $idvoucher);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                return ['success' => false, 'error' => 'Voucher not found'];
            }
            $kuota = $result->fetch_assoc()['kuota_sisa'];
            $stmt->close();

            if ($kuota <= 0) {
                return ['success' => false, 'error' => 'Voucher quota exhausted'];
            }

            // Cek apakah sudah klaim
            $stmt = $this->mysqli->prepare("
                SELECT 1 FROM kepemilikan_voucher 
                WHERE kode_voucher = ? AND kode_member = ?
            ");
            $stmt->bind_param("ss", $idvoucher, $kode_member);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return ['success' => false, 'error' => 'You have already claimed this voucher'];
            }
            $stmt->close();

            // Insert klaim
            $uniqueCode = "voc" . time() . bin2hex(random_bytes(2));
            $stmt = $this->mysqli->prepare("
                INSERT INTO kepemilikan_voucher (kode_member, kode_voucher, kode_unik) 
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sss", $kode_member, $idvoucher, $uniqueCode);
            if (!$stmt->execute()) {
                return ['success' => false, 'error' => 'Failed to claim voucher'];
            }
            $stmt->close();

            // Update kuota
            $stmt = $this->mysqli->prepare("UPDATE voucher SET kuota_sisa = kuota_sisa - 1 WHERE kode = ?");
            $stmt->bind_param("s", $idvoucher);
            $stmt->execute();
            $stmt->close();

            return [
                'success' => true,
                'voucher_code' => $uniqueCode,
                'member_code' => $kode_member
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ];
        }
    }


    public function insertVoucher($menu, $jenis, $nama, $start, $end, $kuota, $diskon) {
        // Convert empty values to NULL
        $menu = ($menu === '' || $menu === '0') ? null : (int)$menu;
        $jenis = ($jenis === '' || $jenis === '0') ? null : (int)$jenis;

        $stmt = $this->mysqli->prepare(
            "INSERT INTO `voucher` 
            (`kode_menu`, `kode_jenis`, `nama`, `mulai_berlaku`, `akhir_berlaku`, `kuota_max`, `kuota_sisa`, `persen_diskon`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param('iisssiii',$menu,$jenis,$nama,$start,$end,$kuota,$kuota,$diskon);
        
        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Insert failed: " . $error);
        }
        
        $last_id = $stmt->insert_id;
        $stmt->close();
        return $last_id;
    }

    public function deleteVoucher($kode){
        $stmt = $this->mysqli->prepare("DELETE FROM `voucher` WHERE (`kode` = ?);");
        $stmt->bind_param('i',$kode);
        $stmt->execute();
        $stmt->close();
    }

    public function updateVoucher($menu, $jenis, $nama, $start, $end, $kuota, $diskon, $kode){
        if($start>$end){echo "end date can't occur before start date";}
        else{
            if($menu == ""){
                $stmt = $this->mysqli->prepare(
                    "UPDATE `voucher` SET 
                            `kode_menu` = NULL,
                            `kode_jenis` = ?, 
                            `nama` = ?, 
                            `mulai_berlaku` = ?, 
                            `akhir_berlaku` = ?, 
                            `kuota_max` = ?,
                            `kuota_sisa` = ?,
                            `persen_diskon` = ? 
                            WHERE (`kode` = ?);");
                $stmt->bind_param('isssiiii',$jenis, $nama, $start, $end, $kuota,$kuota, $diskon, $kode);
                $stmt->execute();
                $stmt->close();
            }
            else if($jenis == ""){
                $stmt = $this->mysqli->prepare(
                    "UPDATE `voucher` SET 
                            `kode_menu` = ?, 
                            `kode_jenis` = NULL, 
                            `nama` = ?, 
                            `mulai_berlaku` = ?, 
                            `akhir_berlaku` = ?, 
                            `kuota_max` = ?,
                            `kuota_sisa` = ?,
                            `persen_diskon` = ? 
                            WHERE (`kode` = ?);");
                $stmt->bind_param('isssiiii',$menu, $nama, $start, $end, $kuota, $kuota, $diskon, $kode);
                $stmt->execute();
                $stmt->close();
            }
            else{
                $stmt = $this->mysqli->prepare(
                    "UPDATE `voucher` SET 
                            `kode_menu` = ?,
                            `kode_jenis` = ?,
                            `nama` = ?, 
                            `mulai_berlaku` = ?, 
                            `akhir_berlaku` = ?, 
                            `kuota_max` = ?,
                            `kuota_sisa` = ?,
                            `persen_diskon` = ? 
                            WHERE (`kode` = ?);");
                $stmt->bind_param('iisssiiii',$menu,$jenis, $nama, $start, $end, $kuota, $kuota, $diskon, $kode);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}
?>