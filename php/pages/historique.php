<?php
session_start();

if (!isset($_SESSION['user'])) {
header('Location: /PFEnagios/php/pages/login.php');
exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Vision</title>
    <link rel="stylesheet" href="/PFEnagios/css/style.css">
    <link rel="stylesheet" href="/PFEnagios/css/historique.css">
  <link rel="stylesheet" href="/PFEnagios/css/header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="dark-mode">
    <?php include '../header.php'; ?>
    <main id="main-content">
<div class="history-header">
    <h2>Historique des alertes</h2>
    <div class="header-controls">
        <div class="timeframe-selector">
           
    <select id="time-filter" class="filter-select">
        <option value="1h">Dernière  heure</option>
        <option value="12h">Dernières 12 heures</option>
        <option value="24h" selected>Dernières 24 heures</option>
        <option value="7d">7 derniers jours</option>
        <option value="all">Tout l'historique</option>
    </select>

        </div>
        <button id="refresh-btn" class="refresh-button">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>

    <div id="last-update"></div>
    <div id="history-data"></div></main>

    
</body>
</html><script src="/PFEnagios/js/historique.js"></script>
    <script src="/PFEnagios/js/theme-toggle.js"></script>
    <script src="/PFEnagios/js/notification.js"></script>