<?php
    // include('loginCheck.php');
    include('DBconnect.php');

    // if (!$logado || $tipoUsuario !== 'adm') {
    //     // Se não estiver logado ou não for um administrador, redireciona para a página de login
    //     header('Location: login.php');
    //     exit();
    // }

    if (!empty($_GET['cpf'])) {
        $cpf = $_GET['cpf'];
        $sqlSelect = "SELECT * FROM Usuario WHERE cpf=$cpf";
        $result = $link->query($sqlSelect);

        if ($result->num_rows > 0) {
            if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
                $sqlDelete = "DELETE FROM Usuario WHERE cpf=$cpf";
                $resultDelete = $link->query($sqlDelete);

                if ($resultDelete === TRUE) {
                    header('Location: Users.php');
                    exit();
                } else {
                    echo "Error deleting record: " . $link->error;
                }
            } else {
                echo '<script>
                    var confirmed = confirm("Tem certeza que deseja deletar este registro?");
                    if (confirmed) {
                        window.location.href = "deleteUser.php?cpf='.$cpf.'&confirm=true";
                    } else {
                        window.location.href = "Users.php";
                    }
                </script>';
            }
        } else {
            header('Location: Users.php');
            exit();
        }
    } else {
        header('Location: Users.php');
        exit();
    }
?>
