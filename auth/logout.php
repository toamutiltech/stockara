<?php
session_start();
require_once '../includes/functions.php';
session_destroy();
header('Location: ' . BASE_URL . 'auth/login.php');
exit();
?>
