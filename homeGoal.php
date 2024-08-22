<?php
include('DBconnect.php');
include('loginCheck.php');

$successSave = $errorSave = '';
$id = '';

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

$recordsPerPage = 8;
$totalPages = ceil($totalRecords / $recordsPerPage);

// Seleciona o ultimo planejamento modificado.
$sql = "SELECT * FROM Plan ORDER BY dataUp DESC LIMIT 1";
$stlast = $link->prepare($sql);
$stlast->execute();
$resultLast = $stlast->get_result();

// Seleciona todos os planejamentos.
$sql = "SELECT * FROM Plan WHERE 1=1 $filterClause ORDER BY dataIn DESC LIMIT ?, ?";
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

    .box-plans{
        padding: 30px;
        position: absolute;
        left:50%;
        top:50%;
        margin-left:-300px;
        margin-top:-300px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
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
        font-size: 20px;
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
        font-size: 20px;      
    }
    .page-title{
        color: white;
        margin-left: 2%;
    }
    .table-title{
        color: white;
    }

    .plans, .lastPlan{
        margin-left: 2px;
        width: 60%;
        font-size: 15px;      
        font-weight: italic;
    }
</style>

<?php include('nav.php') ?>
<body>
    <h1 class = page-title>Objetivos</h1>
    <div class = "box-plans">
        <div class="lastPlan"> 
            <h2 class = table-title>Último Planejamento</h2>
            <div class="table-container">
                <table class='table table-bg'>
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Exercício</th>
                        </tr>
                    </thead>       
                    <tbody>
                        <?php if (isset($resultLast)) : ?>      
                            <?php while ($user_data = $resultLast->fetch_assoc()) : ?>
                                <tr>
                                    <td><?php echo $user_data['id']; ?></td>
                                    <td><?php echo $user_data['nome']; ?></td>
                                    <td><?php echo $user_data['exercicio']; ?></td>
                                    <td class='actions-cell'>  
                                        <a title='Objetivos' class='btn btn-primary btn-sm' href='goal.php?id=<?php echo $user_data['id']; ?>'>
                                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-book-half' viewBox='0 0 16 16'>
                                                <path d='M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z'/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="plans">    
            <h2 class = table-title>Planejamentos</h2>
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
                                    <a title='Objetivos' class='btn btn-primary btn-sm' href='goal.php?id=<?php echo $user_data['id']; ?>'>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-book-half' viewBox='0 0 16 16'>
                                        <path d='M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z'/>
                                        </svg>
                                    </a>
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
                            $url = 'homeGoal.php?page=1' . '&id=' . $id;
                            if (!empty($searchQuery)) {
                                $url .= '&search=' . urlencode($searchQuery);
                            }
                            echo '<li><a href="' . $url . '">&laquo;</a></li>';
                        }

                        for ($page = $startPage; $page <= $endPage; $page++) {
                            $url = 'homeGoal.php?page=' . $page . '&id=' . $id;
                            if (!empty($searchQuery)) {
                                $url .= '&search=' . urlencode($searchQuery);
                            }
                            
                            $activeClass = ($page == $currentPage) ? 'active' : '';
                            echo '<li class="' . $activeClass . '"><a href="' . $url . '">' . $page . '</a></li>';
                        }
                        

                        if ($endPage < $totalPages) {
                            $url = 'homeGoal.php?page=' . $totalPages;
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
        var url = 'homeGoal.php?id=<?php echo $id; ?>';
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
        var url = 'homeGoal.php?id=<?php echo $id; ?>';
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
        var closeModal = document.getElementById('closeModal');

        closeModal.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }

            form.addEventListener('submit', function(event) {
            var nome = document.getElementById('modalPlanName').value;
            var ex = document.getElementById('modalPlanEx').value;

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
