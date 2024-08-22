<?php
    $db_server = 'localhost';
    $db_username = 'root';
    $db_password = 'admin123';
    $db_name = 'METADATA';


    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $link = mysqli_connect($db_server, $db_username, $db_password, $db_name);
    
    // if (mysqli_connect_errno()) {
    //     echo "Erro";
    // }else{
    //     echo "Conectado";
    // }
?>