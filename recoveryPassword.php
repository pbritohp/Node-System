<?php
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'lib/vendor/autoload.php';
include('DBconnect.php');

// Define a função generateRandomKey no escopo global
function generateRandomKey($length = 12) {
    $randomBytes = random_bytes($length);
    $key = bin2hex($randomBytes);
    return $key;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];
    $sqlSelect = "SELECT * FROM Usuario WHERE email='$email'";
    $result = $link->query($sqlSelect);

    if ($result->num_rows > 0) {
        $user_data = mysqli_fetch_assoc($result);

        $cpf = $user_data['cpf'];
        $nome = $user_data['nome'];
        $email = $user_data['email'];
    } else {
        header('Location: login.php');
        exit();
    }

    $tempKey = generateRandomKey();

    $query = mysqli_prepare($link, "UPDATE Usuario SET tempKey=? WHERE cpf=?");
    mysqli_stmt_bind_param($query, "ss", $tempKey, $cpf);


    if (mysqli_stmt_execute($query)) {
        $success = "Cadastro realizado com sucesso!";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'b5ad514f1a13a1';
            $mail->Password = '4209a152752acd';

            $mail->setFrom('sc2c@sc2c.com', 'SC2C');    
            $mail->addAddress($email, $nome);
            $mail->isHTML(true);
            $mail->Subject = 'Novo cadastro';
            $mail->Body = $nome . ", segue o link para a troca de senha da plataforma Metadata <br><br>"
                . "Convite: <a href='http://localhost/vsxampp/meta/changePassword.php?tempKey=" . $tempKey . "'>Clique aqui</a>";
            $mail->AltBody = $nome . ", segue o link para a troca de senha da plataforma Metadata \n\n"
                . "Convite: http://localhost/vsxampp/meta/changePassword.php?tempKey=" . $tempKey;

            $mail->send();
            $success .= " Email enviado com sucesso!";

            header("Location: login.php");
            exit();
        } catch (Exception $e) {
            $error = "Erro ao enviar o email: " . $mail->ErrorInfo;
        }

        mysqli_stmt_close($query);
    } else {
        $error = "Erro ao cadastrar. Email já cadastrado ou aguardando cadastro.";
    }
}
?>
