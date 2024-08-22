<?php
include('DBconnect.php');
include('loginCheck.php');


$successSave = $errorSave = '';
$id = '';

if (isset($_POST['submit'])) {
    $nome = $_POST['modalPlanName'];
    $ex = $_POST['modalPlanEx'];

    $query = mysqli_prepare($link, "INSERT INTO Plan (nome, exercicio, dataIn) VALUES (?, ?, NOW())");
    mysqli_stmt_bind_param($query, "ss", $nome, $ex);

    if (mysqli_stmt_execute($query)) {
        $successSave = "Cadastro realizado com sucesso!";
    } else {
        $errorSave = "Erro ao cadastrar.";
    }
}

if (isset($_POST['submitEdit'])) {
    $id = $_POST['editId']; 
    $nome = $_POST['editmodalPlanName'];
    $ex = $_POST['editmodalPlanEx'];

    $query = mysqli_prepare($link, "UPDATE Plan SET nome=?, exercicio=? WHERE id=?");
    mysqli_stmt_bind_param($query, "ssi", $nome, $ex, $id);

    if (mysqli_stmt_execute($query)) {
        $successSave = "Alteração realizada com sucesso!";
    } else {
        $errorSave = "Erro ao alterar.";
    }
}

if (isset($_POST['submitDelete'])) {
    $deletePlanId = $_POST['deletePlanId'];

    $sql = "DELETE FROM Plan WHERE id = ?"; 

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $deletePlanId);

        if (mysqli_stmt_execute($stmt)) {
            $successSave = "Planejamento excluído com sucesso.";
        } else {
            $errorSave = "Erro ao excluir planejamento: " . mysqli_stmt_error($stmt);
        }
    } else {
        $errorSave = "Erro ao preparar statement: " . mysqli_error($link);
    }
}



$searchQuery = isset($_GET['search']) ? $_GET['search'] : "";

$filterClauses = [];

if (!empty($searchQuery)) {
    $filterClauses[] = "(id LIKE '%$searchQuery%' OR
                        nome LIKE '%$searchQuery%' OR
                        exercicio LIKE '%$searchQuery%')";
}

$filterClause = "";
if (!empty($filterClauses)) {
    $filterClause = "AND (" . implode(" AND ", $filterClauses) . ")";
}

$countSql = "SELECT COUNT(*) as total FROM Plan WHERE 1=1 $filterClause";
$stmt = $link->prepare($countSql);
$stmt->execute();
$countResult = $stmt->get_result();
$totalRecords = $countResult->fetch_assoc()['total'];

$recordsPerPage = 2;
$totalPages = ceil($totalRecords / $recordsPerPage);

