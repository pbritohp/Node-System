<?php
    //Código para terminar a sessão e voltar ao loggin
    session_start();
    session_destroy();
    header('Location: login.php');
    exit();

?>