<?php
session_start();
require_once("../class/classUser.php"); // Make sure this path is correct
$user = new classUser();

$message = "";
$show_active = isset($_GET['show']) && $_GET['show'] == 'active';

// Redirect if not admin (Crucial security check)
if ($_SESSION["USER"] != "admin") {
    header("location: ../logout.php");
    exit();
}

if(isset($_GET['kode']) && isset($_GET['aksi'])){
    $id = $_GET['kode'];
    $current_show_param = $show_active ? 'active' : 'inactive'; // Preserve the current tab state

    if($_GET['aksi']=='terima'){
        $user->acceptMember($id);
        $message = "Member " . htmlspecialchars($id) . " accepted successfully.";
    } else {
        $user->declineMember($id);
        $message = "Member " . htmlspecialchars($id) . " declined.";
    }
    header("Location: member.php?show=".$current_show_param); // Redirect back to the same tab
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee StartBug - Kelola Member</title>
    <link rel="stylesheet" href="../css/index.css">
     <link rel="stylesheet" href="../css/member.css"> </head>
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
    
    <div style="
        position: relative;
        z-index: 1;
        padding: 60px 20px 20px;
        color: antiquewhite;">
        
        <h1 style="font-size: 80px; margin: 0;">Kelola Member</h1>
        <p style="font-size: 24px; margin-top: 10px;"><?=$message?></p>

        <div class="tab-container">
            <a href="member.php" class="tab <?= !$show_active ? 'active-tab' : '' ?>">
                Pending Members
            </a>
            <a href="member.php?show=active" class="tab <?= $show_active ? 'active-tab' : '' ?>">
                Active Members
            </a>
        </div>

        <?php if(!$show_active): ?>
            <h2>Member yang belum aktif:</h2>
            <?php
            $jmlh = $user->getTotalDataMemberNonActive();
            $limit = 5;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $max_page = ceil($jmlh/$limit);
            $offset = ($page-1)*$limit;
            $res = $user->getMemberNonActive($offset, $limit);
            ?>
            <?php if ($res && $res->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Tanggal Lahir</th>
                            <th>Foto</th>
                            <th>Terima</th>
                            <th>Tolak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['iduser']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_lahir']) ?></td>
                            <td><img src="<?= htmlspecialchars($row["url_foto"]) ?>" alt="Foto Member"></td>
                            <td><a href="member.php?kode=<?= htmlspecialchars($row['iduser']) ?>&aksi=terima&show=inactive" class="action-link accept">Terima</a></td>
                            <td><a href="member.php?kode=<?= htmlspecialchars($row['iduser']) ?>&aksi=tolak&show=inactive" class="action-link decline">Tolak</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No pending members.</p>
            <?php endif; ?>
        <?php else: ?>
            <h2>Member yang sudah aktif:</h2>
            <?php
            $jmlh = $user->getTotalDataMemberActive();
            $limit = 5;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $max_page = ceil($jmlh/$limit);
            $offset = ($page-1)*$limit;
            $res = $user->getMemberActive($offset, $limit);
            ?>
            <?php if ($res && $res->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Tanggal Lahir</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['iduser']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_lahir']) ?></td>
                            <td><img src="<?= htmlspecialchars($row["url_foto"]) ?>" alt="Foto Member"></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No active members.</p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($max_page > 1): ?>
            <div class="pagination">
                <?php if($page != 1): ?>
                    <a href="member.php?page=1&show=<?= $show_active ? 'active' : 'inactive' ?>">First</a>
                    <a href="member.php?page=<?= $page-1 ?>&show=<?= $show_active ? 'active' : 'inactive' ?>">Prev</a>
                <?php endif; ?>
                
                <?php for($i=1; $i<=$max_page; $i++): ?>
                    <?php if($i != $page): ?>
                        <a href="member.php?page=<?= $i ?>&show=<?= $show_active ? 'active' : 'inactive' ?>"><?= $i ?></a>
                    <?php else: ?>
                        <span><?= $i ?></span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if($page != $max_page): ?>
                    <a href="member.php?page=<?= $page+1 ?>&show=<?= $show_active ? 'active' : 'inactive' ?>">Next</a>
                    <a href="member.php?page=<?= $max_page ?>&show=<?= $show_active ? 'active' : 'inactive' ?>">Last</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>