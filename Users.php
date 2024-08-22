<?php
include('DBconnect.php');
include('loginCheck.php');


$queryArea = "SELECT * FROM AreaU";

function getNameAreaU($idArea) {
    global $link;
    $sql = "SELECT nome FROM AreaU WHERE id = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $idArea);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['nome'];
    } else {
        return 'Área desconhecida';
    }
}


$searchQuery = isset($_GET['search']) ? $_GET['search'] : "";
$areaFilter = isset($_GET['areau']) ? $_GET['areau'] : "";
$sitUserFilter = isset($_GET['sit_user']) ? $_GET['sit_user'] : "";

$filterClauses = [];

if (!empty($searchQuery)) {
    $filterClauses[] = "(Usuario.cpf LIKE '%$searchQuery%' OR
                        Usuario.nome LIKE '%$searchQuery%' OR
                        Usuario.idArea IN (SELECT id FROM AreaU WHERE AreaU.nome LIKE '%$searchQuery%') OR
                        Usuario.sit_user LIKE '%$searchQuery%')";
}

if (!empty($areaFilter)) {
    $filterClauses[] = "idArea = '$areaFilter'";
}

if (!empty($sitUserFilter)) {
    $filterClauses[] = "sit_user = '$sitUserFilter'";
}

$filterClause = !empty($filterClauses) ? "(" . implode(" AND ", $filterClauses) . ")" : "1";

$countSql = "SELECT COUNT(*) as total FROM Usuario";

if (!empty($filterClause)) {
    $countSql .= " WHERE $filterClause";
}

$stmt = $link->prepare($countSql);
$stmt->execute();
$countResult = $stmt->get_result();
$totalRecords = $countResult ? $countResult->fetch_assoc()['total'] : 0;

$recordsPerPage = 4;
$totalPages = ceil($totalRecords / $recordsPerPage);

$sql = "SELECT Usuario.*, AreaU.nome AS nomeArea FROM Usuario LEFT JOIN AreaU ON Usuario.idArea = AreaU.id";

if (!empty($filterClause)) {
    $sql .= " WHERE $filterClause";
}


$sql .= " ORDER BY nome DESC LIMIT ?, ?";

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
    <title>Users | Node</title>


