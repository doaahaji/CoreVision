
<?php
session_start();

if (!isset($_SESSION['user'])) {
header('Location: /PFEnagios/php/pages/login.php');
exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Core Vision</title>
  <link rel="stylesheet" href="/PFEnagios/css/style.css">
  <link rel="stylesheet" href="/PFEnagioscss/header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="dark-mode" id="index-page">
  <?php
  $currentPage = basename($_SERVER['PHP_SELF']);
?>
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
      <li><a href="/PFEnagios/php/pages/grhosts.php"><i class="fas fa-layer-group"></i> Groupes Hôtes</a></li>
      <li><a href="/PFEnagios/php/pages/grservices.php"><i class="fas fa-link"></i> Groupes Services</a></li>
      <li><a href="/PFEnagios/php/pages/problems.php" class="<?= $currentPage === 'problems.php' ? 'active' : '' ?>"><i class="fas fa-exclamation-triangle"></i> Problèmes</a></li>
      <li><a href="/PFEnagios/php/pages/historique.php" class="<?= $currentPage === 'historique.php' ? 'active' : '' ?>"><i class="fas fa-exclamation-triangle"></i> Historique</a></li>
      <li><a href="/PFEnagios/php/pages/profile.php" class="<?= $currentPage === 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user-circle"></i> Profil</a></li>
    </ul>
  </aside>
  <header class="topbar">
  <input type="text" class="search-input" placeholder="Rechercher...">
  <div class="topbar-actions">
    <button id="theme-toggle" class="icon-button" title="Changer de thème">
      <i class="fas fa-moon"></i>
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
<main id="main-content">
  <div id="statistic">
    <div id="statistic-as-numbers">
      <div class="status-badge">
        <div class="status-card">
          <i class="fa-solid fa-server"></i>
          <h5 id="total-hosts"></h5>
          <h4>Total Hôtes</h4>
        </div>
        <div class="status-card">
          <i class="fa-solid fa-gears"></i>
          <h5 id="total-services"></h5>
          <h4>Total Services</h4> 
        </div>
      </div>
      <div class="status-badge">
        <div class="status-card">
          <i class="fa-solid fa-database"></i>
          <h5 id="total-backends"></h5>
          <h4>Total Backends</h4>
        </div>
        <div class="status-card">
          <i class="fa-solid fa-users"></i>
          <h5 id="total-contacts"></h5>
          <h4>Total Contacts</h4>
          
        </div>
      </div>
    </div>
    <div id="statistic-as-graphs">
      <div class="graph">
        <h4>Hôtes</h4>
        <div class="chart-container">
  <canvas id="host-chart"></canvas>
  <div class="chart-legend" id="host-legend"></div>
</div>
        <div class="graph-buttons">
        <button class="chart-types" onclick="sethost_chartType('doughnut')">Doughnut</button>
        <button class="chart-types" onclick="sethost_chartType('bar')">Bar</button>
        <button class="chart-types" onclick="sethost_chartType('polarArea')">PolarArea</button></div>
      </div>
      <div class="graph">
        <h4>Services</h4>
        <div class="chart-container">
  <canvas id="service-chart"></canvas>
  <div class="chart-legend" id="service-legend"></div>
</div>
        <div class="graph-buttons">
        <button class="chart-types" onclick="setservice_chartType('doughnut')">Doughnut</button>
        <button class="chart-types" onclick="setservice_chartType('bar')">Bar</button>
        <button class="chart-types" onclick="setservice_chartType('polarArea')">PolarArea</button></div>
      </div>
    </div>
  </div>
  <div id="problems">
   <div id="table-container" >
    <div class="problem-section">
  <div id="host-table-container" class="prob-table"></div>
  <a href="php/pages/problems.php" id="see-host-prob">Voir plus</a></div>
  <div class="problem-section">
  <div id="service-table-container" class="prob-table"></div>
  <a href="php/pages/problems.php" id="see-service-prob">Voir plus</a></div>
</div>
    </div>
  </div>
</main>
<!--Js links-->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/PFEnagios/js/charts.js"></script>
<script src="/PFEnagios/js/tabs.js"></script>
<script src="/PFEnagios/js/theme-toggle.js"></script>
<script src="/PFEnagios/js/total_numbers.js"></script>
<script src="/PFEnagios/js/notification.js"></script>
</body>
</html>