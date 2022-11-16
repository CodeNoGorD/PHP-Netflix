<?php
    session_start();  // initialiser la session
    session_unset(); // désactive la session
    session_destroy(); // détruire la session

    setcookie('auth', '', time() -1);

    header('location: ./index.php');
    exit();