</head>

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background-image: linear-gradient(90deg,gray,gray);
    }



    .table-container {
        position: relative;
        margin-top: 75px;
        margin-bottom: 3%;
    }   

    .table-bg{
        color: white;
        background: rgba(0,0,0,0.6);
        margin-left:5%;
        width: 90%;
        padding: 30px;
        border-radius: 15px;       
        
    }

    .pagination {
        position: relative;
        margin-left: 5%;
    }

    .form-control{
        width:175px;
    }

    .box-search{
        justify-content: center;
        margin-top: 5%;
        display: flex;
        gap: .1%;
    }

    .table-title{
        margin-left:6.5%;
        color: white;
        }

    .titleObj{
        font-style:bold;
        font-size:30px;
        display: flex;
        align-items: center;
        margin-left:5%;
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
        
</style>

<body>

<?php include('nav.php');?>

    <div class="box-search">
        <input type="search" class="form-control" placeholder="Search" id="searchBD">
        <select class="form-control" id="filterArea">
            <option value="">Select Area</option>
            <?php
            $FAreaU = mysqli_query($link, "SELECT * FROM AreaU");
            while ($A = mysqli_fetch_array($FAreaU)) {
                ?>
                <option value="<?php echo $A['id'] ?>"><?php echo $A['nome'] ?></option>
            <?php } ?>
        </select>
        <select class="form-control" id="filterSituation">
            <option value="">Select Situation</option>
            <?php
            $Fsit_user = mysqli_query($link, "SELECT DISTINCT sit_user FROM Usuario");
            while ($S = mysqli_fetch_array($Fsit_user)) {
                ?>
                <option value="<?php echo $S['sit_user'] ?>"><?php echo $S['sit_user'] ?></option>
            <?php } ?>
        </select>
        <button onclick="applyFilters()" class="btn btn-primary btn-sm">Apply Filters</button>
    </div>

    <div class="table-container">
    <div class="titleObj">
            Usuarios
                <a class="btn btn-default dropdown-toggle" style="margin: 2px;" type="button" href="inviteUser.php">
                    Novo Usuario
            </a>
        </div>        
        <table class='table table-bg'>
            <thead>
                <tr>
                    <th scope="col">CPF</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Area</th>
                    <th scope="col">Situação</th>
                    <th scope="col">...</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($user_data = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $user_data['cpf'] . "</td>";
                    echo "<td>" . $user_data['nome'] . "</td>";
                    echo "<td>" . getNameAreaU($user_data['idArea']) . "</td>";
                    if ($user_data['sit_user'] == 'inactive') {
                        echo "<td>
                                Inactive
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-exclamation-circle-fill' viewBox='0 0 16 16'>
                                    <path d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z'/>
                                </svg>
                            </td>";
                    } elseif ($user_data['sit_user'] == 'active') {
                        echo "<td>
                                Active
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-check-circle-fill' viewBox='0 0 16 16'>
                                    <path d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z'/>
                                </svg>
                            </td>";
                    }
                    echo "<td>  
                            <a class='btn btn-primary btn-sm' href='edit_user.php?cpf=" . $user_data['cpf'] . "'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                                    <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z'/>
                                </svg>
                            </a>
                            <a class='btn btn-danger btn-sm' href='deleteUser.php?cpf=" . $user_data['cpf'] . "'>
                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 16 16'>
                                    <path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z'/>
                                </svg>
                            </a>
                            </td>";
                        }
                    ?>
            </tbody>
        </table>

        <ul class="pagination">
            <?php
            if ($totalPages > 1) {
                $maxVisiblePages = 3;
                $startPage = max($currentPage - floor($maxVisiblePages / 2), 1);
                $endPage = min($startPage + $maxVisiblePages - 1, $totalPages);

                if ($startPage > 1) {
                    $url = 'Users.php?page=1';
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    echo '<li><a href="' . $url . '">&laquo;</a></li>';
                }

                for ($page = $startPage; $page <= $endPage; $page++) {
                    $url = 'Users.php?page=' . $page;

                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    if (!empty($sitUserFilter)) {
                        $url .= '&sit_user=' . urlencode($sitUserFilter);
                    }
                    if (!empty($areaFilter)) {
                        $url .= '&areau=' . urlencode($areaFilter);
                    }

                    $activeClass = ($page == $currentPage) ? 'active' : '';
                    echo '<li class="' . $activeClass . '"><a href="' . $url . '">' . $page . '</a></li>';
                }

                if ($endPage < $totalPages) {
                    $url = 'Users.php?page=' . $totalPages;
                    if (!empty($searchQuery)) {
                        $url .= '&search=' . urlencode($searchQuery);
                    }
                    echo '<li><a href="' . $url . '">&raquo;</a></li>';
                }
            }
            ?>
        </ul>
    </div>

    <script>
        var search = document.getElementById('searchBD');
        var currentPage = <?php echo $currentPage; ?>;

        search.addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                searchData();
            }
        });

        function searchData() {
            var url = 'Users.php';
            var searchQuery = encodeURIComponent(search.value);

            if (currentPage) {
                url += '?page=' + currentPage;
            }

            if (searchQuery) {
                url += '&search=' + searchQuery;
            }

            window.location = url;
        }

        function applyFilters() {
            var searchQuery = encodeURIComponent(document.getElementById('searchBD').value);
            var areaFilter = document.getElementById('filterArea').value;
            var sitUserFilter = document.getElementById('filterSituation').value;

            var pageNumber = getParameterByName('page');
            if (!pageNumber) pageNumber = 1;

            var url = 'Users.php?page=' + pageNumber;

            if (searchQuery) {
                url += '&search=' + searchQuery;
            }

            if (areaFilter) {
                url += '&areau=' + areaFilter;
            }

            if (sitUserFilter) {
                url += '&sit_user=' + sitUserFilter;
            }

            window.location.href = url;
        }

        function getParameterByName(name, url = window.location.href) {
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }
    </script>

</body>

</html>