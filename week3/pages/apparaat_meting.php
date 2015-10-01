<?php
require('inc/connection.php');

if (!isset($_SESSION['huishouden_id'])) {
    echo '<meta http-equiv="refresh" content="0;URL=?p=login" />';
}

$apparaat_huishoudenselectsql = "SELECT * FROM apparaat_huishouden WHERE apparaat_fk = " . $_GET['id'] . " AND huishouden_fk = " . $_SESSION['huishouden_id'];
$apparaat_huishoudens = $mysqli->query($apparaat_huishoudenselectsql) or die($mysqli->error);

while ($row = $apparaat_huishoudens->fetch_assoc()) {
    $allmetingensql = "SELECT * FROM meting WHERE app_hh = " . $row['id'];
    $app_hhid = $row['id'];
}

if ($apparaat_huishoudens->num_rows >= 1) {

    if (!empty($_POST)) {
        
        //Controle of alles is ingevuld
        $error = NULL;

        for ($i = 0; $i < 24; $i++) {
            if (!is_numeric($_POST[$i])) {
                if (!empty($_POST[$i])) {
                    $error .= "<li>$_POST[$i] is not nummeric</li>";
                }
            }
        }
        
        for ($i = 0; $i < 24; $i++) {
            if (empty($_POST[$i])) {
                $_POST[$i] = 0;
            }
        }

        if (!empty($error)) {

            echo "<div class='alert alert-danger'>";
            echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
            echo "<strong>Er zijn enkele verplichte velden niet (correct) ingevuld!</strong><br /><br>";
            echo "<ul class='errors'>" . $error . "</ul>";
            echo "</div>";
        } else {

            for ($i = 0; $i < 24; $i++) {
                $value = $_POST[$i];

                $timestamp = $i * 10000;
                $oldvalue = null;
                $allmetingen = $mysqli->query($allmetingensql) or die($mysqli->error);
                while ($row = $allmetingen->fetch_assoc()) {
                    if (($row['tijd'] / 1) == $i && $row['app_hh'] == $app_hhid) {
                        $oldvalue = $row['waarde'];
                    }
                }

                if (!isset($oldvalue)) {
                    $meting = "INSERT INTO meting (app_hh, tijd, waarde) VALUES ($app_hhid,$timestamp,$value)";
                } elseif($value == ''){
                    $meting = "DELETE FROM meting WHERE app_hh = $app_hhid AND tijd = $timestamp"; 
                }else{
                        $meting = "UPDATE meting SET waarde = $value WHERE app_hh = $app_hhid AND tijd = $timestamp";
                }
                mysqli_query($mysqli,$meting);
            }

            echo "<div class='alert alert-success'>";
            echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
            echo "<strong>Apparaat succesvol geregistreerd!</strong><br /><br>";
            echo "</div>";

            echo "<b></b>";
        }
    }
}
?>
<from class="form-inline" action="" method="POST">
    <fieldset>
        <legend>Meting toevoegen</legend>
        <div class="form-group">
            <div class="col-sm-2">
                <input type="text" class="form-control" name="date" value=""/>
            </div>
        </div>
        <button type="submit" name="submit" class="btn">GO!</button>
    </fieldset>
</from>
<br>


<form class="form-horizontal" action="" method="POST">


<?php
for ($i = 0; $i < 24; $i++) {
    $time = sprintf("%02d", $i);
    $set = false;
    echo '<div class="form-group">';
    echo '<label class="col-sm-1 control-label" for="' . $i . '">' . $time . ':00</label>';
    echo '<div class="col-sm-2">';
    $allmetingen = $mysqli->query($allmetingensql) or die($mysqli->error);
    while ($row = $allmetingen->fetch_assoc()) {
        if (($row['tijd'] / 1) == $i) {
            echo '<input type="text" class="form-control" name="' . $i . '" value="' . $row['waarde'] . '"/>';
            $set = true;
        }
    }
    if ($set == false) {
        echo '<input type="text" class="form-control" name="' . $i . '" value=""/>';
    }
    echo '</div>';
    echo '<div class="col-sm-2">';
    echo 'Kw';
    echo '</div>';
    echo '</div>';
}
?> 
    <div class="control-group">
        <div class="controls">
            <br>
            <button type="submit" name="submit" class="btn">Opslaan</button>
            <input type='button' class='btn' onclick="location.href = '?p=apparaat';" value='Annuleren' />
        </div>
    </div>
</form>
