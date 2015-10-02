<?php
require('inc/connection.php');

if (!isset($_SESSION['huishouden_id'])) {
    echo '<meta http-equiv="refresh" content="0;URL=?p=login" />';
}

$dayselect = date("d");
$monthselect = date("m");
$yearselect = date("Y");

if (!empty($_POST)) {
    $error = NULL;
            
    if(isset($_POST["day"]) && isset($_POST["month"]) && isset($_POST["year"])){

        if (is_numeric($_POST["day"]) && is_numeric($_POST["month"]) && is_numeric($_POST["year"])) {
            $dayselect = $_POST["day"];
            $monthselect = $_POST["month"];
            $yearselect = $_POST["year"];
            if(!checkdate($monthselect,$dayselect,$yearselect)){
                $error .= "<li>Je kunt geen onbestaande datum opgeven!</li>";
            }
            $newdate=strtotime(date("$yearselect/$monthselect/$dayselect"));
            $curdate=strtotime(date("Y/m/d"));

            if ($newdate > $curdate) {
                    $error .= "<li>Je kunt niet in het toekomst kijken xD</li>";
                }
        }else{
            $error .= "<li>Datum moet nummeriek zijn!</li>";
        }
    }
    if (!empty($error)) {

        echo "<div class='alert alert-danger'>";
        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        echo "<strong>Er zijn enkele verplichte velden niet (correct) ingevuld!</strong><br /><br>";
        echo "<ul class='errors'>" . $error . "</ul>";
        echo "</div>";
     }
    
}


$datesqlformat = $yearselect.$monthselect.$dayselect;
$sql = "SELECT tijd, waarde, naam, apparaat.id AS appid 
    FROM meting 
    LEFT JOIN apparaat_huishouden 
    ON meting.app_hh = apparaat_huishouden.id 
    LEFT JOIN apparaat
    ON apparaat_huishouden.apparaat_fk = apparaat.id 
    WHERE apparaat_huishouden.huishouden_fk = " . $_SESSION['huishouden_id'] . "
    AND datum = $datesqlformat  
    ORDER BY tijd    
    ";

$sql2 = "SELECT apparaat.id AS appid, naam, type, merk "
        . "FROM apparaat "
        . "LEFT JOIN apparaat_huishouden ON apparaat.id = apparaat_huishouden.apparaat_fk "
        . "WHERE apparaat_huishouden.huishouden_fk = " . $_SESSION['huishouden_id'];

$result = $mysqli->query($sql) or die($mysqli->error);
$result2 = $mysqli->query($sql2) or die($mysqli->error);
?>

<form class="form-inline" method="POST">
    <input type="text" class="form-control" name="day" size="1px" value="<?php echo $dayselect; ?>">
    <input type="text" class="form-control" name="month" size="1px" value="<?php echo $monthselect; ?>">
    <input type="text" class="form-control" name="year" size="2px" value="<?php echo $yearselect; ?>">
    <input type="submit" value="GO!" class="btn" >
</form>


<?php
if (checkdate($monthselect, $dayselect, $yearselect)) {
    
?>




    <table class="table table-hover" >

        <thead>
            <tr>
                <th>Apparaat</th>
                <th></th>
    <?php
    $stack = array();
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['tijd'], $stack)) {
            array_push($stack, $row['tijd']);
            if (($row['tijd'] / 1) >= 9 AND ( $row['tijd'] / 1) <= 18) {
                $time = date('H.i', strtotime(str_replace('-', '/', $row['tijd'])));
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
                    echo "<td rowspan=2>" . $row['naam'] . "</td>";
                    echo "<td><b>i(kW)</b></td>";
                    //$stack2 = $stack;     
                    
                    
                    $result3 = $mysqli->query($sql) or die($mysqli->error);
                    while ($row2 = $result3->fetch_assoc()) {
                        if ($row2['appid'] == $row['appid']) {
                            if (($row2['tijd'] / 1) >= 9 AND ( $row2['tijd'] / 1) <= 18) {
                                
                                echo "<td>" . $row2['waarde'] . "</td>";
                                
                            }
                        }     
                        
                    }

                    echo "</tr>";
                    echo "<tr>";
                    echo "<td><b>&sum;i(kWh)</b></td>";
                    $value = 0;
                    $result4 = $mysqli->query($sql) or die($mysqli->error);
                    while ($row2 = $result4->fetch_assoc()) {
                        if ($row2['appid'] == $row['appid']) {
                            if (($row2['tijd'] / 1) >= 9 AND ( $row2['tijd'] / 1) <= 18) {
                                $value += $row2['waarde'];
                                echo "<td>$value</td>";
                            }
                        }
                    }
                    echo "</tr>";
                }
            }else{
                echo 'Onjuiste datum!';
            }
            ?>


</table>