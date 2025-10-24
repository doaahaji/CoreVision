<?php
session_start();

if (!isset($_SESSION['user'])) {
header('Location: /PFEnagios/php/pages/login.php');
exit();
}

require '../db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //pour changer fuseau horaire
    if (isset($_POST['timezone']) && in_array($_POST['timezone'], timezone_identifiers_list())) {
        $newTz = $_POST['timezone'];
        $userId = $_SESSION['user']; 
        $stmt = $conn->prepare("UPDATE users SET fuseau_horaire = ? WHERE nom_utilisateur = ?");
        $stmt->execute([$newTz, $userId]);

        $_SESSION['horaire'] = $newTz;
    }

    //pour changer image
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '/PFEnagios/src/userImages/';
    $uploadPath = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    $filename = basename($_FILES['avatar']['name']);
    $targetFile = $uploadPath . $filename;
    $relativePath = $uploadDir . $filename;

    // pour Deplacer l'image
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("UPDATE users SET _image = ? WHERE nom_utilisateur = ?");
        $stmt->execute([$relativePath, $_SESSION['user']]);
        $_SESSION['image'] = $relativePath;
    }
}
//pour changer user info (nom,prenom,role)
if (isset($_POST['update_user_info'])) {
    $newPrenom = trim($_POST['prenom']);
    $newNom = trim($_POST['nom']);
    $newRole = $_POST['role'];

    if (!empty($newPrenom) && !empty($newNom) && in_array($newRole, ['admin', 'read-only', 'operator'])) {
        $stmt = $conn->prepare("UPDATE users SET prenom = ?, nom = ?, _role = ? WHERE nom_utilisateur = ?");
        $stmt->execute([$newPrenom, $newNom, $newRole, $_SESSION['user']]);

        $_SESSION['prenom'] = $newPrenom;
        $_SESSION['nom'] = $newNom;
        $_SESSION['_role'] = $newRole;
    }
}
//pour changer mot de passe
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        echo "<script>alert('Les mots de passe ne correspondent pas.');</script>";
    } elseif (strlen($new) < 4) {
        echo "<script>alert('Le mot de passe doit contenir au moins 4 caractères.');</script>";
    } else {
        // Recuperer le mot de passe actuel depuis la BDD
        $stmt = $conn->prepare("SELECT pass FROM users WHERE nom_utilisateur = ?");
        $stmt->execute([$_SESSION['user']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($current, $row['pass'])) {
            $hashed = password_hash($new, PASSWORD_BCRYPT);
            $updateStmt = $conn->prepare("UPDATE users SET pass = ? WHERE nom_utilisateur = ?");
            $updateStmt->execute([$hashed, $_SESSION['user']]);
            echo "<script>alert('Mot de passe mis à jour avec succès.');</script>";
        } else {
            echo "<script>alert('Mot de passe actuel incorrect.');</script>";
        }
    }
}
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Core Vision</title>
  <link rel="stylesheet" href="/PFEnagios/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="dark-mode">
  <script>
  const currentUserTimezone = <?= json_encode($_SESSION['horaire'] ?? 'UTC') ?>;
</script>
<?php include '../header.php'; ?>
<main id="main-content">
  <div class="profile-container">
    <h1 class="profile-title">Profil</h1>
    
    <div class="profile-header">
  <div class="avatar-container">
    <form action="" method="post" enctype="multipart/form-data" id="avatar-form">
      <img src="<?= htmlspecialchars($_SESSION['image'] ?? '/PFEnagios/src/user.jpeg') ?>" class="profile-avatar" alt="User image">
      
      <input type="file" name="avatar" id="avatar-input" accept="image/*" style="display: none;" onchange="document.getElementById('avatar-form').submit();">

      <button type="button" class="avatar-upload" onclick="document.getElementById('avatar-input').click();">
        <i class="fas fa-camera"></i>
      </button>
    </form>
  </div>

      <div class="user-info">
        <h2 class="username"><?= $_SESSION['user']?></h2>
        <p class="email"><?= $_SESSION['email']?></p>
        <p class="member-since">Member since <?= $_SESSION['date inscription']?></p>
      </div>
    </div>

    <div class="profile-sections">
<section class="profile-section">
  <h3><i class="fas fa-user-circle"></i>Informations utilisateur</h3>
  <form class="profile-form" method="post" action="">


    <div class="form-group">
      <label>Prénom</label>
      <input type="text" name="prenom" value="<?= htmlspecialchars($_SESSION['prenom']) ?>">
    </div>

    <div class="form-group">
      <label>Nom</label>
      <input type="text" name="nom" value="<?= htmlspecialchars($_SESSION['nom']) ?>">
    </div>

    <div class="form-group">
      <label>Rôle</label>
      <select name="role" class="select">
        <option value="admin" <?= $_SESSION['_role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="read-only" <?= $_SESSION['_role'] === 'read-only' ? 'selected' : '' ?>>Read-Only</option>
        <option value="operator" <?= $_SESSION['_role'] === 'operator' ? 'selected' : '' ?>>Operator</option>
      </select>
    </div>

    <button type="submit" name="update_user_info" class="btn-primary">Enregistrer</button>
  </form>
</section>


<section class="profile-section">
  <h3><i class="fas fa-lock"></i> Sécurité</h3>
  <form class="profile-form" method="post" action="">
    <div class="form-group">
      <label>Mot de passe actuel</label>
      <input type="password" name="current_password" placeholder="Enter current password" required>
    </div>
    <div class="form-group">
      <label>Nouveau mot de passe</label>
      <input type="password" name="new_password" placeholder="Enter new password" required>
    </div>
    <div class="form-group">
      <label>Confirmer le nouveau mot de passe</label>
      <input type="password" name="confirm_password" placeholder="Confirm new password" required>
    </div>
    <button type="submit" name="change_password" class="btn-primary">Mettre à jour</button>
  </form>
</section>


      <section class="profile-section">
        <h3><i class="fas fa-palette"></i>Paramètres du compte</h3>
        <form  class="profile-form" method="post">
          <div class="form-group radio-group">
            <label>Préférence de thème</label>
            <div class="radio-options">
              <label class="radio-option">
                <input type="radio" name="theme" value="dark" id="dark-mode">
                <span class="radio-custom"></span>
                Dark Mode
              </label>
              <label class="radio-option">
                <input type="radio" name="theme" value="light" id="light-mode">
                <span class="radio-custom"></span>
                Light Mode
              </label>
            </div>
            <label>Fuseau horaire</label>
            <select name="timezone" id="timezone-select" class="select"></select>
          </div>
          <button type="submit" class="btn-primary">Enregistrer</button>
        </form>
      </section>
    </div>
  </div>
</main>
<script src="/PFEnagios/js/timezones.js" defer></script>
</body>
<script src="/PFEnagios/js/theme.js"></script>
<script src="/PFEnagios/js/notification.js"></script>

</html>
