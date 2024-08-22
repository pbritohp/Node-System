<?php
include('DBconnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update']) && isset($_POST['confirm']) && $_POST['confirm'] === 'true') {
    $cpf = isset($_POST['cpf']) ? $_POST['cpf'] : '';
    $nome = isset($_POST['nome']) ? $_POST['nome'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $area = isset($_POST['area']) ? $_POST['area'] : null;
    $sit_user = isset($_POST['sit_user']) ? $_POST['sit_user'] : '';
    $adm = isset($_POST['adm']) ? $_POST['adm'] : '';
    $photo_path = '';
    
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    $confirmar_senha = isset($_POST['confirmar_senha']) ? $_POST['confirmar_senha'] : '';

    // Verifica se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        echo "<script>alert('As senhas não coincidem. Por favor, tente novamente.'); window.history.back();</script>";
        exit;
    }

    // Hash da senha
    $senhahash = '';

    // Se a senha foi fornecida, atualiza-a
    if (!empty($senha)) {
        $senhahash = password_hash($senha, PASSWORD_DEFAULT);
    }

    // Atualiza as demais informações no banco de dados
    $sqlUpdateInfo = "UPDATE Usuario SET nome=?, email=?, idArea=?, sit_user=?, adm=? WHERE cpf=?";
    $updateInfoQuery = mysqli_prepare($link, $sqlUpdateInfo);
    mysqli_stmt_bind_param($updateInfoQuery, "ssisis", $nome, $email, $area, $sit_user, $adm, $cpf);
    $resultInfo = mysqli_stmt_execute($updateInfoQuery);

    // Verifica se a atualização das informações foi bem-sucedida
    if ($resultInfo === FALSE) {
        echo "Erro ao atualizar as informações: " . $link->error;
        exit;
    }

    // Se a senha foi fornecida, atualiza-a
    if (!empty($senha)) {
        $sqlUpdateSenha = "UPDATE Usuario SET senha_hash=? WHERE cpf=?";
        $updateSenhaQuery = mysqli_prepare($link, $sqlUpdateSenha);
        mysqli_stmt_bind_param($updateSenhaQuery, "ss", $senhahash, $cpf);
        $resultSenha = mysqli_stmt_execute($updateSenhaQuery);

        if ($resultSenha === FALSE) {
            echo "Erro ao atualizar a senha: " . $link->error;
            exit;
        }
    }

    // Upload de imagens
    if (!empty($_FILES['pic']['name']) && $_FILES['pic']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['pic']['name'];
        $picTmpName = $_FILES['pic']['tmp_name'];
        $targetDirectory = 'dbImages/perfilPhotos/';
        $targetPath = $targetDirectory . basename($fileName);

        // Valida tamanho e tipo
        $allowedFileTypes = array('jpg', 'jpeg', 'png', 'gif');
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        $fileExtension = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedFileTypes) && $_FILES['pic']['size'] <= $maxFileSize) {
            if (move_uploaded_file($picTmpName, $targetPath)) {
                // Atualiza o caminho da imagem no banco de dados
                $updateLogoQuery = mysqli_prepare($link, "UPDATE Usuario SET photo_path = ? WHERE cpf = ?");
                mysqli_stmt_bind_param($updateLogoQuery, "ss", $fileName, $cpf);
                if (mysqli_stmt_execute($updateLogoQuery)) {
                    echo "Upload de imagem bem-sucedido e caminho atualizado no banco de dados.";
                } else {
                    echo "Erro ao atualizar o caminho da imagem no banco de dados.";
                }
            } else {
                echo "Erro ao mover o arquivo para o diretório de destino.";
            }
        } else {
            echo "O arquivo enviado não é do tipo permitido ou excede o tamanho máximo permitido.";
        }
    }

    // Atualiza tempKey e sit_user se a senha foi atualizada
    if (!empty($senha) && $resultSenha === TRUE) {
        $updatetempKeyQuery = "UPDATE Usuario SET tempKey = NULL, sit_user = 'active' WHERE cpf = '$cpf'";
        $updatetempKeyResult = $link->query($updatetempKeyQuery);

        if ($updatetempKeyResult === TRUE) {
            echo "Informações atualizadas com sucesso!";
        } else {
            echo "Erro ao atualizar a tempKey e situação do usuário: " . $link->error;
        }
    } else {
        //echo "Informações atualizadas com sucesso!";
    }

    mysqli_stmt_close($updateInfoQuery);
    //echo "<script>window.history.go(-2);</script>";
    echo "$cpf";
    exit();

} else {
    echo "Erro ao processar o formulário.";
}
?>
