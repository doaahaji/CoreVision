<?php
session_start();

if (!isset($_SESSION['user'])) {
header('Location: /PFEnagios/php/pages/login.php');
exit();
}
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/PFEnagios/php/header.php'; ?>

<main class="main-content">
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/PFEnagios/php/stats-section.php'; ?>

  <div class="page-header">
    <h1>Liste des Hôtes</h1>
  </div>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/PFEnagios/php/filters-hosts.php' ?>

  <div class="table-container">
    <table class="styled-table" id="hostsTable">
      <thead>
        <tr>
          <th data-sort="name">
            <span class="th-text">Host</span><i class="fa-solid fa-sort sort-icon"></i>
          </th>
          <th data-sort="ip">
            <span class="th-text">Adresse IP</span><i class="fa-solid fa-sort sort-icon"></i>
          </th>
          <th data-sort="state">
            <span class="th-text">État</span><i class="fa-solid fa-sort sort-icon"></i>
          </th>
          <th data-sort="group">
            <span class="th-text">Groupe</span><i class="fa-solid fa-sort sort-icon"></i>
          </th>
          <th data-sort="status_info">
            <span class="th-text">Statut Info</span><i class="fa-solid fa-sort sort-icon"></i>
          </th>
          <th data-sort="last">
            <span class="th-text">Dernière vérif</span><i class="fa-solid fa-sort sort-icon"></i>
          </th>
        </tr>
      </thead>
      <tbody id="hostsBody">
        <!-- Remplissage via JS -->
      </tbody>
    </table>
  </div>
</main>

<!-- JS -->
<script src="/PFEnagios/js/hosts.js"></script>
<script src="/PFEnagios/js/stats-section.js"></script>
<script src="/PFEnagios/js/theme-toggle.js"></script>
<script src="/PFEnagios/js/filters-bar.js"></script>
<script src="/PFEnagios/js/nav-search.js"></script>
<script src="/PFEnagios/js/notification.js"></script>
</body>
</html>
