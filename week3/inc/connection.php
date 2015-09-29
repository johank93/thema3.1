<?php

$mysqli = new MySQLi('localhost','root','','nrg');

if (!$mysqli) {
        die('Could not connect: ' . mysqli_error($mysqli));
    }
    
    $GLOBALS['con'] = $mysqli;