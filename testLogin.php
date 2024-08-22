<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['login_error'])) {
    $login_error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
} else if (isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    include_once('DBconnect.php');
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sqlUsuario = "SELECT * FROM Usuario WHERE email = ?";
    $stmtUsuario = $link->prepare($sqlUsuario);
    $stmtUsuario->bind_param("s", $email);
    $stmtUsuario->execute();
    $resultUsuario = $stmtUsuario->get_result();

    if ($resultUsuario->num_rows > 0) {
        $user = $resultUsuario->fetch_assoc();
        if (password_verify($senha, $user['senha_hash'])) {
            if ($user['sit_user'] == 'active') {
                $_SESSION['cpf'] = $user['cpf'];
                $_SESSION['tipo_user'] = $user['adm'] == '1' ? 'admin' : 'user';
                $_SESSION['email'] = $email;
                $_SESSION['logado'] = 'usuario';
                header('Location: home.php');
                exit();
            } else {
                $_SESSION['login_error'] = 'Usuário inativo ou aguardando confirmação';
            }
        } else {
            $_SESSION['login_error'] = 'Senha incorreta';
        }
    } else {
        $_SESSION['login_error'] = 'Usuário não encontrado';
    }
    header('Location: login.php');
    exit();
}
?>
