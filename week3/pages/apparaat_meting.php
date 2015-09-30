<?php
require('inc/connection.php');
$con = $GLOBALS['con'];

if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $huishoudenidsql = "SELECT id FROM huishouden WHERE email = '$email'";
        
        $huishoudenid = mysqli_query($con, $huishoudenidsql);
        while($row = mysqli_fetch_assoc($huishoudenid)) {
            $valicationchecksql = "SELECT * FROM apparaat_huishouden WHERE apparaat_fk = $_GET[id] AND huishouden_fk = $row[id]";
        }
        
        $valicationcheck = mysqli_query($con,$valicationchecksql);
        
        if(mysqli_num_rows($valicationcheck) >= 1){
            
        if(!empty($_POST)) {

            //Controle of alles is ingevuld
            $error = NULL;
            
           for($i = 0; $i < 24;$i++) {
                if(!is_numeric($_POST[$i])){
                    if(!empty($_POST[$i])){
                        $error .= "<li>$_POST[$i] is not nummeric</li>";
                    }
                }
           }

            if (!empty($error)) {

                    echo "<div class='alert alert-danger'>";
                    echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                    echo "<strong>Er zijn enkele verplichte velden niet (correct) ingevuld!</strong><br /><br>";
                    echo "<ul class='errors'>" . $error . "</ul>";
                    echo "</div>";

            }else {

                while($row = mysqli_fetch_assoc($valicationcheck)) {
                    $app_hhid = $row['id'];
                }
                for($i = 0; $i < 24;$i++) {
                    $value = mysqli_real_escape_string($con,$_POST[$i]);
                    
                    $timestamp = $i ."0000";

                    $meting = "INSERT INTO meting (app_hh, tijd, waarde) VALUES ($app_hhid,$timestamp,$value)";
                    mysqli_query($con,$meting);
                }

                    
                    
                    $lastid = mysqli_insert_id($con);
                    while($row = mysqli_fetch_assoc($huishoudenid)) {
                        $insertconnectie = "INSERT INTO apparaat_huishouden (huishouden_fk, apparaat_fk) VALUES ($row[id],$lastid)";
                        mysqli_query($con,$insertconnectie);
                    }

                    echo "<div class='alert alert-success'>";
                    echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                    echo "<strong>Apparaat succesvol geregistreerd!</strong><br /><br>";
                    echo "</div>";

                    echo "<b></b>";

            }
    }           
        
?>
<form class="form-horizontal" action="" method="POST">
    <fieldset>
           <legend>Meting toevoegen</legend>
    
           <?php
                for($i = 0; $i < 24;$i++) {
                    $time = sprintf("%02d", $i);
                    
                    echo '<div class="form-group">';
                    echo '<label class="col-sm-1 control-label" for="'.$i.'">'.$time.':00</label>';
                    echo '<div class="col-sm-2">';
                    echo '<input type="text" class="form-control" name="'.$i.'" value=""/>';
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
                        <input type='button' class='btn' onclick="location.href='?p=apparaat';" value='Annuleren' />
                </div>
        </div>
    </fieldset>
</form>



<?php
        }
    }else{
        header('Location: ?p=registreren'); 
        echo "nosession";

    }