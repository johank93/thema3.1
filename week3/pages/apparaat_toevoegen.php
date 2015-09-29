<?php
$con = mysqli_connect('localhost','root','','nrg');

    if (!$con) {
        die('Could not connect: ' . mysqli_error($con));
    }

    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $huishoudenidsql = "SELECT id FROM huishouden WHERE email = '$email'";
        
        $huishoudenid = mysqli_query($con, $huishoudenidsql);
        
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
           <legend>Apparaat toevoegen</legend>
    
        <div class="form-group">
                <label class="col-sm-1 control-label" for="naam">* Naam:</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" name="naam" value="<?php if(!empty($_POST)) {echo $_POST['naam']; }?>" />
                </div>
        </div>
        <div class="form-group">
                <label class="col-sm-1 control-label" for="merk">* Merk:</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" name="merk" value="<?php if(!empty($_POST)) {echo $_POST['merk']; }?>" />
                </div>
        </div>
        <div class="form-group">
                <label class="col-sm-1 control-label" for="type">* Type:</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" name="type" value="<?php if(!empty($_POST)) {echo $_POST['type']; }?>" />
                </div>
        </div>   
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
    }else{
        header('Location: ?p=registreren'); 
        echo "nosession";

    }