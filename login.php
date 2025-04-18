<?php
session_start();
require_once("class/classUser.php");
$user = new classUser();

$message = "";
$iduser = "";
if(isset($_COOKIE["USER"])){$iduser=$_COOKIE["USER"];}

if (isset($_POST['login'])) {
    $iduser = $_POST["iduser"] ?? '';
    $password = $_POST["password"] ?? '';
    $remember = isset($_POST["remember"]);

    // Validate inputs
    if (empty($iduser) || empty($password)) {
        $message = "Username and password are required";
    } else {
        $result = $user->login($iduser);
            if($result['profil']=='Member' && $result['isaktif']==0) { $message = "Wait for admin to accept your registration"; }
            else if ($result) {
            if (password_verify($password, $result['password'])) {
                session_regenerate_id(true);
                
                $_SESSION["USER"] = $iduser;
                if ($remember) {setcookie("USER", $iduser, time() + (86400 * 30), "/"); }

                $redirect = ($result['profil'] == "Admin") ? "admin/index.php" : "index.php";
                header("Location: $redirect");
                exit();
            } else { $message = "Incorrect username or password"; }
        } else { $message = "Incorrect username or password"; }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee StartBug</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <!-- header -->
    <!-- <header id="header">
    <div class="logo">
        <a href="index.php">LOGO</a>
    </div>
    <div>
        <a href="menu.php">Menu</a>
        <a href="promo.php">Promo</a>
        <a href="voucherku.php">Voucherku</a>
    </div>
    <div class="login">
        <a href="login.php">Login</a>
    </div>
    </header> -->

    <div id="content">
        <h1>Log in</h1>
        <form action="login.php" method="post">
            <label for="iduser">Username</label>
            <input type="text" name="iduser" value="<?=$iduser?>" required>
            <br>
            <label for="password">Password</label>
            <input type="password" name="password" required>
            <br>
            <label for="remember">Remember Me</label>
            <input type="checkbox" name="remember" id="remember">
            <br>
            <input type="submit" value="login" name="login">
        </form>
        <a href="register.php">register</a>
        <p><?=$message?></p>
    </div>
</body>
</html>