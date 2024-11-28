<?php
session_start();
require_once 'platonusAPI.php';

if (isset($_SESSION['authToken']) && isset($_SESSION['sid'])) {
    $platonus = new Platonus();
    $platonus->authToken = $_SESSION['authToken'];
    $platonus->sid = $_SESSION['sid'];

    try {
        $platonus->logout();
    } catch (Exception $e) {
        // Ошибку можно записать в лог
    }
}

// Уничтожение сессии
session_destroy();
header('Location: index.php');
exit;
?>
