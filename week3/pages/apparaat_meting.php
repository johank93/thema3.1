<?php

require('inc/connection.php');

if (!isset($_SESSION['huishouden_id'])) {
    echo '<meta http-equiv="refresh" content="0;URL=?p=login" />';
}

$dayselect = date("d");
$monthselect = date("m");
$yearselect = date("Y");

if (isset($_GET['id'])) {
    $apparaat_huishoudenselectsql = "SELECT * FROM apparaat_huishouden WHERE apparaat_fk = " . $_GET['id'] . " AND huishouden_fk = " . $_SESSION['huishouden_id'];
    $apparaat_huishoudens = $mysqli->query($apparaat_huishoudenselectsql) or die($mysqli->error);

    while ($row = $apparaat_huishoudens->fetch_assoc()) {
        $allmetingensql = "SELECT * FROM meting WHERE app_hh = " . $row['id'];
        
        $app_hhid = $row['id'];
    }

    if ($apparaat_huishoudens->num_rows >= 1) {
        
        if (!empty($_POST)) {
            $newformat = '';
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
                            $error .= "<li>Je kunt geen datum in het toekomst opgeven!</li>";
                        }
                }else{
                    $error .= "<li>Datum moet nummeriek zijn!</li>";
                }
            }else{
                $dayselect = $_POST["day2"];
                $monthselect = $_POST["month2"];
                $yearselect = $_POST["year2"];
                if(empty($_POST["day2"]) || empty($_POST["month2"]) || empty($_POST["year2"])){
                    $error .= "<li>No date given!</li>";
                }

                //Controle of alles is ingevuld
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
            }

            if (!empty($error)) {

                echo "<div class='alert alert-danger'>";
                echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                echo "<strong>Er zijn enkele verplichte velden niet (correct) ingevuld!</strong><br /><br>";
                echo "<ul class='errors'>" . $error . "</ul>";
                echo "</div>";
            } else {
       
            if(!isset($_POST['day']) && !isset($_POST['month']) && !isset($_POST['year'])){
                    $day = $_POST['day2'];
                    $month = $_POST['month2'];
                    $year = $_POST['year2'];
                    $datesqlformat = $year.$month.$day;

                    for ($i = 0; $i < 24; $i++) {
                        $value = $_POST[$i];

                        $timestamp = $i * 10000;
                        $existoldvalue = false;

                        $allmetingensql = "SELECT * FROM meting WHERE app_hh = $app_hhid AND datum = $datesqlformat";
                        $allmetingen = $mysqli->query($allmetingensql) or die($mysqli->error);
                        while ($row = $allmetingen->fetch_assoc()) {
                            if (($row['tijd'] / 1) == $i) {
                                $existoldvalue = true;
                            }
                        }

                        if ($existoldvalue == false) {
                            $meting = "INSERT INTO meting (app_hh, tijd, datum, waarde) VALUES ($app_hhid,$timestamp,$datesqlformat,$value)";
                        }  else {
                            $meting = "UPDATE meting SET waarde = $value WHERE app_hh = $app_hhid AND tijd = $timestamp AND datum = $datesqlformat";
                        }
                        mysqli_query($mysqli, $meting);
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

        <form class="form-inline" method="POST">
            <input type="text" class="form-control" name="day" size="1px" value="<?php echo $dayselect; ?>">
            <input type="text" class="form-control" name="month" size="1px" value="<?php echo $monthselect; ?>">
            <input type="text" class="form-control" name="year" size="2px" value="<?php echo $yearselect; ?>">
            <input type="submit" value="GO!" class="btn" >
        </form>

        <br>


        <form class="form-horizontal" action="" method="POST">


        <?php
        
        
        if(checkdate($monthselect,$dayselect,$yearselect)){
            $datesqlformat = $yearselect.$monthselect.$dayselect;
            $allmetingensql = "SELECT * FROM meting WHERE app_hh = $app_hhid AND datum = $datesqlformat";

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
                <input type="hidden" name="day2" value="<?php echo $dayselect; ?>"/>
                <input type="hidden" name="month2" value="<?php echo $monthselect; ?>"/>
                <input type="hidden" name="year2" value="<?php echo $yearselect; ?>"/>
                <div class="control-group">
                    <div class="controls">
                        <br>
                        <button type="submit" name="submit" class="btn">Opslaan</button>
                        <input type='button' class='btn' onclick="location.href = '?p=apparaat';" value='Annuleren' />
                    </div>
                </div>
            </form>
        <?php
        }else{
            echo "Onjuiste datum!";
        }
    }else{
        echo "Geen toegang!";
    }
}

