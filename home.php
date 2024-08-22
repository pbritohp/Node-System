<?php
include('DBconnect.php');
include('loginCheck.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaManager </title>
</head>

<style>
    body{
        font-family: Arial, Helvetica,sans-serif;
        background: gray;
        text-align: center;
        color: white;
    }

    .text{
        font-family: "Roboto", sans-serif;
        font-weight: 400;
        text-decoration: none;
        color: white;
        border-radius: 15px;
        padding: 10px;
        text-align: center;
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
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin-top: 5px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .a_home{
        text-decoration: none;
        color: white;
        border: 3px solid dodgerblue;
        border-radius: 15px;
        padding: 10px;
        margin: 2px;
    }
    .a_home:hover{
        background-color: dodgerblue;
        color: white;
        text-decoration: none;
    }

    .inputUserLabel{
        background: white;
        border: color: white;
        border-radius: 10px;
        color: black;
        outline: none;
        font-size: 15px;
        letter-spacing: 1px;
    }

    .modal-submit{
        background-image: linear-gradient(to right, dodgerblue, dodgerblue);
        width: 100%;
        color: white;
        border: none;
        padding: 15px;
        font-size: 15px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center;
    }
    .modal-submit:hover{
        background-image: linear-gradient(to right, deepskyblue, deepskyblue);
    }

    .plans{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        width: 100%;
    }
    .a_itens{
        padding:20px;
    }
</style>

<?php include('nav.php') ?>

<body>
    <div class="plans">
        <div class="box">
            <div class="text">
                <h1>Seja bem-vindo!</h1>
            </div>
            
        </div>
    </div>
</body>

</html>
