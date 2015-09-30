<?php

require('connection.php');
if (isset($_GET['household_id'])) {
    $sql = "SELECT huishouden.id,huishouden.postcode,huisnummer,grootte,telefoonnummer,email,street,city,province "
            . "FROM huishouden "
            . "INNER JOIN postcode on huishouden.postcode = postcode.postcode "
            . "WHERE huishouden.id = " . $_GET['household_id'];

    $result = $mysqli->query($sql) or die($mysqli->error);
    $data = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($data);
} else {
    // return empty string
    echo "";
}