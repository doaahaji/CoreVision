<?php
  $currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Core Vision</title>
  <link rel="stylesheet" href="/PFEnagios/css/style.css">
  <link rel="stylesheet" href="/PFEnagios/css/stats-section.css">
  <link rel="stylesheet" href="/PFEnagios/css/tables-section.css">
  <link rel="stylesheet" href="/PFEnagios/css/filters-bar.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body class="dark-mode">

  <aside class="sidebar">
    <div class="brand">
      <img src="/PFEnagios/src/logo.png" alt="logo" class="logo">
      <h1 class="brand-name">
        <span class="core">Core</span><span class="vision">Vision</span>
      </h1>
    </div>
    <ul class="nav-list">
      <li><a href="/PFEnagios/index.php"  class="<?= $currentPage === 'index.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i>Tableau de bord</a></li>
      <li><a href="/PFEnagios/php/pages/hosts.php" class="<?= $currentPage === 'hosts.php' ? 'active' : '' ?>"><i class="fas fa-server"></i> Hosts</a></li>
      <li><a href="/PFEnagios/php/pages/services.php" class="<?= $currentPage === 'services.php' ? 'active' : '' ?>"><i class="fas fa-network-wired"></i> Services</a></li>
      <li><a href="/PFEnagios/php/pages/grhosts.php" class="<?= $currentPage === 'grhosts.php' ? 'active' : '' ?>"><i class="fas fa-layer-group"></i> Groupes Hôtes</a></li>
      <li><a href="/PFEnagios/php/pages/grservices.php" class="<?= $currentPage === 'grservices.php' ? 'active' : '' ?>"><i class="fas fa-link"></i> Groupes Services</a></li>
      <li><a href="/PFEnagios/php/pages/problems.php" class="<?= $currentPage === 'problems.php' ? 'active' : '' ?>"><i class="fas fa-exclamation-triangle"></i> Problèmes</a></li>
      <li><a href="/PFEnagios/php/pages/historique.php" class="<?= $currentPage === 'historique.php' ? 'active' : '' ?>"><i class="fas fa-exclamation-triangle"></i> Historique</a></li>
      <li><a href="/PFEnagios/php/pages/profile.php" class="<?= $currentPage === 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user-circle"></i> Profil</a></li>
    </ul>
  </aside>
  <header class="topbar">
    <div class="search-container">
      <input type="text" class="search-input nav-search" placeholder="Rechercher un hôte, service..." autocomplete="off">
      <ul id="search-suggestions" class="suggestions-list"></ul>
    </div>
    <div class="topbar-actions">
      <button id="theme-toggle" class="icon-button" title="Changer de thème">
        <i class="fas fa-sun"></i>
      </button>

      <button class="icon-button" id="notif-button" title="Notifications">
        <i class="fas fa-bell"></i>
      </button>
      <a href="/PFEnagios/php/logout.php" class="icon-button"><i class="fa-solid fa-right-from-bracket"></i></a>
      
      <div class="notification-dropdown" id="notification-dropdown">
 <h4>Notifications</h4>
 <ul class="notification-list" id="notification-list"></ul>
<div class="notif-footer">
  <a href="/PFEnagios/php/pages/problems.php" class="voir-plus-button">Voir plus</a>
 </div>
</div>

    </div>
  </header>