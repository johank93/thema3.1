<?php

require('connection.php');

if (isset($_GET['device']) && isset($_GET['comparison_type']) && isset($_GET['region_type'])) {
   
    // SELECT fields
    $sql = "SELECT datum AS 'datum', tijd AS 'tijd', AVG(waarde) AS 'gemiddeldewaarde' ";
    
    // SELECT tables
    $sql .= " FROM meting "
            . "INNER JOIN apparaat_huishouden ON meting.app_hh = apparaat_huishouden.id "
            . "INNER JOIN apparaat ON apparaat_huishouden.apparaat_fk = apparaat.id"
            . "INNER JOIN huishouden ON huishouden.id = apparaat_huishouden.huishouden_fk "
            . "INNER JOIN postcode ON huishouden.postcode = postcode.postcode";

    // device number of the household
    $device = $_GET['device'];
    
    $comparison_type = $_GET['comparison_type'];  
    if ($comparison_type == "1") {
        // same device type number
        $sql .= " WHERE typenummer = '" . $device . "'";
    }
    else if ($comparison_type == "2") {
        // same device kind
    }

    $region_type = $_GET['region_type'];
    if ($region_type == "1") {
        // search on zipcode
        // TODO: Replace with current household zipcode
        $sql .= " AND huishouden.postcode = '9501AL'";
    }
    else if ($region_type == "2") {
        // search on county
        // TODO: Replace with current household county
        $sql .= " AND postcode.province_code = 'GR'";
    }
    
    // GROUP BY
    $sql .= " GROUP BY datum,tijd";
    
    // ORDER AND SELECTION
    $sql .= " ORDER BY datum DESC, tijd DESC";
    $sql .= " LIMIT 0,10";
    
    $result = $mysqli->query($sql) or die($mysqli->error);
    
    $data = $result->fetch_all( MYSQLI_ASSOC );
    echo json_encode( $data );
}
else {
    // return empty string
    echo "";
}