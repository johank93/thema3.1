<?php
require('inc/connection.php');
$con = $GLOBALS['con'];

if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $huishoudenidsql = "SELECT id FROM huishouden WHERE email = '$email'";
        
        $huishoudenid = mysqli_query($con, $huishoudenidsql);
        while($row = mysqli_fetch_assoc($huishoudenid)) {
            $apparaat_huishoudenselectsql = "SELECT * FROM apparaat_huishouden WHERE apparaat_fk = $_GET[id] AND huishouden_fk = $row[id]";
        }
         
        $apparaat_huishoudenselect = mysqli_query($con,$apparaat_huishoudenselectsql);
        
        while($row = mysqli_fetch_assoc($apparaat_huishoudenselect)) {
            $allmetingensql = "SELECT * FROM meting WHERE app_hh = $row[id]";
            $app_hhid = $row['id'];
        }
        
        if(mysqli_num_rows($apparaat_huishoudenselect) >= 1){
            
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

                
                for($i = 0; $i < 24;$i++) {
                    $value = mysqli_real_escape_string($con,$_POST[$i]);
                    
                    $timestamp = $i * 10000;
                    $oldvalue = null;
                    $allmetingen = mysqli_query($con,$allmetingensql);
                    while($row = mysqli_fetch_array($allmetingen)) {
                        if(($row['tijd']/1) == $i && $row['app_hh'] == $app_hhid){
                            $oldvalue = $row['waarde'];
                        }
                    }
                    
                    if(!isset($oldvalue)){
                        $meting = "INSERT INTO meting (app_hh, tijd, waarde) VALUES ($app_hhid,$timestamp,$value)";
                    }else{
                        $meting = "UPDATE meting SET waarde = $value WHERE app_hh = $app_hhid AND tijd = $timestamp";
                    }
                    
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
                    for($i = 0; $i < 24;$i++) {
                        $time = sprintf("%02d", $i);
                        $set = false;
                        echo '<div class="form-group">';
                        echo '<label class="col-sm-1 control-label" for="'.$i.'">'.$time.':00</label>';
                        echo '<div class="col-sm-2">';
                        $allmetingen = mysqli_query($con,$allmetingensql);
                        while($row = mysqli_fetch_array($allmetingen)) {
                            if(($row['tijd']/1) == $i){
                                echo '<input type="text" class="form-control" name="'.$i.'" value="'.$row['waarde'].'"/>';
                                $set = true;
                            }
                        }
                        if($set == false){
                            echo '<input type="text" class="form-control" name="'.$i.'" value=""/>';
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
                        <input type='button' class='btn' onclick="location.href='?p=apparaat';" value='Annuleren' />
                </div>
        </div>
</form>



<?php
        }
    }else{
        header('Location: ?p=registreren'); 
        echo "nosession";

    }