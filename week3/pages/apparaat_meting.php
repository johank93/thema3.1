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
            if (empty($_POST['naam'])) {$error .= "<li>Naam</li>";}
            if (empty($_POST['merk'])) {$error .= "<li>Merk</li>";}
            if (empty($_POST['type'])) {$error .= "<li>Type</li>";}

            if (!empty($error)) {

                    echo "<div class='alert alert-danger'>";
                    echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                    echo "<strong>Er zijn enkele verplichte velden niet (correct) ingevuld!</strong><br /><br>";
                    echo "<ul class='errors'>" . $error . "</ul>";
                    echo "</div>";

            }else {

                    $naam = mysqli_real_escape_string($con,$_POST['naam']);
                    $merk = mysqli_real_escape_string($con,$_POST['merk']);
                    $type = mysqli_real_escape_string($con,$_POST['type']);
                    

                    $insertapparaten = "INSERT INTO apparaat (naam, merk, type) VALUES ('$naam','$merk','$type')";
                    mysqli_query($con,$insertapparaten);
                    
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
                    echo '<label class="col-sm-1 control-label" for="'.$time.'">'.$time.':00</label>';
                    echo '<div class="col-sm-2">';
                    echo '<input type="text" class="form-control" name="'.$time.'" value=""/>';
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