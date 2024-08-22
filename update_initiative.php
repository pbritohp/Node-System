<?php
include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $done = $_POST['done'];

    $updateQuery = "UPDATE Ini SET done = ? WHERE id = ?";
    $stmt = mysqli_prepare($link, $updateQuery);
    mysqli_stmt_bind_param($stmt, 'ii', $done, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo 'Success';
    } else {
        echo 'Error: ' . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);
}


?>
