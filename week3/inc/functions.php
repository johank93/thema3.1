<?php

session_start();

function pageload() {

//if (!isset($_SESSION["username"])) {
    //include 'homepage.php';
//}Else{
    if (!isset($_GET['p'])) {

        include 'pages/home.php';
    } Else {
        $page = $_GET['p'];

        if (file_exists('pages/' . $page . '.php')) {

            include ('pages/' . $page . '.php');
        } Else {

            echo "De opgevraagde pagina bestaat niet, probeer het later opnieuw!";
        }
    }
}

//}
?>