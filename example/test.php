<?php

require_once 'platonusAPI.php';

try {
    // Инициализация API
    $api = new Platonus();

    // Авторизация
    $api->login('login', 'password');
    
    $journal = $api->getJournal(2024, 1, 'ru');
    print_r($journal);

    $subjects = $api->getSubjects(2024, 1);
    print_r($subjects);
    
    $subject = $api->getSubject(2024, 1, 24172);
    print_r($subject);

    $logoutMessage = $api->logout();
    echo "Logout: $logoutMessage\n";

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
