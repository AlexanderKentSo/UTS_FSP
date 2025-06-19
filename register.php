<?php
require_once("class/classUser.php");
session_start();
$member = new classUser();

$message = "";

if (isset($_POST['register'])) {
    $iduser = htmlentities($_POST["iduser"]);

    if ($member->checkIDUser($iduser)) {
        $hash_password = password_hash(htmlentities($_POST["password"]),PASSWORD_DEFAULT);
        $nama = htmlentities($_POST["nama"]);
        $tgllahir =  htmlentities($_POST["tgllahir"]);
        $foto =  htmlentities($_POST["foto"]);
        $profil = "Member";
        $status = 0;

        if(!is_null($member->insertMember($iduser, $hash_password, $profil,$nama, $tgllahir, $foto, $status)))
        {header("Location: login.php");exit();}
    } else { $message = "username already taken"; }
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
        <h1>Register</h1>
        <form action="register.php" method="post">
            <div>
            <label for="iduser">Username</label>
            <input type="text" name="iduser" required>
            </div>

            <div>
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Create Password" required>
            </div>

            <div>
            <label for="nama">Nama</label>
            <input type="text" name="nama"placeholder="Enter nama " required>
            </div>

            <div>
            <label for="tgllahir">Tanggal Lahir</label>
            <input type="date" name="tgllahir" required>
            </div>

            <div>
            <label for="foto">Foto</label>
            <input type="text" name="foto"placeholder="Enter image URL" required>
            </div>
            
            <input type="submit" value="register" name="register"
            style="width: 80px;
            padding:10px; 
            border-radius: 30%; 
            border: 0px; 
            background-color: #683416; 
            color:wheat">
        </form>
        <p><?=$message?></p>
    </div>
    </div>
</body>
</html>