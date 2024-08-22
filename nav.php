<?php
include('DBconnect.php');

$photoSql = "SELECT photo_path, cpf FROM usuario WHERE email = ?";
$stmt = $link->prepare($photoSql);
$stmt->bind_param('s', $_SESSION['email']);
$stmt->execute();
$photoResult = $stmt->get_result();

if ($photoResult->num_rows > 0) {
    $row = $photoResult->fetch_assoc();
    $photo_path = $row['photo_path'];
    $cpf =$row['cpf'];
} else {
    // Caminho padrão caso o usuário não tenha uma foto
    $photo_path = 'images/fotoanonima.jpg';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
        }

        .body_nav{
            font-family: "Roboto", sans-serif;
            background-color: linear-gradient(45deg,cyan,white);
        }

        .li_nav,.a_nav, .dropbtn{
            font-family: "Roboto", sans-serif;
            font-weight: 400;
            font-weight: bold;
            font-size: 16px;
            color: black;
            width: 200;
            height: 300;
        }

        .header_nav{
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 2%;
            margin-top: 20px;
            left: 0;
            right: 0;
            background-color: white;
            height: 80px;
            min-width: 800px;

        }

        .logo{
            cursor: pointer;
            max-width: 100%;
            height: auto;
            margin-right: 10px;
            border-radius:15px;
            width: 300px;
            padding: 10px;
        }

        .nav_links{
            list-style: none;
            align-items: center;
            justify-content: left;
            display: flex;
        }

        .li_nav{
            display: inline-block;
            padding: 0px 20px;      
        }

        .nav_links .li_nav .a_nav{
            transition: all 0.5s ease 0s;
            text-decoration: none;
        }

        .nav_links .li_nav .a_nav:hover{
            color: dodgerblue;
            text-decoration: none;
        }

        .dropbtn {
            background: white;
            padding: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            margin-left: 20px;
        }

        .dropbtn img {
            border-radius: 50%;
            width: 60px;
            height: 60px;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Botão de logout. */
        .logoutbtn{
            color: red;
            background: white;
            padding: none;
            border: none;
            cursor: pointer;
            font-family: "Roboto", sans-serif;
            font-weight: 400;
            font-size: 16px;
        }

        /* Dropdown Content (Hidden by Default) */
        .dropdown-content {
            display: none;
            position: absolute;
            min-width: 100px;
            z-index: 1;
            top: 60px;
            padding: 10px;
            background: white;
            middle:0;
        }

        /* Dropdown ao passar o mouse */
        .dropdown:hover .dropdown-content {
        display: block;
        }

        /* Links do dropdown*/
        .dropdown-content a{
            text-decoration: none;
            display: block;
            font-weight: lighter;
            margin-top: 10px;
        }

        .li_drop{
            padding: 2px;
        }
        
    </style>
<body>
    <div class="header_nav">
        <a href="https://kartado.com.br/">
            <img class="logo" src="images/logo_Kartado.png" alt="logo">
        </a>
        <nav class="body_nav">
            <div class="nav_links" id="myTopnav">
                <li class="li_nav"><a class="a_nav" href="home.php">Home</a></li>
                
                <li class="li_nav"><a class="a_nav" href="homePlan.php">Planejamento Estratégico</a></li>
                <!-- <li class="li_nav"><a class="a_nav" href="homeGoal.php">Objetivos</a></li> -->
                <?php
                if ($_SESSION['tipo_user'] == 'admin'){
                    echo "<li class='li_nav'><a class='a_nav' href='Users.php'>Users</a></li> ";
                }
                ?>                <!-- <li class="li_nav"><a class="a_nav" href="">Iniciativas</a></li>  -->
                <div class="dropdown">
                    <button class="dropbtn">
                    <?php
                        // Exibir a imagem e a mensagem de sucesso ou erro
                        if (!empty($photo_path)){
                            echo "<img class='pic' src='dbImages/perfilPhotos/$photo_path'>";
                        }
                    ?>
                        <!-- Meu Perfil ▾ -->
                    </button>
                    <div class="dropdown-content">
                        <li class="li_nav li_drop"> 
                            <?php
                            echo "<a href='edit_user.php?cpf=" . $cpf ."'>Configurações</a></li>";
                            ?>
                        <!-- <li class="li_nav li_drop"><a class="a_nav" href="#">Feedback</a></li> -->
                        <br><br>
                        <a href="logout.php"><button class="logoutbtn">Sair</button> </a>
                    </div>
                </div>
            </div> 
        </nav>
    </div>
</body>
</html>
