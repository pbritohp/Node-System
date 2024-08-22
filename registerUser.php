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
        $nome = $user_data['nome'];
        $email = $user_data['email'];
        $adm = $user_data['adm'];
        $idArea = $user_data['idArea'];

    
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
    <title>Senha novo usuario| Metadata</title>

    <script>
        function confirmAndSubmit() {
            var confirmed = confirm("Salvar senha novo?");
            if (confirmed) {
                var confirmInput = document.createElement("input");
                confirmInput.type = "hidden";
                confirmInput.name = "confirm";
                confirmInput.value = true;
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
        background-image: linear-gradient(90deg,gray, gray);
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
</style>

<body>
    <div class="box">
    <form method='POST' action='saveUser.php' id='updateForm' onsubmit="return confirmAndSubmit();" enctype="multipart/form-data">
            <fieldset>
                <legend><b>Cadastrar User</b></legend>
                <br><br>

                <font size="5"><b>Definir a senha</b></font>
                <br><br>
                <div class="inputBox"> 
                    <input type="password" name="senha" id="senha" class="inputUser" required>
                    <label class="labelInput">Senha</label> 
                </div>
                <br><br>
                <div class="inputBox"> 
                    <input type="password" name="confirmar_senha" id="confirmar_senha" class="inputUser" required>
                    <label class="labelInput">Confirmar Senha</label> 
                </div>
                <br>
                <font size="5"><b>Adicione a sua foto</b></font> (Pode ser feito depois...)
                <br><br>
                <input type="file" name="pic" id="pic" accept="image/*">
                <br><br>
                <input  type="hidden" name="cpf" value="<?php echo $cpf?>">
                <input type="hidden" name="sit_user" value="inactive">
                <input type="hidden" name="tempKey" value="<?php echo $tempKey; ?>">
                <input type="hidden" name="area" value="<?php echo $idArea; ?>">
                <input type="hidden" name="nome" value="<?php echo $nome; ?>">
                <input type="hidden" name="email" value="<?php echo $email; ?>">
                <input type="hidden" name="adm" value="<?php echo $adm?>">
                <button class= 'save-submit' type='submit' name="update" id='update'>Salvar</button>
            </fieldset>
        </form>
    </div>
</body>
</html>
