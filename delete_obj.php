<?php
include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idObj = $_POST['id'];
    $query = mysqli_prepare($link, "DELETE FROM Obj WHERE id = ?");
    mysqli_stmt_bind_param($query, "i", $idObj);
    if (mysqli_stmt_execute($query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao deletar objetivo.']);
    }
    mysqli_stmt_close($query);
}
?>
