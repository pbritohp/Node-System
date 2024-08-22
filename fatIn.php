<?php
include('DBconnect.php');
include('loginCheck.php');
$successSave = $errorSave = '';

// Função de exclusão
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM fatIn WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $successSave = "Fator deletado com sucesso!";
    } else {
        $errorSave = "Erro ao deletar o fator.";
    }
}

// Verifica se o ID do plano é fornecido
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
        header('Location: homePlan.php');
        exit();
    }
} else {
    header('Location: homePlan.php');
    exit();
}

if (isset($_POST['submit'])) {
    $nome = $_POST['modalFatName'];
    $comment = $_POST['modalDescr'];
    $idFat = $_POST['idFat'];

    $queryCheck = mysqli_prepare($link, "SELECT id FROM typeFat WHERE id = ?");
    mysqli_stmt_bind_param($queryCheck, "i", $idFat);
    mysqli_stmt_execute($queryCheck);
    $resultCheck = mysqli_stmt_get_result($queryCheck);

    if (mysqli_num_rows($resultCheck) > 0) {
        $query = mysqli_prepare($link, "INSERT INTO fatIn (nome, idFat, Descr, idPlan) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($query, "sisi", $nome, $idFat, $comment, $id);
        if (mysqli_stmt_execute($query)) {
            $successSave = "Cadastro realizado com sucesso!";
        } else {
            $errorSave = "Erro ao cadastrar.";
        }
    } else {
        $errorSave = "Erro ao cadastrar: idFat não existe.";
    }
}

// Função de edição
if (isset($_POST['editSubmit'])) {
    $nome = $_POST['editFatName'];
    $comment = $_POST['editDescr'];
    $id = $_POST['editFactorId'];

    $query = mysqli_prepare($link, "UPDATE fatIn SET nome=?, Descr=? WHERE id=?");
    mysqli_stmt_bind_param($query, "ssi", $nome, $comment, $id);
    if (mysqli_stmt_execute($query)) {
        $successSave = "Edição realizada com sucesso!";
        header("Location: fatIn.php?id=" . $_GET['id'] . "&success=1");
        exit();
    } else {
        $errorSave = "Erro ao editar o fator.";
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>alert('Alteração realizada com sucesso!');</script>";
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
        min-width:600px;
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


    .a_home{
        height:fit-content ;
        text-decoration: none;
        color: white;
        border: 3px solid dodgerblue;
        border-radius: 15px;
        padding: 10px;
        position: relative;
        margin-top:5px;
        margin-bottom:5px;
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
        margin-top: 2%;
        width:80%;
        display:flex;
        align-content:center;
    }

    .content-box {
        height: fit-content;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        border-radius:0px 15px 15px 0px;
        padding: 30px;
        align-content: space-around;
        position: relative;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .content{
        align-content: center;
        width: calc(33% - 20px);
        min-width: 350px;
        height: 300px;
        color: white;
        background-color: black;
        margin-top: 5px;
        position: relative;
        padding: 30px;
        border-radius: 15px;
        white-space: nowrap;
        margin-right: 20px;
        margin-bottom: 20px;
    }
    .table-content {
        max-height: 200px;
        overflow-y: auto;
        }


    .inputUserDesc{
        color: black;
        background: white;
        border: color: white;
        border-radius: 10px;
        outline: none;
        resize: none;
        font-size: 15px;
        letter-spacing:1px;
        width: 100%;
        height: 150px;
    }

    .select{
            border: none;
            padding: 8px;
            border-radius: 10px;
            outline: none;
            color: black;
        }

    .close {
            font-size: 50px;
            color: red;
            border-color: white;
        }

    .close:hover {
            color: white;
        }

    .table-container {
        position: relative;
        margin-top: 5px;
        margin-bottom: 5%;
        height: 70%;
    }   

    .table-bg{
        color: white;
        background: rgba(0,0,0,0.6);
        padding: 30px;
        border-radius: 15px;       
    }

    .sidebar {
        position: relative;
        top: 0;
        width: 20%;
        background-color: rgba(0, 0, 0, 0.8);
        border-radius: 15px 0px 0px 15px;
        padding: 30px;
        box-sizing: border-box;
        min-width:150px;
    }

    .sidebar a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 10px;
        margin-bottom: 10px;
        border: 2px solid dodgerblue;
        border-radius: 10px;
        text-align: center;
    }

    .sidebar a:hover {
        background-color: dodgerblue;
        color: white;
    }

    .titleFat{
        Align-items:center;
        Color:white;
    }
</style>


<body>
    
<?php include('nav.php');?>
    <!-- Modal Criação -->
    <div id="newPlanModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Novo Fator</h2>
            <form method="POST">
                <label for="modalFatName">Fator:</label>
                <input class="inputUserLabel" type="text" id="modalFatName" name="modalFatName" required>
                <br><br>
                <label for="modalDescr">Comentário:</label>
                <br>
                <textarea class="inputUserDesc" id="modalDescr" name="modalDescr"></textarea>
                <br><br>
                <input type="hidden" id="idFat" name="idFat" value="">
                <input class="inputSubmit" type="submit" name="submit" value="Salvar">
            </form>
        </div>
    </div>
    <!-- Modal edição -->
    <div id="editFactorModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2>Fator</h2>
        <form method="POST">
            <label for="editFatName">Fator:</label>
            <input class="inputUserLabel" type="text" id="editFatName" name="editFatName" required>
            <br><br>
            <label for="editDescr">Comentário:</label>
            <br>
            <textarea class="inputUserDesc" id="editDescr" name="editDescr"></textarea>
            <br><br>
            <input type="hidden" id="editFactorId" name="editFactorId" value="">
            <input class="inputSubmit" type="submit" name="editSubmit" value="Editar">
        </form>
    </div>
</div>


    <!-- Tabela de fatores -->
    <div class="plans">

        <div class="sidebar">
        <div class="titleFat">
                    <h3><?php echo $nomedis; ?></h3>
                    <h3 class = "nomePage">Fatores Internos</h3>    
            </div>
            <a class="a_home" href='fatEx.php?id=<?php echo $id; ?>'>Fatores Externos</a>
            <a class="a_home" href='fatIn.php?id=<?php echo $id; ?>'>Fatores Internos</a>
            <a class="a_home" href='planStr.php?id=<?php echo $id; ?>'>Análise</a>
            <a class="a_home" href='swot.php?id=<?php echo $id; ?>'>Estratégia</a>
            <a class="a_home" href='culture.php?id=<?php echo $id; ?>'>Cultura</a>
            <a href="export_table_csv.php?table=fatIN&id=<?php echo $_GET['id']; ?>" class="a_home">Exportar Tabela para CSV</a>
            <a href="export_table_xml.php?table=fatIN&id=<?php echo $_GET['id']; ?>" class="a_home">Exportar Tabela para XML</a>
        </div>
        <div class="content-box">
            <?php
            $queryTipo = "SELECT * FROM typeFat WHERE id > 4 and id < 14";
            $resultTipo = mysqli_query($link, $queryTipo);

            while ($rowTipo = mysqli_fetch_assoc($resultTipo)) {
                $idTipo = $rowTipo['id'];
                $tipoNome = $rowTipo['nome'];

                echo "<div class='content'>";
                echo "<h3>$tipoNome</h3>";
                echo "<a class='modalLink' data-id='$idTipo' style='color: cyan;' href='#'>+ ADICIONAR</a>";

                echo "<div class='table-container table-content'>"; 

                echo "<table class='table table-bg'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th scope='col'>Nome</th>";
                echo "<th scope='col'>...</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                $queryFat = "SELECT * FROM fatIn WHERE idFat = $idTipo AND idPlan = $id";
                $resultFat = mysqli_query($link, $queryFat);

                if (mysqli_num_rows($resultFat) > 0) {
                    while ($factor_data = mysqli_fetch_assoc($resultFat)) {
                        echo "<tr>";
                        echo "<td>" . $factor_data['nome'] . "</td>";
                        echo "<td class='actions-cell'>";
                            echo "<a title='Edit Factor' class='btn btn-primary btn-sm view-factor-button' href='#' 
                                    data-id='" . $factor_data['id'] . "' 
                                    data-name='" . $factor_data['nome'] . "' 
                                    data-descr='" . $factor_data['Descr'] . "'>";
                                echo "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='12' fill='currentColor' class='bi bi-book-half' viewBox='0 0 16 16'>";
                                    echo "<path d='M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 
                                                        1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 
                                                        .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z'/>";
                                echo "</svg>
                            </a>";

                            echo " ";
                            echo "<a class='btn btn-danger btn-sm delete-factor-button' data-id='" . $factor_data['id'] . "'>";
                                echo "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='12' fill='currentColor' class='bi bi-file-earmark-image' viewBox='0 0 16 16'>";
                                    echo "<path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 
                                                        1-1V2a1 1 0 0 0-1-1h-11zm1 2H2V2h1.5v1zm8-1H14v1h-2.5V2zM12 4V2h1.5v2H12zM6.002 8.5 4.002 11h8L10 8l-2 2-1.998-1.5zM5 11l1.002-1.5L8 11H5z'/>";
                                echo "</svg>";
                            echo "</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Nenhum fator cadastrado.</td></tr>";
                }

                echo "</tbody>";
                echo "</table>";
                echo "</div>"; 
                echo "</div>";
            }
            ?>
        </div>
    </div>
</body>

<?php if ($successSave): ?>
    <div class="success-message"><?php echo $successSave; ?></div>
<?php elseif ($errorSave): ?>
    <div class="error-message"><?php echo $errorSave; ?></div>
<?php endif; ?>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('newPlanModal');
            var newPlanModalLinks = document.querySelectorAll('.modalLink');
            var closeModal = document.getElementById('closeModal');
            var editModal = document.getElementById('editFactorModal');
            var closeEditModal = document.getElementById('closeEditModal');

            // Abrir o modal de criação
            newPlanModalLinks.forEach(function(link) {
                link.onclick = function() {
                    var typeId = link.getAttribute('data-id');
                    document.getElementById('idFat').value = typeId;
                    modal.style.display = "block";
                }
            });

            // Fechar o modal de criação
            closeModal.onclick = function() {
                modal.style.display = "none";
            };

            // Fechar o modal ao clicar fora dele
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                } else if (event.target == editModal) {
                    editModal.style.display = "none";
                }
            };

            // Validação do formulário de criação
            var form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                var nome = document.getElementById('modalPlanName').value;
                var ex = document.getElementById('modalPlanEx').value;

                if (!nome || !ex) {
                    alert('Por favor, preencha todos os campos.');
                    event.preventDefault();
                } else {
                    alert('Formulário enviado com sucesso!');
                }
            });

            // Abrir o modal de edição
            function openEditModal(factorId, factorName, factorDescr) {
                document.getElementById('editFactorId').value = factorId;
                document.getElementById('editFatName').value = factorName;
                document.getElementById('editDescr').value = factorDescr;
                editModal.style.display = "block";
            }



            // Fechar o modal de edição
            closeEditModal.onclick = function() {
                editModal.style.display = "none";
            }

            // Evento ao clicar no botão de edição
           // Evento ao clicar no botão de edição
            var viewFactorButtons = document.querySelectorAll('.view-factor-button');
            viewFactorButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var factorId = button.getAttribute('data-id');
                    var factorName = button.getAttribute('data-name');
                    var factorDescr = button.getAttribute('data-descr');
                    openEditModal(factorId, factorName, factorDescr);
                });
            });


            // Evento ao clicar no botão de exclusão
            $('.delete-factor-button').click(function() {
                var id = $(this).data('id');
                if (confirm('Você tem certeza que deseja excluir este fator?')) {
                    window.location.href = "fatIn.php?id=<?php echo $_GET['id']; ?>&delete_id=" + id;
                }
            });

            // Confirmar ao salvar alterações
            $('#editFactorForm').submit(function(e) {
                if (!confirm('Você tem certeza que deseja salvar as alterações deste fator?')) {
                    e.preventDefault();
                }
            });
        });
    </script>

</html>


