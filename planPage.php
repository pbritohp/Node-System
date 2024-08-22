<?php
include('DBconnect.php');
include('loginCheck.php');

$successSave = $errorSave = '';

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $sqlSelect = "SELECT * FROM Plan WHERE id=$id";
    $result = $link->query($sqlSelect);

    if ($result->num_rows > 0) {
        while ($user_data = mysqli_fetch_assoc($result)) {
            $nomedis = $user_data['nome'];
            $exercicio = $user_data['exercicio'];
        }
    } else {
        header('Location: sistema.php');
        exit();
    }
} else {
    header('Location: sistema.php');
    exit();
}

if (isset($_POST['submit'])) {
    $id = $_GET['id'];
    $nome = $_POST['modalPlanName'];
    $ex = $_POST['modalPlanEx'];

    $query = mysqli_prepare($link, "UPDATE Plan SET nome=?, exercicio= ? WHERE id =? ");
    mysqli_stmt_bind_param($query, "ssi", $nome, $ex, $id);

    if (mysqli_stmt_execute($query)) {
        $successSave = "Alteração realizada com sucesso!";
    } else {
        $errorSave = "Erro ao alterar.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Plan | Node</title>
</head>

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(90deg, gray, gray);
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


    .box {
    width: fit-content;
    background-color: rgba(0, 0, 0, 0.8);
    border-radius: 15px;
    padding: 30px;
    display: flex; 
    justify-content: space-between;;
    align-items: center;
    margin-top: 5px;
    position: absolute;
    top:50%;
    left:50%;
    transform: translate(-50%,-50%);
    }
    .a_home{
        text-decoration: none;
        color: white;
        border: 3px solid dodgerblue;
        border-radius: 15px;
        padding: 10px;
        margin:2px;
    }
    .a_home:hover{
        background-color: dodgerblue;
        color: white;
        text-decoration: none;

    }

    .modal-title{
        border-bottom: 4px solid white;
    }

    .inputUserLabel{
        background: white;
        border: color: white;
        border-radius: 10px;
        color:black;
        outline: none;
        font-size: 15px;
        letter-spacing:1px;
    }

    .modal-submit{
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
    .modal-submit:hover{
        background-image: linear-gradient(to right,deepskyblue,deepskyblue);
    }
    .action-container{
        gap:10px;
    }

    .table-container {
        position: relative;
        margin-top: 5px;
        margin-bottom: 5%;
    }   

    .table-bg{
        color: white;
        background: rgba(0,0,0,0.6);
        padding: 30px;
        border-radius: 15px;       
        
    }

    .pagination {
        position: relative;
    }


    .box-search{
        justify-content: start;
        margin-top: 1%;
        display: flex;
        gap: .1%;
    }

    .table-title{
        color: white;
    }

    .plans{
        margin-left: 2%;
        width:80%;
    }
</style>

<body> 

<?php include('nav.php')?>


    <div id="newPlanModal" class="modal" style="display:none ;">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Editar Planejamento</h2>
            <form  method="POST">
            <br><br>
                <label for="modalPlanName">Nome Planejamento:</label>
                <input class="inputUserLabel" type="text" id="modalPlanName" name="modalPlanName" value="<?php echo $nomedis; ?>"  required>
                <br><br>
                <label for="modalPlanEx">Exercício:</label>
                <input class="inputUserLabel" type="text" id="modalPlanEx" name="modalPlanEx" value="<?php echo $exercicio; ?>"  required>
                <br><br>
                <input class="inputSubmit" type="submit" name="submit" value="Salvar">
            </form>
        </div>
    </div>

    <div class="plans">
    <h3><?php echo $nomedis; ?></h3>
        <div class="box">
            <a class="a_home" id="modalLink">Editar Planejamento</a>
            <a class="a_home" href='fatEx.php?id=<?php echo $id; ?>'>Fatores Externos</a>
            <a class="a_home" href='fatIn.php?id=<?php echo $id; ?>'>Fatores Internos</a>
            <a class="a_home" href='swot.php?id=<?php echo $id; ?>'>Análise</a>
            <a class="a_home" href='planStr.php?id=<?php echo $id; ?>'>Estratégia</a>
            <a class="a_home" href='culture.php?id=<?php echo $id; ?>'>Cultura</a>
        </div>
    </div>
        


    </body>

    <?php if ($successSave): ?>
        <div class="success-message"><?php echo $successSave; ?></div>
    <?php elseif ($errorSave): ?>
        <div class="error-message"><?php echo $errorSave; ?></div>
    <?php endif; ?>

    <script>
        var modal = document.getElementById('editPlanModal');
        var newPlanModalLink = document.getElementById('editmodalLink');
        var closeModal = document.getElementById('EditcloseModal');

        newPlanModalLink.onclick = function() {
            modal.style.display = "block";
        }

        closeModal.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }

            form.addEventListener('submitEdit', function(event) {
            var nome = document.getElementById('editmodalPlanName').value;
            var ex = document.getElementById('editmodalPlanEx').value;

            if (!nome || !ex) {
                alert('Por favor, preencha todos os campos.');
                event.preventDefault();
            } else {
                alert('Formulário enviado com sucesso!');
            }
        }
        )};
    </script>

</body>
</html>
