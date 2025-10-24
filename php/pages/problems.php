<?php
session_start();

if (!isset($_SESSION['user'])) {
header('Location: /PFEnagios/php/pages/login.php');
exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/PFEnagios/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Core Vision</title>
</head>
<body class="dark-mode" id="problems-page">


    <?php include '../header.php'; ?>
<main id="main-content">
<div id="topsection">
    <div id="graph-section">
        <div class="chart-container problem-chart">
  <canvas id="host-chart"></canvas>
  <div class="chart-legend" id="host-legend"></div>
</div>
        <div class="chart-container problem-chart">
  <canvas id="service-chart"></canvas>
  <div class="chart-legend" id="service-legend"></div>
</div>
    </div>
    <div id="statistic-section">
        <div class="statistic-numbers">
      <div class="status-badge">
        <div class="status-card">
          <i class="fa-solid fa-circle-exclamation"></i>
          <h5 id="down-hosts"></h5>
          <h4>Hôtes Down</h4>
          
        </div>
        <div class="status-card">
          <i class="fa-solid fa-circle-minus"></i>
          <h5 id="unreachable-hosts"></h5>
          <h4>Hôtes Unreachable</h4>
          
        </div>
      </div>
      <div class="status-badge">
        <div class="status-card">
          <i class="fa-solid fa-clock"></i>
          <h5 id="pending-hosts"></h5>
          <h4>Hôtes Pending</h4>
          
        </div>
        <div class="status-card">
          <i class="fa-solid fa-circle-minus"></i>
          <h5 id="warning-services"></h5>
          <h4> Services Warning</h4>
          
        </div>
      </div>
    </div>
<!---->
    <div class="statistic-numbers">
      <div class="status-badge">
        <div class="status-card">
          <i class="fa-solid fa-circle-question"></i>
          <h5 id="unknown-services">0</h5>
          <h4> Services Unknown</h4>
          
        </div>
        <div class="status-card">
          <i class="fa-solid fa-circle-exclamation"></i>
          <h5 id="critical-services">0</h5>
          <h4> Services Critical</h4>
          
        </div>
      </div>
      <div class="status-badge">
        <div class="status-card">
          <i class="fa-solid fa-clock"></i>
          <h5 id="pending-services">0</h5>
          <h4> Services Pending</h4>
          
        </div>
        <div class="status-card">
          <i class="fa-solid fa-circle-exclamation"></i>
          <h5 id="total-problems">0</h5>
          <h4>Total problèmes</h4>
          
        </div>
      </div>
    </div>
    </div>
</div>
<div class="prob-buttons">
        <button class="problem-types"  id="all-prob">Tous</button>
        <button class="problem-types" id="host-prob">Hôtes</button>
        <button class="problem-types" id="service-prob">Services</button></div>
    <div id="table-container">
  <div id="host-table-container" class="problems-table" style="display: none;"></div>
  <div id="service-table-container" class="problems-table" style="display: none;"></div>
</div>
  
</main>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/PFEnagios/js/problem_charts.js"></script>
    <script src="/PFEnagios/js/theme-toggle.js"></script>
    <script src="/PFEnagios/js/problem-tabs.js"></script>
    <script src="/PFEnagios/js/notification.js"></script>
</body>
</html>