$sql = "SELECT * FROM Plan WHERE 1=1 $filterClause ORDER BY nome DESC LIMIT ?, ?";
$stmt = $link->prepare($sql);
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $recordsPerPage;
$stmt->bind_param("ii", $offset, $recordsPerPage);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
    width: 225px;
    background-color: rgba(0, 0, 0, 0.8);
    border-radius: 15px;
    padding: 30px;
    display: flex; 
    justify-content: center; 
    align-items: center;
    margin-top: 5px;
    }
    .a_home{
        text-decoration: none;
        color: white;
        border: 3px solid dodgerblue;
        border-radius: 15px;
        padding: 10px;
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

    .titleObj{
        margin-top:5%;
        font-style:bold;
        font-size:30px;
        display: flex;
        align-items: center;
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
   <?php include('nav.php');?>

    <!--New Modal -->
    <div id="newPlanModal" class="modal" style="display:none ;">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Novo Planejamento</h2>
            <form  method="POST">
            <br><br>
                <label for="modalPlanName">Nome Planejamento:</label>
                <input class="inputUserLabel" type="text" id="modalPlanName" name="modalPlanName" required>
                <br><br>
                <label for="modalPlanEx">Exercício:</label>
                <input class="inputUserLabel" type="text" id="modalPlanEx" name="modalPlanEx" required>
                <br><br>
                <input class="inputSubmit" type="submit" name="submit" value="Salvar">
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editPlanModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" id="EditcloseModal">&times;</span>
            <h2>Editar Planejamento</h2>
            <form method="POST">
                <input type="hidden" id="editPlanId" name="editId">
                <br><br>
                <label for="editmodalPlanName">Nome Planejamento:</label>
                <input class="inputUserLabel" type="text" id="editmodalPlanName" name="editmodalPlanName" required>
                <br><br>
                <label for="editmodalPlanEx">Exercício:</label>
                <input class="inputUserLabel" type="text" id="editmodalPlanEx" name="editmodalPlanEx" required>
                <br><br>
                <input class="inputSubmit" type="submit" name="submitEdit" value="Salvar">
            </form>
        </div>
    </div>


    <!-- Delete Modal -->
    <div id="deletePlanModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" id="DeletecloseModal">&times;</span>
            <h2>Confirmar Exclusão</h2>
            <p>Você tem certeza que deseja excluir este planejamento?</p>
            <form method="POST">
                <input type="hidden" id="deletePlanId" name="deletePlanId">
                <input class="inputSubmit" type="submit" name="submitDelete" value="Excluir">
            </form>
        </div>
    </div>




    <div class="plans">      

        <div class="titleObj">
            Planejamento
            <?php if ($tipoUsuario == 'admin') { ?>
                <button class="btn btn-default dropdown-toggle" style="margin: 2px;" type="button" id="modalLink">
                    Novo
                </button>
            <?php } ?>
        </div>

        <div class="box-search">
            <input type="search" class="form-control" placeholder="Search" id="searchBD">
            <button onclick="applyFilters()" class="btn btn-primary btn-sm">Apply Filters</button>
        </div>


        <div class="table-container">
            <table class='table table-bg'>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Exercício</th>
                        <th scope="col">Fatores</th>
                        <th scope="col">Objetivos</th>
                        <th scope="col">...</th>
                    </tr>
                </thead>       
                <tbody>
                <?php if (isset($result)) : ?>
                    <?php while ($user_data = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $user_data['id']; ?></td>
                            <td><?php echo $user_data['nome']; ?></td>
                            <td><?php echo $user_data['exercicio']; ?></td>
                            <td class='actions-cell'>
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton<?php echo $user_data['id']; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-book-half' viewBox='0 0 16 16'>
                                            <path d='M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z'/>
                                        </svg>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo $user_data['id']; ?>">
                                        <li><a class="dropdown-item" href="fatEx.php?id=<?php echo $user_data['id']; ?>">Fatores Externos</a></li>
                                        <li><a class="dropdown-item" href="fatIn.php?id=<?php echo $user_data['id']; ?>">Fatores Internos</a></li>
                                        <li><a class="dropdown-item" href="swot.php?id=<?php echo $user_data['id']; ?>">Análise SWOT</a></li>
                                        <li><a class="dropdown-item" href="planStr.php?id=<?php echo $user_data['id']; ?>">Estratégia</a></li>
                                        <li><a class="dropdown-item" href="culture.php?id=<?php echo $user_data['id']; ?>">Cultura Organizacional</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td class='actions-cell'>  
                                <a title='Objetivos' class='btn btn-primary' href='goal.php?id=<?php echo $user_data['id']; ?>'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-bullseye' viewBox="0 0 16 16">
                                        <path d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16'/>
                                        <path d='M8 13A5 5 0 1 1 8 3a5 5 0 0 1 0 10m0 1A6 6 0 1 0 8 2a6 6 0 0 0 0 12'/>
                                        <path d='M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8'/>
                                        <path d='M9.5 8a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0'/>
                                    </svg>
                                </a>
                            </td>
                            <td class='actions-cell'>
                                <button title='Editar' class='btn btn-primary btn-sm' onclick='openEditModal(<?php echo $user_data["id"]; ?>, "<?php echo $user_data["nome"]; ?>", "<?php echo $user_data["exercicio"]; ?>")'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                        <path d='M12.146.854a.5.5 0 0 1 .708 0l2.292 2.292a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-4 1.5a.5.5 0 0 1-.64-.64l1.5-4a.5.5 0 0 1 .11-.168l10-10zM11.207 3L13 4.793 12.5 5.293 10.707 3.5 11.207 3zM10.5 4.207L12.293 6 4.5 13.793 2.707 12 10.5 4.207zM1 13v2h2l10-10-2-2L1 13zm.5 1h1v-1h-1v1z'/>
                                    </svg>
                                </button>
                                <button title='Excluir' class='btn btn-danger btn-sm' onclick='openDeleteModal(<?php echo $user_data["id"]; ?>)'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 16 16'>
                                        <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z'/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
        
                </tbody>
            </table>
            <ul class="pagination">
                <?php
                if ($totalPages > 1) {
                    $maxVisiblePages = 3;
                    $startPage = max($currentPage - floor($maxVisiblePages / 2), 1);
                    $endPage = min($startPage + $maxVisiblePages - 1, $totalPages);

                    if ($startPage > 1) {
                        $url = 'homePlan.php?page=1' . '&id=' . $id;
                        if (!empty($searchQuery)) {
                            $url .= '&search=' . urlencode($searchQuery);
                        }
                        echo '<li><a href="' . $url . '">&laquo;</a></li>';
                    }

                    for ($page = $startPage; $page <= $endPage; $page++) {
                        $url = 'homePlan.php?page=' . $page . '&id=' . $id;
                        if (!empty($searchQuery)) {
                            $url .= '&search=' . urlencode($searchQuery);
                        }
                        
                        $activeClass = ($page == $currentPage) ? 'active' : '';
                        echo '<li class="' . $activeClass . '"><a href="' . $url . '">' . $page . '</a></li>';
                    }
                    

                    if ($endPage < $totalPages) {
                        $url = 'homePlan.php?page=' . $totalPages;
                        if (!empty($searchQuery)) {
                            $url .= '&search=' . urlencode($searchQuery);
                        }
                        echo '<li><a href="' . $url . '">&raquo;</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>  


</body>

<script>
    var search = document.getElementById('searchBD');
    var filterCategory = document.getElementById('filterCategory');
    var filterTRL = document.getElementById('filterTRL');

    search.addEventListener("keydown", function(event) { 
        if (event.key === "Enter") {
            applyFilters();
        }
    });

    function applyFilters() {
        var url = 'homePlan.php?id=<?php echo $id; ?>';
        var searchQuery = encodeURIComponent(search.value);

        if (searchQuery) {
            url += '&search=' + searchQuery;
        }

        window.location = url;
    }

    var currentPage = <?php echo $currentPage; ?>;

    search.addEventListener("keydown", function(event) { 
        if (event.key === "Enter") {
            searchData();
        }
    });

    function searchData() {
        var url = 'homePlan.php?id=<?php echo $id; ?>';
        var searchQuery = encodeURIComponent(search.value);

        if (currentPage) {
            url += '?page=' + currentPage;
        }

        if (searchQuery) {
            url += '&search=' + searchQuery;
        }

        window.location = url;
    }
</script>

    <?php if ($successSave): ?>
        <div class="success-message"><?php echo $successSave; ?></div>
    <?php elseif ($errorSave): ?>
        <div class="error-message"><?php echo $errorSave; ?></div>
    <?php endif; ?>

    <script>
    var search = document.getElementById('searchBD');
    var filterCategory = document.getElementById('filterCategory');
    var filterTRL = document.getElementById('filterTRL');

    search.addEventListener("keydown", function(event) { 
        if (event.key === "Enter") {
            applyFilters();
        }
    });

    function applyFilters() {
        var url = 'homePlan.php?id=<?php echo $id; ?>';
        var searchQuery = encodeURIComponent(search.value);

        if (searchQuery) {
            url += '&search=' + searchQuery;
        }

        window.location = url;
    }

    var currentPage = <?php echo $currentPage; ?>;

    search.addEventListener("keydown", function(event) { 
        if (event.key === "Enter") {
            searchData();
        }
    });

    function searchData() {
        var url = 'homePlan.php?id=<?php echo $id; ?>';
        var searchQuery = encodeURIComponent(search.value);

        if (currentPage) {
            url += '?page=' + currentPage;
        }

        if (searchQuery) {
            url += '&search=' + searchQuery;
        }

        window.location = url;
    }

    var modal = document.getElementById('newPlanModal');
    var newPlanModalLink = document.getElementById('modalLink');
    var closeModal = document.getElementById('closeModal');

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
    }

    var editModal = document.getElementById('editPlanModal');
    var editModalLink = document.getElementById('editmodalLink');
    var editCloseModal = document.getElementById('EditcloseModal');

    editCloseModal.onclick = function() {
        editModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == editModal) {
            editModal.style.display = "none";
        }
    }

    function openEditModal(id, nome, exercicio) {
        document.getElementById('editPlanId').value = id;
        document.getElementById('editmodalPlanName').value = nome;
        document.getElementById('editmodalPlanEx').value = exercicio;
        document.getElementById('editPlanModal').style.display = 'block';
    }


        function openDeleteModal(id) {
        document.getElementById('deletePlanId').value = id;
        document.getElementById('deletePlanModal').style.display = "block";
    }

    var deleteModal = document.getElementById('deletePlanModal');
    var deleteCloseModal = document.getElementById('DeletecloseModal');
    var cancelDelete = document.getElementById('cancelDelete');

    deleteCloseModal.onclick = function() {
        deleteModal.style.display = "none";
    }

    cancelDelete.onclick = function() {
        deleteModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == deleteModal) {
            deleteModal.style.display = "none";
        }
    }
</script>


</body>
</html>
