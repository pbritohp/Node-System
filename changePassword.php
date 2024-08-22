<?php
//include('loginCheck.php');
include('DBconnect.php');

$senhas_coincidem = true;

if (isset($_GET['tempKey'])) {
    $tempKey = $_GET['tempKey'];

    $query = mysqli_prepare($link, "SELECT * FROM Usuario WHERE tempKey = ?");
    mysqli_stmt_bind_param($query, "s", $tempKey);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);

    if (mysqli_num_rows($result) === 1) {
        $user_data = mysqli_fetch_assoc($result);
        $cpf = $user_data['cpf'];
    

        if (isset($_POST['update'])) {
            $confirmar_senha = isset($_POST['confirmar_senha']) ? $_POST['confirmar_senha'] : '';
            $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
        
            if ($senha !== $confirmar_senha) {
                $senhas_coincidem = false;
            } else {
                $senhahash = password_hash($senha, PASSWORD_DEFAULT);
        
                $updateQuery = mysqli_prepare($link, "UPDATE usuario SET senha_hash=? WHERE cpf=?");
                mysqli_stmt_bind_param($updateQuery, "ss", $senhahash, $cpf);
                $updateResult = mysqli_stmt_execute($updateQuery);
        
                if ($updateResult) {
                    $updatetempKeyQuery = mysqli_prepare($link, "UPDATE usuario SET tempKey = NULL WHERE cpf = ?");
                    mysqli_stmt_bind_param($updatetempKeyQuery, "s", $cpf);
                    $updatetempKeyResult = mysqli_stmt_execute($updatetempKeyQuery);
        
                    if ($updatetempKeyResult) {
                        echo "<script>alert('DONE - tempKey and sit_usuario_id updated')</script>";
                    } else {
                        echo "<script>alert('ERROR - Failed to update tempKey and sit_usuario_id')</script>";
                        echo "Error: " . mysqli_error($link);
                    }
                    mysqli_stmt_close($updatetempKeyQuery);
                } else {
                    echo "<script>alert('ERROR - Update query failed')</script>";
                    echo "Error: " . mysqli_error($link);
                }
                mysqli_stmt_close($updateQuery);

            }
        }
    } else {
        echo "Convite inválido!";
        exit;
    }
} else {
    echo "Página não acessível diretamente!";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Laboratório| SC2C</title>

    <script>
        function confirmAndSubmit() {
            var confirmed = confirm("Salvar o pefil do Laboratório?");
            if (confirmed) {
                var confirmInput = document.createElement("input");
                confirmInput.type = "hidden";
                confirmInput.name = "confirm";
                confirmInput.value = "true";
                document.getElementById("updateForm").appendChild(confirmInput);
                return true;
            } else {
                return false;
            }
        }
    </script>

</head>


<style>

    body{
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(90deg,cyan, white);
        }

    .box{
        color: white;
        margin-top: 2%;
        margin-left:30%;
        margin-bottom: 2%;
        width:500px;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;

    }

    fieldset{
        border: 3px solid dodgerblue;
    }

    legend{
        border: 1px solid dodgerblue;
        padding: 10px;
        background-color: dodgerblue;
        border-radius: 8px;
        font-size:20px;
    }

    .inputBox{
        position: relative;
    }

    .inputUser{
        background:none;
        border:none;
        border-bottom: 1px solid white;
        color:white;
        outline: none;
        font-size: 15px;
        width: 100%;
        letter-spacing:1px;
    }
    .labelInput{
        position: absolute;
        top:0px;
        left: 0px;
        pointer-events: none;
        transition: .5s;
    }
    .inputUser:focus ~ .labelInput,
    .inputUser:valid ~ .labelInput{
        top: -20px;
        font-size: 12px;
        color: dodgerblue;
    }

    .inputUserDesc{
        background: white;
        border: color: white;
        border-radius: 10px;
        outline: none;
        resize: none;
        font-size: 15px;
        letter-spacing:1px;
        width: 100%;
    }
    .save-submit{
        background-image: linear-gradient(to right,dodgerblue,dodgerblue);
        width: 100%;
        color:white;
        border: none;
        padding:15px;
        font-size:15px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center; 
    }
    .save-submit:hover{
        background-image: linear-gradient(to right,deepskyblue,deepskyblue);
    }

    #update{
        background-image: linear-gradient(to right,dodgerblue,dodgerblue);
        width: 100%;
        color:white;
        border: none;
        padding:15px;
        font-size:15px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center; 
    }
    #update:hover{
        background-image: linear-gradient(to right,deepskyblue,deepskyblue);
    }
</style>

<body>
    <div class="box">
    <form method='POST' id='post' onsubmit="return confirmAndSubmit();" enctype="multipart/form-data">
            <fieldset>
                <font size="5"><b>Definir Nova Senha</b></font>
                <br><br>
                <div class="inputBox"> 
                    <input type="password" name="senha" id="senha" class="inputUser" required>
                    <label class="labelInput">Nova Senha</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="password" name="confirmar_senha" id="confirmar_senha" class="inputUser" required>
                    <label class="labelInput">Confirmar Nova Senha</label>
                </div>
                <br><br>
                <button type='submit' name="update" id='update'>Salvar</button>
            </fieldset>
        </form>
    </div>
</body>

</html>
