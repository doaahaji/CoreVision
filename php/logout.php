<?php
session_start();
session_unset();
session_destroy();
header('Location: /PFEnagios/php/pages/login.php');
?>