<?php
require('inc/connection.php');

if (!isset($_SESSION['huishouden_id'])) {
    echo '<meta http-equiv="refresh" content="0;URL=?p=login" />';     
}

$sql = "SELECT tijd, waarde, naam, apparaat.id AS appid 
    FROM meting 
    LEFT JOIN apparaat_huishouden 
    ON meting.app_hh = apparaat_huishouden.id 
    LEFT JOIN apparaat
    ON apparaat_huishouden.apparaat_fk = apparaat.id 
    WHERE apparaat_huishouden.huishouden_fk = ".$_SESSION['huishouden_id']."
    ORDER BY tijd    
    ";

$sql2 = "SELECT apparaat.id AS appid, naam, type, merk "
        . "FROM apparaat "
        . "LEFT JOIN apparaat_huishouden ON apparaat.id = apparaat_huishouden.apparaat_fk "
        . "WHERE apparaat_huishouden.huishouden_fk = " . $_SESSION['huishouden_id'];

    $result = $mysqli->query($sql) or die($mysqli->error);
    $result2 = $mysqli->query($sql2) or die($mysqli->error);
    
    
?>

<table class="table table-hover" >

    <thead>
    <tr>
        <th>Apparaat</th>
        <th></th>
        <?php
        $stack = array();
            while ($row = $result->fetch_assoc()) {
                if(!in_array($row['tijd'], $stack)){
                    array_push($stack,$row['tijd']);
                    if(($row['tijd'] / 1) >= 9 AND ($row['tijd'] / 1) <= 18){
                        $time = date('H.i', strtotime(str_replace('-','/', $row['tijd'])));
                        echo "<th>$time</th>";
                    }
                }
            }
        
        ?>
    </tr>
    </thead>
    <?php
    //print_r(array_values($stack));
    while ($row = $result2->fetch_assoc()) {
        echo "<tr>";
                echo "<td rowspan=2>".$row['naam']."</td>";
                echo "<td><b>i(kW)</b></td>";
        //$stack2 = $stack;     
        $result3 = $mysqli->query($sql) or die($mysqli->error);
        while ($row2 = $result3->fetch_assoc()) {
            if($row2['appid'] == $row['appid']){
                if(($row2['tijd'] / 1) >= 9 AND ($row2['tijd'] / 1) <= 18){
                    echo "<td>".$row2['waarde']."</td>";
                }
            }
        }

        echo "</tr>";
        echo "<tr>";
        echo "<td><b>&sum;i(kWh)</b></td>";
        $value = 0;
        $result4 = $mysqli->query($sql) or die($mysqli->error);
        while ($row2 = $result4->fetch_assoc()) {
            if($row2['appid'] == $row['appid']){
                if(($row2['tijd'] / 1) >= 9 AND ($row2['tijd'] / 1) <= 18){
                    $value += $row2['waarde'];
                    echo "<td>$value</td>";
                }
            }
        }
        echo "</tr>";
    }   
    
    ?>
    
    
</table>