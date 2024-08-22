<?php
include('testLogin.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de login</title>
</head>

<style>
    body{
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(45deg,gray,gray);
    }
    .telaLogin{
        background-color: rgba(0, 0, 0, 0.8);
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%,-50%);
        padding: 70px;
        border-radius: 15px;
        color: white;
    }
    input{
        padding: 15px;
        border: none;
        outline: none;
        font-size: 15px;
    }
    .inputSubmit{
        background-color: dodgerblue;
        border: none;
        padding: 15px;
        width: 100%;
        border-radius: 10px;
        color: white;
    }
    .inputSubmit:hover{
        background-color: deepskyblue;
        cursor: pointer;
    }

    .back{
        background-color: dodgerblue;
        border: black;
        padding: 8px;
        width: 100%;
        border-radius: 10px;
        color: white;
        font-size: 15px;
        text-decoration: none;
    }
    .back:hover{
        background-color: deepskyblue;
        cursor: pointer;
    }

    .modal-content{
    color: white;
    position: absolute;
    top: 50%;
    left: 50%;
    -ms-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    width:30%;
    background-color: rgba(0, 0, 0, 0.9);
    padding: 15px;
    border-radius: 15px;

    }

    .modal-title{
        border-bottom: 4px solid white;
    }

</style>

<body>
    <a href="home.php" class="back">Voltar</a>
    <div class="telaLogin">
        <h1 class= "titulo">Login</h1>
        <?php
        if (isset($login_error)) {
            echo "<p>{$login_error}</p>";
        }
        ?>
        <form action="login.php" method="POST">
            <input type="text" name="email" placeholder="Email" required>
            <br><br>
            <input type="password" name="senha" placeholder="Senha" required>
            <br>
            <a id="forgotPasswordLink" style="color: blue;" href="#">Esqueceu sua senha?</a>
            <br><br>
            <input class="inputSubmit" type="submit" name="submit" value="Entrar">
        </form>
    </div>

    <div id="forgotPasswordModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Recuperar Senha</h2>
            <p>Insira seu email para receber as instruções de recuperação de senha:</p>
            <form action="recoveryPassword.php" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <br><br>
                <input class="inputSubmit" type="submit" name="submit" value="Enviar Email de Recuperação">
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById('forgotPasswordModal');
        var forgotPasswordLink = document.getElementById('forgotPasswordLink');
        var closeModal = document.getElementById('closeModal');

        forgotPasswordLink.onclick = function() {
            modal.style.display = "block";
        }

        closeModal.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
