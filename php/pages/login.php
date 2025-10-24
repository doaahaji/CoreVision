<?php
session_start();
require_once '../db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$user = $_POST['username'];
$pass = $_POST['password'];

$sql = "SELECT * FROM users WHERE nom_utilisateur = :username";
$stmt = $conn->prepare($sql);
$stmt->execute(['username' => $user]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userData && password_verify($pass, $userData['pass'])) {
 $_SESSION['user'] = $userData['nom_utilisateur'];
$_SESSION['nom'] = $userData['nom'];
$_SESSION['prenom'] = $userData['prenom'];
$_SESSION['email'] = $userData['email'];
$_SESSION['image'] = $userData['_image'];
$_SESSION['role'] = $userData['_role'];
$_SESSION['horaire'] = $userData['fuseau_horaire'];
$_SESSION['date inscription'] = $userData['date_inscription'];
$_SESSION['image'] = $userData['_image'];
    header("Location: ../../index.php");
  exit();
  } else {
    $error = "Identifiants incorrects.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/PFEnagios/css/style.css">
    <!--To link  html with css-->
    <!--Link for using icons-->
    <script src="https://kit.fontawesome.com/13aace1a4f.js" crossorigin="anonymous"></script>
    <title>Core Vision</title>
</head>
<body class="dark-mode">
    <div id="container"><!--I use this div to center the main-login horizontally and vertically-->
    <header id="appearance">
      <div class="topbar-actions">
    <button id="theme-toggle" class="icon-button" title="Changer de thème">
      <i class="fas fa-moon"></i>
    </button>
  </div>
    </header>
    <div id="main-login">
        <div id="logo-sitename">
            <img src="/PFEnagios/src/logo.png" class="img-logo"/>
            <span class="logo-text"><span class="core">Core </span><span class="vision">Vision</span></span>
        </div>
        <form action="" method="post">
            <div id="signin-info">
                <div class="info-user">
                    <label for="username">Utilisateur</label>
                    <input name="username" type="text" placeholder="enter your username" id="username"><br/>
                </div>
                <div class="info-user">
                    <label for="password">Mot de Passe:</label>
                    <input name="password" type="password" placeholder="enter your password" id="password">
                </div>
                </div>
                <div id="signin-submit">
                    <button type="submit" id="signin-button" aria-label="Login to your account">Se connecter</button>
                </div>
            </form>
        </div>
    </div>
    <script src="/PFEnagios/js/theme-toggle.js"></script>
</body>
</html>