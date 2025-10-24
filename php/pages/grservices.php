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
        <h1>Liste des Groupes de Services</h1>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/PFEnagios/php/filters-services.php'; ?>

    <div class="table-container">
        <table class="styled-table" id="serviceGroupTable">
            <thead>
                <tr>
                    <th data-sort="group"><span class="th-text">Groupe</span><i class="fa-solid fa-sort sort-icon"></i></th>
                    <th data-sort="service"><span class="th-text">Service</span><i class="fa-solid fa-sort sort-icon"></i></th>
                    <th data-sort="state"><span class="th-text">Statut du Service</span><i class="fa-solid fa-sort sort-icon"></i></th>
                    <th><span class="th-text">Détail</span></th>
                </tr>
            </thead>
            <tbody id="serviceGroupBody">
                <!-- lignes dynamiques -->
            </tbody>
        </table>
    </div>
</main>

<script src="/PFEnagios/js/grservices.js"></script>
<script src="/PFEnagios/js/stats-section.js"></script>
<script src="/PFEnagios/js/theme-toggle.js"></script>
<script src="/PFEnagios/js/filters-bar.js"></script>
<script src="/PFEnagios/js/nav-search.js"></script>
<script src="/PFEnagios/js/notification.js"></script>
</body>
</html>
