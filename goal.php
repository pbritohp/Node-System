<?php
include('dbconnect.php');
include('loginCheck.php');

//Verifica se é lider e de que area
function isAreaLeader($userId, $areaId, $link) {
    $query = mysqli_prepare($link, "SELECT 1 FROM AreaU WHERE id = ? AND lider = ?");
    mysqli_stmt_bind_param($query, "is", $areaId, $userId);
    mysqli_stmt_execute($query);
    mysqli_stmt_store_result($query);
    $result = mysqli_stmt_num_rows($query);
    mysqli_stmt_close($query);
    return $result > 0;
}


// Função para validar a data no formato YYYY-MM-DD
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// Filtros
$filterArea = isset($_GET['area']) ? $_GET['area'] : '';

// Fetch Objs
$id = $_GET['id'];
$objectivesQuery = "SELECT * FROM Obj WHERE idPlan = $id";
if ($filterArea) {
    $objectivesQuery .= " AND idArea = '$filterArea'";
}
$objectivesResult = mysqli_query($link, $objectivesQuery);


// Fetch Areas
$areasQuery = "SELECT * FROM AreaU";
$areasResult = mysqli_query($link, $areasQuery);


// Função de criação de Indicador.
if ($tipoUsuario == 'admin' && isset($_POST['submitInd'])) {
    $nome = $_POST['newIndName'];
    $meta = $_POST['newMeta'];
    $idType = $_POST['newIndType'];
    $idResp = $_POST['newResp'];
    $idObj = $_POST['idObj'];
    $deadline = $_POST['newIndDead'];
    $pk = isset($_POST['newIndChave']) ? 1 : 0;

    if (validateDate($deadline)) {
        $query = mysqli_prepare($link, "INSERT INTO Ind (nome, meta, idType, idResp, idObj, deadline, pk, dataIn) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        mysqli_stmt_bind_param($query, "ssisisi", $nome, $meta, $idType, $idResp, $idObj, $deadline, $pk);
        if (mysqli_stmt_execute($query)) {
            $successSave = "Cadastro realizado com sucesso!";
        } else {
            $errorSave = "Erro ao cadastrar.";
        }
    } else {
        $errorSave = "Data inválida.";
    }
}

// Função de criação de Iniciativa.
if ($tipoUsuario == 'admin' && isset($_POST['submitIni'])) {
    $nome = $_POST['newIniName'];
    $comment = $_POST['modalDescr'] ?? ''; // Garantir que não seja null
    $idInd = $_POST['idInd'];
    $deadline = $_POST['newIniDead'];
    $done = 0;

    if (validateDate($deadline)) {
        $query = mysqli_prepare($link, "INSERT INTO Ini (nome, idInd, Descr, deadline, dataIn, done) VALUES (?, ?, ?, ?, NOW(), ?)");
        mysqli_stmt_bind_param($query, "sissi", $nome, $idInd, $comment, $deadline, $done);
        if (mysqli_stmt_execute($query)) {
            $successSave = "Cadastro realizado com sucesso!";
        } else {
            $errorSave = "Erro ao cadastrar.";
        }
    } else {
        $errorSave = "Data inválida.";
    }
}

// Função de criação de Objetivo.
if ($tipoUsuario == 'admin' && isset($_POST['submitObj'])) {
    $nome = $_POST['newObjName'];
    $idArea = $_POST['newObjArea'];
    $deadline = $_POST['newObjDeadline'];
    $id = $_GET['id'];

    if (validateDate($deadline)) {
        $query = mysqli_prepare($link, "INSERT INTO Obj (Nome, idArea, deadline, idPlan) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($query, "sisi", $nome, $idArea, $deadline, $id);
        if (mysqli_stmt_execute($query)) {
            $successSave = "Cadastro realizado com sucesso!";
        } else {
            $errorSave = "Erro ao cadastrar.";
        }
    } else {
        $errorSave = "Data inválida.";
    }
}

// Função de criação de Área.
if ($tipoUsuario == 'admin' && isset($_POST['submitArea'])) {
    $nome = $_POST['newAreaName'];
    $lider = $_POST['newAreaLider'];

    $query = mysqli_prepare($link, "INSERT INTO AreaU (nome, lider) VALUES (?, ?)");
    mysqli_stmt_bind_param($query, "ss", $nome, $lider);
    if (mysqli_stmt_execute($query)) {
        $successSave = "Cadastro de área realizado com sucesso!";
    } else {
        $errorSave = "Erro ao cadastrar área.";
    }
}

// Função de edição de Objetivo.
if ($tipoUsuario == 'admin' && isset($_POST['submitEditObj'])) {
    $idObj = $_POST['editObjId'];
    $nome = $_POST['editObjName'];
    $idArea = $_POST['editObjArea'];
    $deadline = $_POST['editObjDeadline'];

    if (validateDate($deadline)) {
        $query = mysqli_prepare($link, "UPDATE Obj SET Nome = ?, idArea = ?, deadline = ? WHERE id = ?");
        mysqli_stmt_bind_param($query, "sisi", $nome, $idArea, $deadline, $idObj);
        if (mysqli_stmt_execute($query)) {
            $successSave = "Edição realizada com sucesso!";
        } else {
            $errorSave = "Erro ao editar.";
        }
    } else {
        $errorSave = "Data inválida.";
    }
}

// Função de edição de Indicador.
if ($tipoUsuario == 'admin' && isset($_POST['submitEditInd'])) {
    $idInd = $_POST['editIndId'];
    $nome = $_POST['editIndName'];
    $meta = $_POST['editMeta'];
    $idType = $_POST['editIndType'];
    $idResp = $_POST['editResp'];
    $deadline = $_POST['editIndDead'];
    $pk = isset($_POST['editIndChave']) ? 1 : 0;

    if (validateDate($deadline)) {
        $query = mysqli_prepare($link, "UPDATE Ind SET nome = ?, meta = ?, idType = ?, idResp = ?, deadline = ?, pk = ? WHERE id = ?");
        mysqli_stmt_bind_param($query, "ssisssi", $nome, $meta, $idType, $idResp, $deadline, $pk, $idInd);
        if (mysqli_stmt_execute($query)) {
            $successSave = "Edição realizada com sucesso!";
        } else {
            $errorSave = "Erro ao editar.";
        }
    } else {
        $errorSave = "Data inválida.";
    }
}

// Função de edição de Iniciativa.
if ($tipoUsuario == 'admin' && isset($_POST['submitEditIni'])) {
    $idIni = $_POST['editIniId'];
    $nome = $_POST['editIniName'];
    $comment = $_POST['editDescr'] ?? ''; // Garantir que não seja null
    $deadline = $_POST['editIniDead'];

    if (validateDate($deadline)) {
        $query = mysqli_prepare($link, "UPDATE Ini SET nome = ?, Descr = ?, deadline = ? WHERE id = ?");
        mysqli_stmt_bind_param($query, "sssi", $nome, $comment, $deadline, $idIni);
        if (mysqli_stmt_execute($query)) {
            $successSave = "Edição realizada com sucesso!";
        } else {
            $errorSave = "Erro ao editar.";
        }
    } else {
        $errorSave = "Data inválida.";
    }
}

// Função de exclusão de Objetivo.
if ($tipoUsuario == 'admin' && isset($_POST['deleteObj'])) {
    $idObj = $_POST['deleteObjId'];

    $query = mysqli_prepare($link, "DELETE FROM Obj WHERE id = ?");
    mysqli_stmt_bind_param($query, "i", $idObj);
    if (mysqli_stmt_execute($query)) {
        $successDelete = "Objetivo deletado com sucesso!";
    } else {
        $errorDelete = "Erro ao deletar objetivo.";
    }
}

// Função de exclusão de Indicador.
if ($tipoUsuario == 'admin' && isset($_POST['deleteInd'])) {
    $idInd = $_POST['deleteIndId'];

    $query = mysqli_prepare($link, "DELETE FROM Ind WHERE id = ?");
    mysqli_stmt_bind_param($query, "i", $idInd);
    if (mysqli_stmt_execute($query)) {
        $successDelete = "Indicador deletado com sucesso!";
    } else {
        $errorDelete = "Erro ao deletar indicador.";
    }
}

// Função de exclusão de Iniciativa.
if ($tipoUsuario == 'admin' && isset($_POST['deleteIni'])) {
    $idIni = $_POST['deleteIniId'];

    $query = mysqli_prepare($link, "DELETE FROM Ini WHERE id = ?");
    mysqli_stmt_bind_param($query, "i", $idIni);
    if (mysqli_stmt_execute($query)) {
        $successDelete = "Iniciativa deletada com sucesso!";
    } else {
        $errorDelete = "Erro ao deletar iniciativa.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <title>GOAL | Node</title>
</head>

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(90deg, gray, gray);
        min-width:600px;
    }

    .sidebar {
        width: 200px;
        height: 100vh;
        background-color: #f0f0f0;
        padding: 20px;
        position: fixed;
    }
    .sidebar button {
        display: block;
        width: 100%;
        margin-bottom: 20px;
        padding: 10px;
        background-color: #e0e0e0;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .content{
        padding: 20px;
        background-color: white;
        margin:20px auto;
        width:90%;
        border-radius:20px;


    }

    .obj-box {
        margin-bottom: 20px;
        width: 100%;
        max-width: 800px; 
        margin: 20px auto;
        
    }
    .obj {
        background-color: rgba(0, 0, 0, 0.95);
        padding: 20px;
        border-radius: 10px 10px 0 0;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .obj-pct {
        font-size: 24px;
        margin-right: 10px;
    }
    .obj-title {
        flex-grow: 1;
        text-align: center;
        font-size: 18px;
    }
    .obj-area {
        background-color: white;
        border-radius: 5px;
        color: black;
        padding: 5px 10px;
        margin: 5px;
    }
    .arrow {
        color: white;
        cursor: pointer;
        transition: transform 0.3s ease-in-out;
    }
    .arrow.up {
        transform: rotate(180deg);
    }
    .expandable {
        background-color: rgba(0, 0, 0, 0.8);
        overflow: hidden;
        transition: all 0.5s ease-in-out;
        max-height: 0;
        padding: 0 1em;
        color: white;
        width: 100%;
        border-radius: 0 0 10px 10px;
    }
    .expandable.show {
        max-height: 500px;
        padding: 1em;
        overflow-y: auto;
    }

    .meta-section {
        padding: 10px;
        border-radius: 5px;
        margin-top: 10px;
    }
    .meta-section .meta-title {
        font-weight: bold;
    }
    .meta-section .meta-item {
        margin-top: 5px;
        padding: 5px;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .meta-section .initiative-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }
    .user-info {
        display: flex;
        align-items: center;
    }
    .user-info img {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    margin-right: 10px;
    }

    .checkbox {
        margin-left: 10px;
    }

    .modal-content{
        color: white;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width:30%;
        background-color: rgba(0, 0, 0, 0.9);
        padding: 15px;
        border-radius: 15px;
    }

    .modal-title{
        border-bottom: 4px solid white;
    }

    .inputUserLabel{
        background: white;
        border-radius: 10px;
        color:black;
        outline: none;
        font-size: 15px;
        letter-spacing:1px;
        padding:5px;
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
    .titleObj{
        font-style:bold;
        font-size:30px;
        display: flex;
        align-items: center;
    }
    .btnObj{
        background: transparent;
        border-radius: 100%;
        height:30px;
        width:30px;
        justify-content:center;
        display:flex;
        align-items:center;
    }

    .ind-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-default{
        height:30px;
        width:fit-content;
        margin-left:5px;
        align-items: center;
        padding:2px 5px;
    }

    .btn-drop2{
        color:white;
        background-color:transparent;
    }

    .descr-tooltip {
        position: absolute;
        top: -10px;
        left: 100%;
        margin-left: 10px;
        padding: 5px;
        background-color: #333;
        color: white;
        border-radius: 5px;
        white-space: nowrap;
        display: none;
    }
    .initiative-item{
        margin-right:5px;
    }
    .initiative-item:hover .descr-tooltip {
        display: block;
    }

    .cnav{
        display: flex;
        align-items: center;
    }

    .select{
        color:black;
        width:fit-content ;
        padding:5px;
        border-radius: 10px;
        color:black;
        outline: none;
        font-size: 15px;
        letter-spacing:1px;
    }
    .inputUserDesc{
        background: white;
        color: Black;
        border-radius: 10px;
        outline: none;
        resize: none;
        font-size: 15px;
        letter-spacing:1px;
        width: 100%;
        height: 150px;
    }

    .indicator-deadline, .initiative-deadline {
    margin-left: 10px;
    font-size: 14px;
    color: #aaa;
    }

    .modal-content{
	width:fit-content;
    }
            
    .inputUserLabel{
        width: 100%;
    }

    .modal-submit{
    width: 100%;
    }

    .select{
        width:100% ;
    }

</style>

<body>
<?php include('nav.php') ?>

    <!-- Modal de criação de Objetivo -->
    <div id="newObjModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" id="closeObjModal">&times;</span>
        <form id="newObjForm" method="POST">
            <label for="newObjName">Nome do Objetivo:</label>
            <input class="inputUserLabel" type="text" id="newObjName" name="newObjName" required>
            <br><br>
            <label for="newObjArea">Área:</label>
            <select name="newObjArea" id="newObjArea" class='select'>
                <?php
                $areas = mysqli_query($link, "SELECT * FROM AreaU");
                while ($area = mysqli_fetch_array($areas)){
                    ?>
                    <option value="<?php echo $area['id']?>"><?php echo $area['nome']?></option>
                <?php } ?>
            </select>
            <br><br>
            <label for="newObjDeadline">Prazo:</label>
            <input class="inputUserLabel" type="date" id="newObjDeadline" name="newObjDeadline" required>
            <br><br>
            <input type="hidden" id="idPlan" name="idPlan" value="<?php echo $id; ?>">
            <button type="button" id="submitNewObj" class="modal-submit">Salvar</button>
        </form>
    </div>
</div>


    <!-- Modal de criação de Indicador -->
    <?php if ($tipoUsuario == 'admin') { ?>
        <div id="newIndModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" id="closeIndModal">&times;</span>
                <form method="POST">
                    <label for="newIndName">Indicador:</label>
                    <input class="inputUserLabel" type="text" id="newIndName" name="newIndName" required>
                    <br><br>
                    <label for="newIndType">Tipo de Indicador:</label>
                    <select name="newIndType" id="newIndType" class='select'>
                        <?php
                        $inf = mysqli_query($link, "SELECT * FROM typeInd");
                        while ($i = mysqli_fetch_array($inf)){
                            ?>
                            <option value="<?php echo $i['id']?>"><?php echo $i['nome']?></option>
                        <?php } ?>
                    </select>
                    <br><br>
                    <label for="newMeta">Meta:</label>
                    <input class="inputUserLabel" type="text" id="newMeta" name="newMeta" required>
                    <br><br>
                    <label for="newIndDead">Prazo:</label>
                    <input class="inputUserLabel" type="date" id="newIndDead" name="newIndDead" required>
                    <br><br>
                    <label for="newResp">Responsável:</label>
                    <select name="newResp" id="newResp" class='select'>
                        <?php
                        $inf = mysqli_query($link, "SELECT * FROM Usuario");
                        while ($i = mysqli_fetch_array($inf)){
                            ?>
                            <option value="<?php echo $i['cpf']?>"><?php echo $i['nome']?></option>
                        <?php } ?>
                    </select>
                    <br><br>
                    <label for="newIndChave">É indicador chave?:</label>
                    <input type="checkbox" id="newIndChave" name="newIndChave">
                    <br><br>
                    <input type="hidden" id="idObj" name="idObj" value="">
                    <input class="modal-submit" type="submit" name="submitInd" value="Salvar">
                </form>
            </div>
        </div>
    <?php } ?>

    <!-- Modal de criação de Iniciativa -->
    <?php if ($tipoUsuario == 'admin') { ?>
        <div id="newIniModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" id="closeIniModal">&times;</span>
                <form method="POST">
                    <label for="newIniName">Iniciativa:</label>
                    <input class="inputUserLabel" type="text" id="newIniName" name="newIniName" required>
                    <br><br>
                    <label for="newIniDead">Prazo:</label>
                    <input class="inputUserLabel" type="date" id="newIniDead" name="newIniDead" required>
                    <br><br>
                    <label for="modalDescr">Comentário:</label>
                    <br>
                    <textarea class="inputUserDesc" id="modalDescr" name="modalDescr"></textarea>
                    <br><br>
                    <input type="hidden" id="idInd" name="idInd" value="">
                    <input class="modal-submit" type="submit" name="submitIni" value="Salvar">
                </form>
            </div>
        </div>
    <?php } ?>

    <!-- Modal de criação de Área -->
    <?php if ($tipoUsuario == 'admin') { ?>
        <div id="newAreaModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" id="closeAreaModal">&times;</span>
                <form method="POST">
                    <label for="newAreaName">Nome da Área:</label>
                    <input class="inputUserLabel" type="text" id="newAreaName" name="newAreaName" required>
                    <br><br>
                    <label for="newAreaLider">Líder:</label>
                    <select name="newAreaLider" id="newAreaLider" class='select'>
                        <?php
                        $usuarios = mysqli_query($link, "SELECT * FROM Usuario");
                        while ($usuario = mysqli_fetch_array($usuarios)){
                            ?>
                            <option value="<?php echo $usuario['cpf']?>"><?php echo $usuario['nome']?></option>
                        <?php } ?>
                    </select>
                    <br><br>
                    <input class="modal-submit" type="submit" name="submitArea" value="Salvar">
                </form>
            </div>
        </div>
    <?php } ?>

    <!-- Modal de edição de Objetivo -->
    <?php if ($tipoUsuario == 'admin') { ?>
        <div id="editObjModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" id="closeEditObjModal">&times;</span>
                <form method="POST">
                    <label for="editObjName">Nome do Objetivo:</label>
                    <input class="inputUserLabel" type="text" id="editObjName" name="editObjName" required>
                    <br><br>
                    <label for="editObjArea">Área:</label>
                    <select name="editObjArea" id="editObjArea" class='select'>
                        <?php
                        $areas = mysqli_query($link, "SELECT * FROM AreaU");
                        while ($area = mysqli_fetch_array($areas)){
                            ?>
                            <option value="<?php echo $area['id']?>"><?php echo $area['nome']?></option>
                        <?php } ?>
                    </select>
                    <br><br>
                    <label for="editObjDeadline">Prazo:</label>
                    <input class="inputUserLabel" type="date" id="editObjDeadline" name="editObjDeadline" required>
                    <br><br>
                    <input type="hidden" id="editObjId" name="editObjId" value="">
                    <input class="modal-submit" type="submit" name="submitEditObj" value="Salvar">
                </form>
            </div>
        </div>
    <?php } ?>

    <!-- Modal de edição de Indicador -->
    <?php if ($tipoUsuario == 'admin') { ?>
        <div id="editIndModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" id="closeEditIndModal">&times;</span>
                <form method="POST">
                    <label for="editIndName">Indicador:</label>
                    <input class="inputUserLabel" type="text" id="editIndName" name="editIndName" required>
                    <br><br>
                    <label for="editIndType">Tipo de Indicador:</label>
                    <select name="editIndType" id="editIndType" class='select'>
                        <?php
                        $inf = mysqli_query($link, "SELECT * FROM typeInd");
                        while ($i = mysqli_fetch_array($inf)){
                            ?>
                            <option value="<?php echo $i['id']?>"><?php echo $i['nome']?></option>
                        <?php } ?>
                    </select>
                    <br><br>
                    <label for="editMeta">Meta:</label>
                    <input class="inputUserLabel" type="text" id="editMeta" name="editMeta" required>
                    <br><br>
                    <label for="editIndDead">Prazo:</label>
                    <input class="inputUserLabel" type="date" id="editIndDead" name="editIndDead" required>
                    <br><br>
                    <label for="editResp">Responsável:</label>
                    <select name="editResp" id="editResp" class='select'>
                        <?php
                        $inf = mysqli_query($link, "SELECT * FROM Usuario");
                        while ($i = mysqli_fetch_array($inf)){
                            ?>
                            <option value="<?php echo $i['cpf']?>"><?php echo $i['nome']?></option>
                        <?php } ?>
                    </select>
                    <br><br>
                    <label for="editIndChave">É indicador chave?:</label>
                    <input type="checkbox" id="editIndChave" name="editIndChave">
                    <br><br>
                    <input type="hidden" id="editIndId" name="editIndId" value="">
                    <input class="modal-submit" type="submit" name="submitEditInd" value="Salvar">
                </form>
            </div>
        </div>
    <?php } ?>

    <!-- Modal de edição de Iniciativa -->
    <?php if ($tipoUsuario == 'admin') { ?>
        <div id="editIniModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" id="closeEditIniModal">&times;</span>
                <form method="POST">
                    <label for="editIniName">Iniciativa:</label>
                    <input class="inputUserLabel" type="text" id="editIniName" name="editIniName" required>
                    <br><br>
                    <label for="editIniDead">Prazo:</label>
                    <input class="inputUserLabel" type="date" id="editIniDead" name="editIniDead" required>
                    <br><br>
                    <label for="editDescr">Comentário:</label>
                    <br>
                    <textarea class="inputUserDesc" id="editDescr" name="editDescr"></textarea>
                    <br><br>
                    <input type="hidden" id="editIniId" name="editIniId" value="">
                    <input class="modal-submit" type="submit" name="submitEditIni" value="Salvar">
                </form>
            </div>
        </div>
    <?php } ?>

    <!-- Modal de exclusão de Objetivo -->
    <?php if ($tipoUsuario == 'admin') { ?>
        <div id="deleteObjModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" id="closeDeleteObjModal">&times;</span>
                <form method="POST">
                    <p>Tem certeza de que deseja excluir este objetivo?</p>
                    <input type="hidden" id="deleteObjId" name="deleteObjId" value="">
                    <input class="modal-submit" type="submit" name="deleteObj" value="Deletar">
                </form>
            </div>
        </div>
    <?php } ?>

    <!-- Modal de exclusão de Indicador -->
    <?php if ($tipoUsuario == 'admin') { ?>
        <div id="deleteIndModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" id="closeDeleteIndModal">&times;</span>
                <form method="POST">
                    <p>Tem certeza de que deseja excluir este indicador?</p>
                    <input type="hidden" id="deleteIndId" name="deleteIndId" value="">
                    <input class="modal-submit" type="submit" name="deleteInd" value="Deletar">
                </form>
            </div>
        </div>
    <?php } ?>

    <!-- Modal de exclusão de Iniciativa -->
    <?php if ($tipoUsuario == 'admin') { ?>
        <div id="deleteIniModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" id="closeDeleteIniModal">&times;"></span>
                <form method="POST">
                    <p>Tem certeza de que deseja excluir esta iniciativa?</p>
                    <input type="hidden" id="deleteIniId" name="deleteIniId" value="">
                    <input class="modal-submit" type="submit" name="deleteIni" value="Deletar">
                </form>
            </div>
        </div>
    <?php } ?>
    
    <div class="content">
        <div class="titleObj">
            Objetivos
            <?php if ($tipoUsuario == 'admin') { ?>
                <button class="btn btn-default dropdown-toggle" style="margin: 2px;" type="button" id="addObjBtn">
                    Novo
                </button>
            <?php } ?>
        </div>
     
        <ul class="nav nav-tabs cnav">
            <li class="<?php echo !$filterArea ? 'active' : ''; ?>">
                <a href="goal.php?id=<?php echo $id; ?>">Todos</a>
            </li>
            <?php while ($area = mysqli_fetch_assoc($areasResult)) { ?>
                <li class="<?php echo $filterArea == $area['id'] ? 'active' : ''; ?>">
                    <a href="goal.php?id=<?php echo $id; ?>&area=<?php echo $area['id']; ?>"><?php echo $area['nome']; ?></a>
                </li>
            <?php } ?>
            <?php if ($tipoUsuario == 'admin') { ?>
                <button class="btn btn-default" type="button" id="addAreaBtn">Nova Area</button>
            <?php } ?>
        </ul>



        <?php
        while ($objective = mysqli_fetch_assoc($objectivesResult)) {
            $objectiveId = $objective['id'];
            $objectiveName = $objective['Nome'];
            $objectivePct = 0;
            
            // Calculate percentage of completed initiatives
            $totalInitiativesQuery = "SELECT COUNT(*) as total FROM Ini WHERE idInd IN (SELECT id FROM Ind WHERE idObj = $objectiveId)";
            $completedInitiativesQuery = "SELECT COUNT(*) as completed FROM Ini WHERE idInd IN (SELECT id FROM Ind WHERE idObj = $objectiveId) AND done = 1";

            $totalInitiativesResult = mysqli_query($link, $totalInitiativesQuery);
            $completedInitiativesResult = mysqli_query($link, $completedInitiativesQuery);

            if ($totalInitiativesResult && $completedInitiativesResult) {
                $totalInitiatives = mysqli_fetch_assoc($totalInitiativesResult)['total'];
                $completedInitiatives = mysqli_fetch_assoc($completedInitiativesResult)['completed'];
                if ($totalInitiatives > 0) {
                    $objectivePct = ($completedInitiatives / $totalInitiatives) * 100;
                }
            }

            $objectiveArea = "Area " . $objective['idArea'];
            $objectiveDeadline = date('d/m/Y', strtotime($objective['deadline']));

            $indicatorsQuery = "SELECT Ind.*, Usuario.nome AS userName, Usuario.photo_path AS userPhoto
                                FROM Ind
                                LEFT JOIN Usuario ON Ind.idResp = Usuario.cpf
                                WHERE Ind.idObj = '$objectiveId'";
            $indicatorsResult = mysqli_query($link, $indicatorsQuery);
            ?>

            <div class='obj-box'>
                <div class='obj'>
                    <div class='obj-pct'><?php echo round($objectivePct, 2) . '%'; ?></div>
                    <div class='obj-title'><?php echo $objectiveName; ?></div>
                    <div class='obj-area'><?php echo $objectiveArea; ?></div>
                    <?php if ($tipoUsuario == 'admin') { ?>
                        <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle" style="margin: 2px;" type="button" data-toggle="dropdown">
                                ...
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="#" class='btnEditObj' data-id='<?php echo $objectiveId; ?>' data-name='<?php echo $objectiveName; ?>' data-area='<?php echo $objective['idArea']; ?>' data-deadline='<?php echo $objective['deadline']; ?>'>Editar</a></li>
                                <li><a href="#" class='modalLinkInd' data-idObj='<?php echo $objectiveId; ?>'>Adicionar Indicador</a></li>
                                <li><a href="#" class='btnDeleteObj' data-id='<?php echo $objectiveId; ?>'>Deletar</a></li>
                            </ul>
                        </div>
                    <?php } ?>
                    <a class='arrow' id="click_to_slide_<?php echo $objectiveId; ?>">
                        <svg id="arrow_icon_<?php echo $objectiveId; ?>" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-caret-down" viewBox="0 0 16 16">
                            <path d="M3.204 5h9.592L8 10.481zm-.753.659 4.796 5.48a1 1 0 0 0 1.506 0l4.796-5.48c.566-.647.106-1.659-.753-1.659H3.204a1 1 0 0 0-.753 1.659"/>
                        </svg>
                    </a>
                </div>
                <div class="expandable" id="nav_<?php echo $objectiveId; ?>">
                    <div class="meta-section">
                        <?php
                        while ($indicator = mysqli_fetch_assoc($indicatorsResult)) {
                            $indicatorName = $indicator['nome'];
                            $indicatorMeta = $indicator['meta'];
                            $userName = $indicator['userName'];
                            $userPhoto = $indicator['userPhoto'];
                            ?>
                            <div class="meta-item">
                                <div class="ind-title">
                                    <div class="meta-title">
                                        <?php if ($indicator['pk']) { ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmark-star-fill" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2V15.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5M8.16 4.1a.178.178 0 0 0-.32 0l-.634 1.285a.18.18 0 0 1-.134.098l-1.42.206a.178.178 0 0 0-.098.303L6.58 6.993c.042.041.061.1.051.158L6.39 8.565a.178.178 0 0 0 .258.187l1.27-.668a.18.18 0 0 1 .165 0l1.27.668a.178.178 0 0 0 .257-.187L9.368 7.15a.18.18 0 0 1 .05-.158l1.028-1.001a.178.178 0 0 0-.098-.303l-1.42-.206a.18.18 0 0 1-.134-.098z"/>
                                            </svg>
                                        <?php } ?>
                                    </div>
                                    <?php echo $indicatorName . " - " . $indicatorMeta; ?>
                                    <div class="indicator-deadline">
                                        <?php
                                            if (!empty($indicator['deadLine'])) {
                                                echo date('d/m/Y', strtotime($indicator['deadLine']));
                                            } else {
                                                echo "Sem Prazo";
                                            }
                                        ?>
                                    </div>
                                    <?php if ($tipoUsuario == 'admin') { ?>
                                        <div class="dropdown">
                                            <button class="btn btn-default dropdown-toggle btn-drop2" type="button" data-toggle="dropdown">
                                                ...
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" class='btnEditInd' data-id='<?php echo $indicator['id']; ?>' data-name='<?php echo $indicatorName; ?>' data-meta='<?php echo $indicatorMeta; ?>' data-type='<?php echo $indicator['idType']; ?>' data-resp='<?php echo $indicator['idResp']; ?>' data-deadline='<?php echo $indicator['deadLine']; ?>' data-pk='<?php echo $indicator['pk']; ?>'>Editar</a></li>
                                                <li><a href="#" class='modalLinkIni' data-idInd='<?php echo $indicator['id']; ?>'>Adicionar Iniciativa</a></li>
                                                <li><a href="#" class='btnDeleteInd' data-id='<?php echo $indicator['id']; ?>'>Deletar</a></li>
                                            </ul>
                                        </div>
                                    <?php }
                                     ?>
                                </div>
                                <div class="user-info">
                                    <?php if ($userPhoto) { ?>
                                        <img src="dbImages/perfilPhotos/<?php echo $userPhoto; ?>" alt="<?php echo $userName; ?>" style="border-radius: 50%; width: 40px; height: 40px;">
                                    <?php } ?>
                                    <span><?php echo $userName; ?></span>
                                </div>
                            </div>

                            <?php
                            $initiativesQuery = "SELECT * FROM Ini WHERE idInd = '" . $indicator['id'] . "'";
                            $initiativesResult = mysqli_query($link, $initiativesQuery);

                            while ($initiative = mysqli_fetch_assoc($initiativesResult)) {
                                $checked = $initiative['done'] ? 'checked' : '';
                                ?>
                                <div class="meta-item">
                                    <div class="ind-title initiative-item">
                                        <?php echo $initiative['nome']; ?>
                                        <div class="initiative-deadline">
                                            <?php
                                                if (!empty($initiative['deadLine'])) {
                                                    echo date('d/m/Y', strtotime($initiative['deadLine']));
                                                } else {
                                                    echo "Sem Prazo";
                                                }
                                            ?>
                                        </div>
                                        <?php if ($tipoUsuario == 'admin') { ?>
                                            <div class="dropdown">
                                                <button class="btn btn-default dropdown-toggle btn-drop2" type="button" data-toggle="dropdown">
                                                    ...
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="#" class='btnEditIni' data-id='<?php echo $initiative['id']; ?>' data-name='<?php echo $initiative['nome']; ?>' data-deadline='<?php echo $initiative['deadLine']; ?>' data-descr='<?php echo htmlspecialchars($initiative['descr'] ?? ''); ?>'>Editar</a></li>
                                                    <li><a href="#" class='btnDeleteIni' data-id='<?php echo $initiative['id']; ?>'>Deletar</a></li>
                                                </ul>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <input type="checkbox" class="checkbox" data-initiative-id="<?php echo $initiative['id']; ?>" <?php echo $checked; ?>>
                                    <div class="descr-tooltip"><?php echo htmlspecialchars($initiative['descr'] ?? ''); ?></div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <script>
$(document).ready(function () {
    $('#click_to_slide_<?php echo $objectiveId; ?>').on('click', function (e) {
        e.preventDefault();
        $('#nav_<?php echo $objectiveId; ?>').toggleClass('show');
        $('#arrow_icon_<?php echo $objectiveId; ?>').toggleClass('up');
    });

    $('.checkbox').on('change', function () {
        var initiativeId = $(this).data('initiative-id');
        var isDone = $(this).is(':checked') ? 1 : 0;
        $.ajax({
            url: 'update_initiative.php',
            method: 'POST',
            data: {
                id: initiativeId,
                done: isDone
            },
            success: function (response) {
                var result = JSON.parse(response);
                if (result.success) {
                    console.log('Update successful');
                } else {
                    console.log('Update failed:', result.error);
                }
            },
            error: function (error) {
                console.log('Update failed:', error);
            }
        });
    });

    // Função de exclusão de Objetivo
    $('.btnDeleteObj').on('click', function () {
        var id = $(this).data('id');
        $('#deleteObjId').val(id);
        $('#deleteObjModal').show();
    });

    $('#deleteObjModal form').on('submit', function (e) {
        e.preventDefault();
        var objId = $('#deleteObjId').val();
        $.ajax({
            url: 'delete_obj.php',
            method: 'POST',
            data: {
                id: objId
            },
            success: function (response) {
                var result = JSON.parse(response);
                if (result.success) {
                    window.location.reload(); // Recarregar a página
                } else {
                    alert('Erro ao deletar objetivo.');
                }
            },
            error: function (error) {
                console.log('Deletion failed:', error);
                alert('Erro ao deletar objetivo.');
            }
        });
    });

    // Função de exclusão de Indicador
    $('.btnDeleteInd').on('click', function () {
        var id = $(this).data('id');
        $('#deleteIndId').val(id);
        $('#deleteIndModal').show();
    });

    // Função de exclusão de Iniciativa
    $('.btnDeleteIni').on('click', function () {
        var id = $(this).data('id');
        $('#deleteIniId').val(id);
        $('#deleteIniModal').show();
    });

    // Fechar os modais de exclusão ao clicar no botão de fechar
    $('#closeDeleteObjModal').on('click', function () {
        $('#deleteObjModal').hide();
    });
    $('#closeDeleteIndModal').on('click', function () {
        $('#deleteIndModal').hide();
    });
    $('#closeDeleteIniModal').on('click', function () {
        $('#deleteIniModal').hide();
    });

    // Fechar os modais de exclusão ao clicar fora deles
    $(window).on('click', function (event) {
        if (event.target == $('#deleteObjModal')[0]) {
            $('#deleteObjModal').hide();
        } else if (event.target == $('#deleteIndModal')[0]) {
            $('#deleteIndModal').hide();
        } else if (event.target == $('#deleteIniModal')[0]) {
            $('#deleteIniModal').hide();
        }
    });
});
</script>


<script>
$(document).ready(function () {
    var objModal = $('#newObjModal');

    // Abrir o modal de criação de Objetivo
    $('#addObjBtn').on('click', function () {
        objModal.show();
    });

    // Fechar o modal de criação de Objetivo
    $('#closeObjModal').on('click', function () {
        objModal.hide();
    });

    // Fechar o modal de criação de Objetivo ao clicar fora dele
    $(window).on('click', function (event) {
        if (event.target == objModal[0]) {
            objModal.hide();
        }
    });

    // Submissão do novo objetivo via AJAX
    $('#submitNewObj').on('click', function () {
        var formData = {
            newObjName: $('#newObjName').val(),
            newObjArea: $('#newObjArea').val(),
            newObjDeadline: $('#newObjDeadline').val(),
            idPlan: $('#idPlan').val()
        };

        $.ajax({
            url: 'insert_obj.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                var result = JSON.parse(response);
                if (result.success) {
                    window.location.reload(); // Recarregar a página
                } else {
                    alert('Erro: ' + result.error);
                }
            },
            error: function (error) {
                console.log('Inserção falhou:', error);
                alert('Erro ao cadastrar objetivo.');
            }
        });
    });
});
</script>


            <?php
        }
        ?>
    </div>

    <script>
$(document).ready(function () {
    var objModal = $('#newObjModal');
    var indModal = $('#newIndModal');
    var iniModal = $('#newIniModal');
    var areaModal = $('#newAreaModal');

    var editObjModal = $('#editObjModal');
    var editIndModal = $('#editIndModal');
    var editIniModal = $('#editIniModal');

    // Abrir o modal de criação de Objetivo
    $('#addObjBtn').on('click', function () {
        objModal.show();
    });

    // Abrir o modal de criação de Indicador
    $(document).on('click', '.modalLinkInd', function () {
        var objId = $(this).data('idobj');
        $('#idObj').val(objId);
        indModal.show();
    });

    // Abrir o modal de criação de Iniciativa
    $(document).on('click', '.modalLinkIni', function () {
        var indId = $(this).data('idind');
        $('#idInd').val(indId);
        iniModal.show();
    });

    // Abrir o modal de criação de Área
    $('#addAreaBtn').on('click', function () {
        areaModal.show();
    });

    // Fechar os modais ao clicar no botão de fechar
    $('#closeObjModal').on('click', function () {
        objModal.hide();
    });
    $('#closeIndModal').on('click', function () {
        indModal.hide();
    });
    $('#closeIniModal').on('click', function () {
        iniModal.hide();
    });
    $('#closeAreaModal').on('click', function () {
        areaModal.hide();
    });

    // Fechar os modais ao clicar fora deles
    $(window).on('click', function (event) {
        if (event.target == objModal[0]) {
            objModal.hide();
        } else if (event.target == indModal[0]) {
            indModal.hide();
        } else if (event.target == iniModal[0]) {
            iniModal.hide();
        } else if (event.target == areaModal[0]) {
            areaModal.hide();
        }
    });

    // Abrir o modal de edição de Objetivo
    $(document).on('click', '.btnEditObj', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var area = $(this).data('area');
        var deadline = $(this).data('deadline');

        $('#editObjId').val(id);
        $('#editObjName').val(name);
        $('#editObjArea').val(area);
        $('#editObjDeadline').val(formatDateForInput(deadline));

        editObjModal.show();
    });


    // Abrir o modal de edição de Indicador
    $(document).on('click', '.btnEditInd', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var meta = $(this).data('meta');
        var type = $(this).data('type');
        var resp = $(this).data('resp');
        var deadline = $(this).data('deadline');
        var pk = $(this).data('pk');

        $('#editIndId').val(id);
        $('#editIndName').val(name);
        $('#editMeta').val(meta);
        $('#editIndType').val(type);
        $('#editResp').val(resp);
        $('#editIndDead').val(formatDateForInput(deadline));
        $('#editIndChave').prop('checked', pk == 1);

        editIndModal.show();
    });

    // Abrir o modal de edição de Iniciativa
    $(document).on('click', '.btnEditIni', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var deadline = $(this).data('deadline');
        var descr = $(this).data('descr');

        $('#editIniId').val(id);
        $('#editIniName').val(name);
        $('#editIniDead').val(formatDateForInput(deadline));
        $('#editDescr').val(descr);

        editIniModal.show();
    });

    // Fechar os modais de edição ao clicar no botão de fechar
    $('#closeEditObjModal').on('click', function () {
        editObjModal.hide();
    });
    $('#closeEditIndModal').on('click', function () {
        editIndModal.hide();
    });
    $('#closeEditIniModal').on('click', function () {
        editIniModal.hide();
    });

    // Fechar os modais de edição ao clicar fora deles
    $(window).on('click', function (event) {
        if (event.target == editObjModal[0]) {
            editObjModal.hide();
        } else if (event.target == editIndModal[0]) {
            editIndModal.hide();
        } else if (event.target == editIniModal[0]) {
            editIniModal.hide();
        }
    });

    // Função de exclusão de Objetivo
    $('.btnDeleteObj').on('click', function () {
        var id = $(this).data('id');
        $('#deleteObjId').val(id);
        $('#deleteObjModal').show();
    });

    // Função de exclusão de Indicador
    $('.btnDeleteInd').on('click', function () {
        var id = $(this).data('id');
        $('#deleteIndId').val(id);
        $('#deleteIndModal').show();
    });

    // Função de exclusão de Iniciativa
    $('.btnDeleteIni').on('click', function () {
        var id = $(this).data('id');
        $('#deleteIniId').val(id);
        $('#deleteIniModal').show();
    });

    // Fechar os modais de exclusão ao clicar no botão de fechar
    $('#closeDeleteObjModal').on('click', function () {
        $('#deleteObjModal').hide();
    });
    $('#closeDeleteIndModal').on('click', function () {
        $('#deleteIndModal').hide();
    });
    $('#closeDeleteIniModal').on('click', function () {
        $('#deleteIniModal').hide();
    });

    // Fechar os modais de exclusão ao clicar fora deles
    $(window).on('click', function (event) {
        if (event.target == $('#deleteObjModal')[0]) {
            $('#deleteObjModal').hide();
        } else if (event.target == $('#deleteIndModal')[0]) {
            $('#deleteIndModal').hide();
        } else if (event.target == $('#deleteIniModal')[0]) {
            $('#deleteIniModal').hide();
        }
    });

    $('.checkbox').on('change', function () {
        var initiativeId = $(this).data('initiative-id');
        var isDone = $(this).is(':checked') ? 1 : 0;
        $.ajax({
            url: 'update_initiative.php',
            method: 'POST',
            data: {
                id: initiativeId,
                done: isDone
            },
            success: function (response) {
                console.log('Update successful:', response);
            },
            error: function (error) {
                console.log('Update failed:', error);
            }
        });
    });

    // Função para formatar a data no formato YYYY-MM-DD
    function formatDateForInput(date) {
        var d = new Date(date);
        var month = '' + (d.getMonth() + 1);
        var day = '' + d.getDate();
        var year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }
});

</script>

</body>
</html>