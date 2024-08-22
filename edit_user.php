<?php
    include('DBconnect.php');
    include('loginCheck.php');


    if (!empty($_GET['cpf'])) {
        $cpfUser = $_GET['cpf'];
        $sqlSelect = "SELECT * FROM Usuario WHERE cpf=$cpfUser";
        $result = $link->query($sqlSelect);
        if ($result->num_rows > 0) {
            while ($user_data = mysqli_fetch_assoc($result)) {

                $nome = $user_data['nome'];
                $email = $user_data['email'];
                $adm = $user_data['adm'];
                $sit_user = $user_data['sit_user'];
                $idArea = $user_data['idArea'];
                $adm = $user_data['adm'];
                $photo_pathUser = $user_data['photo_path'];

            }
        } else {
            echo "<script>window.history.back();</script>";

            exit();
        }
    } else {
        echo "<script>window.history.back();</script>";
        exit();
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Meta</title>
        <script>
        function confirmAndSubmit() {
            var confirmed = confirm("Tem certeza que deseja salvar as alterações?");
            if (confirmed) {
                var confirmInput = document.createElement("input");
                confirmInput.type = "hidden";
                confirmInput.name = "confirm";
                confirmInput.value = "true";
                document.getElementById("updateForm").appendChild(confirmInput);
                return true; // O formulário será enviado
            } else {
                return false; // O formulário não será enviado
            }
        }
    </script>
</head>

<style>

    body{
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(90deg, gray, gray);
        }

    .box{
        color: white;
        width:500px;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 15px;
        border-radius: 15px;
        margin-top:25px;

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
    .select{
        border: none;
        padding: 8px;
        border-radius: 10px;
        outline: none;
    }
    .inputUserdescr{
        background: white;
        border: color: white;
        border-radius: 10px;
        outline: none;
        resize: none;
        font-size: 15px;
        letter-spacing:1px;
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

    .pic {
        background-color: white;
        max-width: 100%;
        height: auto;
        margin-right: 10px;
        border-radius:15px;
        width: 150px;
        padding: 10px;
    }

    .content {
    display: flex;
    align-items: center;
    justify-content: center;
}


</style>


<body>

    <?php include('nav.php')?>

    <div class='content'>
        <div class="box">
        <form method='POST' action='saveUser.php' id='updateForm' onsubmit="return confirmAndSubmit();" enctype="multipart/form-data">
            <fieldset>
                    <legend><b>Editar Usuario</b></legend>
                    <br>
                    <div class="inputBox"> 
                        <input type="text" name="nome" id='nome' class='inputUser' value="<?php echo $nome ?>" required>
                        <label for="nome" class="labelInput">Nome</label> 
                    </div>
                    <br><br>
                    <div class="inputBox"> 
                        <input type="text" name="email" id='email' class='inputUser' value="<?php echo $email; ?>" required>
                        <label for="email" class="labelInput">email:</label> 
                    </div>
                    <br>
                    <?php
                        if ($logado && $tipoUsuario != 'adm') {
                            echo "<label for='area'>Area</label>";
                            echo "<br>";
                            echo "<select name='area' id='area' class='select'>";

                            $AreaQuery = mysqli_query($link, "SELECT * FROM AreaU");
                            while ($A = mysqli_fetch_array($AreaQuery)) {
                                // Verifica se o ID da área do usuário é igual ao ID da área atual
                                $selected = ($idArea == $A['id']) ? 'selected' : '';
                                echo '<option value="' . $A['id'] . '" ' . $selected . '>' . $A['nome'] . '</option>';
                            }
                            echo "</select>";
                        }
                    ?>
                    <br><br>
                    <?php
                        if ($logado && $tipoUsuario != 'adm') {
                            echo "<label for='adm' >Admin</label>";
                            echo "<br><br>";
                            echo "<select name='adm' id='adm' class='select'>";
                            echo "<option value='1' " . ($adm == 1 ? 'selected' : '') . ">Admin</option>";
                            echo "<option value='0' " . ($adm == 0 ? 'selected' : '') . ">Usuário Comum</option>";
                            echo "</select>";
                        }
                    ?>
                    <br><br>
                    <label for='sit_user' size="5" >Situation</label>
                    <br><br>
                        <select name="sit_user" id="sit_user" class='select'>
                        <option value="active" <?php echo ($sit_user === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($sit_user === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                    <br><br>
                    <font size="5"><b>Foto de Perfil</b></font>
                    <br><br>
                    <b>Foto Atual:</b>
                    <br><br>
                    <?php
                    // Exibir a imagem e a mensagem de sucesso ou erro
                    if (!empty($photo_pathUser)) {
                        echo "<img class='pic' src='dbImages/perfilPhotos/$photo_pathUser'>";
                    } else {
                        echo "Nenhuma foto disponível.";
                    }
                    ?>
                    <br><br>
                    <b>Alterar foto:</b>
                    <br><br>
                    <input type="file" name="pic" id="pic" accept="image/*">
                    <br><br>
                    <input type="hidden" name="cpf" value="<?php echo $cpfUser?>">
                    <button type='submit' name="update" id='update'>Salvar</button>
                </fieldset>
            </form>
        </div>  
    </div> 
</body>

<script>
        function confirmAndSubmit() {
            var confirmed = confirm("Tem certeza que deseja salvar as alterações?");
            if (confirmed) {
                var confirmInput = document.createElement("input");
                confirmInput.type = "hidden";
                confirmInput.name = "confirm";
                confirmInput.value = true;
                document.querySelector("form").appendChild(confirmInput);
                return true;
            } else {
                return false;
            }
        }
    </script>



</html>