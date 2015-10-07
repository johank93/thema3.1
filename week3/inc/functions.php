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

function validateEmail($email) {
  return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function checkAuthorization($type_id) {
    if (isset($_SESSION['type_id'])) {
        if ($type_id >= $_SESSION['type_id']) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
//}
?>