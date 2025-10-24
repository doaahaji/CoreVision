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
        <h1>Liste des Groupes d'Hôtes</h1>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/PFEnagios/php/filters-hosts.php'; ?>

    <div class="table-container">
        <table class="styled-table" id="hostGroupTable">
            <thead>
                <tr>
                    <th data-sort="group"><span class="th-text">Groupe</span><i class="fa-solid fa-sort sort-icon"></i></th>
                    <th data-sort="name"><span class="th-text">Hôte</span><i class="fa-solid fa-sort sort-icon"></i></th>
                    <th data-sort="host_status"><span class="th-text">Statut du Hôte</span><i class="fa-solid fa-sort sort-icon"></i></th>
                    <th data-sort="service_count"><span class="th-text">Nb. de Services</span><i class="fa-solid fa-sort sort-icon"></i></th>
                    <th data-sort="service_status"><span class="th-text">Statut des Services</span><i class="fa-solid fa-sort sort-icon"></i></th>
                    <th><span class="th-text">Détails</span></th>
                </tr>
            </thead>
            <tbody id="hostGroupBody">
                <!-- lignes dynamiques JS -->
            </tbody>
        </table>
    </div>
</main>

<script src="/PFEnagios/js/grhosts.js"></script>
<script src="/PFEnagios/js/stats-section.js"></script>
<script src="/PFEnagios/js/theme-toggle.js"></script>
<script src="/PFEnagios/js/filters-bar.js"></script>
<script src="/PFEnagios/js/nav-search.js"></script>
<script src="/PFEnagios/js/notification.js"></script>
</body>
</html>
