<?php

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'lib/vendor/autoload.php';
include('DBconnect.php');
include('loginCheck.php');

//include('loginCheck.php');

// if ($tipoUsuario != 'admin') {
//     echo header('Location: login.php');
//     exit();
// }

if (isset($_POST['submit'])) {
    $cpf = $_POST['cpf'];
    $email = $_POST['email'];
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $area = $_POST['area'];

    $tempKey = generateRandomKey();

    $query = mysqli_prepare($link, "INSERT INTO Usuario (cpf, nome, email, tempKey, adm, idArea) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($query, "ssssii", $cpf, $nome, $email, $tempKey, $tipo, $area);

    if (mysqli_stmt_execute($query)) {
        $successLab = "Cadastro realizado com sucesso!";
        
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'b5ad514f1a13a1';
            $mail->Password = '4209a152752acd';

            $mail->setFrom('meta@meta.com', 'Metadata');
            $mail->addAddress($email, $nome);
            $mail->isHTML(true);
            $mail->Subject = 'Novo cadastro';
            $mail->Body = $nome . ", segue o link de convite para se cadastrar na plataforma Metadata <br><br>"
            . "Convite: <a href='http://localhost/vsxampp/meta/registerUser.php?tempKey=" . $tempKey . "'>Clique aqui</a>";
            $mail->AltBody = $nome . ", segue o link de convite para se cadastrar na plataforma Metadata \n\n"
            . "Convite: http://localhost/vsxampp/meta/registerUser.php?tempKey=" . $tempKey;

            $mail->send();
            $successLab .= " Email enviado com sucesso!";
        } catch (Exception $e) {
            $errorLab = "Erro ao enviar o email: " . $mail->ErrorInfo;
        }

        mysqli_stmt_close($query);
    } else {
        $errorLab = "Erro ao cadastrar. Email já cadastrado ou aguardando cadastro.";
    }
}

function generateRandomKey($length = 12) {
    $randomBytes = random_bytes($length);
    $key = bin2hex($randomBytes);
    return $key;
}

// Fetching areas from the database
$areaQuery = "SELECT id, nome FROM AreaU";
$areaResult = mysqli_query($link, $areaQuery);
$areas = [];

if ($areaResult) {
    while ($row = mysqli_fetch_assoc($areaResult)) {
        $areas[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>New User | Node</title>
</head>

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(90deg, gray, gray);
    }

    .box {
        position: relative;
        color: white;
        margin-top: 5%;
        margin-left: 5%;
        margin-bottom: 10%;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;
        width: 400px;
    }

    fieldset {
        border: 3px solid dodgerblue;
        border-radius: 5px;
    }

    legend {
        border: 1px solid dodgerblue;
        padding: 10px;
        background-color: dodgerblue;
        border-radius: 8px;
        font-size: 20px;
    }

    .inputBox {
        position: relative;
    }

    .inputUser {
        background: none;
        border: none;
        border-bottom: 1px solid white;
        color: white;
        outline: none;
        font-size: 15px;
        width: 100%;
        letter-spacing: 1px;
    }

    .labelInput {
        position: absolute;
        top: 0px;
        left: 0px;
        pointer-events: none;
        transition: .5s;
    }

    .inputUser:focus ~ .labelInput,
    .inputUser:valid ~ .labelInput {
        top: -20px;
        font-size: 12px;
        color: dodgerblue;
    }

    .select {
        border: none;
        padding: 8px;
        border-radius: 10px;
        outline: none;
        width: 100%;
    }

    .submit-c {
        background-image: linear-gradient(to right, dodgerblue, dodgerblue);
        width: 100%;
        color: white;
        border: none;
        padding: 15px;
        font-size: 15px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center;
    }

    .submit-c:hover {
        background-image: linear-gradient(to right, deepskyblue, deepskyblue);
    }
</style>

<?php include('nav.php')?>

<body>
    <div class="box">
        <form method='POST'>
            <fieldset>
                <legend class="title"><b>Novo Usuario</b></legend>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="cpf" id='cpf' class='inputUser' required>
                    <label class="labelInput">CPF</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="nome" id='nome' class='inputUser' required>
                    <label class="labelInput">Nome</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="email" id='email' class='inputUser' required>
                    <label class="labelInput">Email</label>
                </div>
                <br><br>
                <p>Tipo:</p>
                <input id="tipo" type="radio" name="tipo" value='0' required>
                <label>Usuario</label>
                <input id="tipo" type="radio" name="tipo" value='1' required>
                <label>Admin</label>
                <br><br>
                <p>Área:</p>
                <select name="area" class="select" required>
                    <?php foreach ($areas as $area): ?>
                        <option value="<?php echo $area['id']; ?>"><?php echo $area['nome']; ?></option>
                    <?php endforeach; ?>
                </select>
                <br><br>
                <button type='submit' class='submit-c' name="submit" id='submit'>Cadastrar</button>
            </fieldset>
        </form>
    </div>
</body>
</html>
