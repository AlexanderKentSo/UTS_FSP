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
    <link rel="stylesheet" href="css/index.css">
        <link rel="stylesheet" href="css/login.css">

</head>

<body>
        <div class="background-container"></div>
    <div class="overlay"></div>
<div class="login-wrapper">
    <div class="form-container">
        <h1>Welcome to Koffee StartBug</h1>
        <form action="login.php" method="post">
            <div>
                <label for="iduser">Username</label>
                <input type="text" name="iduser" id="iduser" value="<?=$iduser?>" required placeholder="Enter your username">
            </div>
            
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required placeholder="Enter your password">
            </div>
            
            <div>
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember Me</label>
            </div>
            
            <input type="submit" value="Login" name="login" 
            style="width: 80px;
            padding:10px; 
            border-radius: 30%; 
            border: 0px; 
            background-color: #683416; 
            color:wheat">
        </form>
        <a href="register.php" class="register-link">Don't have an account? Register here</a>
        <p class="message"><?=$message?></p>
    </div></div>
</body>
</html>