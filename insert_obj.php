<?php
include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['newObjName'];
    $idArea = $_POST['newObjArea'];
    $deadline = $_POST['newObjDeadline'];
    $id = $_POST['idPlan'];

    if (validateDate($deadline)) {
        $query = mysqli_prepare($link, "INSERT INTO Obj (Nome, idArea, deadline, idPlan) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($query, "sisi", $nome, $idArea, $deadline, $id);
        if (mysqli_stmt_execute($query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao cadastrar objetivo.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Data inválida.']);
    }
    mysqli_stmt_close($query);
}

// Função para validar a data no formato YYYY-MM-DD
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
?>
