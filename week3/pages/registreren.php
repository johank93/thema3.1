<?php
require('inc/connection.php');
$con = $GLOBALS['con'];


if(!empty($_POST)) {

		//Controle of alles is ingevuld
		$error = NULL;
		if (empty($_POST['grootte'])) {$error .= "<li>Aantal inwoners</li>";}
		if (empty($_POST['postcode'])) {$error .= "<li>Postcode</li>";}
		if (empty($_POST['huisnr'])) {$error .= "<li>Huisnummer</li>";}
                if (empty($_POST['tel'])) {$error .= "<li>Tel</li>";}
                if (empty($_POST['email'])) {$error .= "<li>Email</li>";}
                if (empty($_POST['passwd'])) {  $error .= "<li>Wachtwoord is nog niet ingevuld</li>";}
                
                if (!validateEmail($_POST['email'])) {  $error .= "<li>E-mail is ongeldig</li>";}
                if(!preg_match('/^[0-9\-]{10,11}$/', $_POST['tel']) AND (!empty($_POST['tel']))) { $error .= "<li>Ongeldig telefoonnummer</li>";}
                
		if (!empty($error)) {
			
			echo "<div class='alert alert-danger'>";
			echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			echo "<strong>Er zijn enkele verplichte velden niet (correct) ingevuld!</strong><br /><br>";
			echo "<ul class='errors'>" . $error . "</ul>";
			echo "</div>";
                        
		}else {
		
			$postcode = mysqli_real_escape_string($con,$_POST['postcode']);
			$huisnr = mysqli_real_escape_string($con,$_POST['huisnr']);
			$grootte = mysqli_real_escape_string($con,$_POST['grootte']);
			$tel = mysqli_real_escape_string($con,$_POST['tel']);
                        $email = mysqli_real_escape_string($con,$_POST['email']);
                        $passwd = mysqli_real_escape_string($con,$_POST['passwd']);
                        
                        $passwd = sha1($_POST['passwd']);
                        
                        $sql = "INSERT INTO huishouden (postcode, huisnummer, grootte, telnummer, email, wachtwoord) VALUES ('$postcode','$huisnr','$grootte','$tel','$email','$passwd')";
                        mysqli_query($con,$sql);
                        
                        echo "<div class='alert alert-success'>";
			echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			echo "<strong>Je bent succesvol geregistreerd!</strong><br /><br>";
			echo "</div>";
                        
                        echo "<b></b>";
                        
                        $_SESSION['email'] = $_POST['email'];
                        
                        header('Location: ?p=apparaat');
                        
		}
	}

        
        

?>

    <form method="POST">
       <fieldset>
           <legend>Registratie</legend>

           <table>

       
       
       <tr>
        <td>Postcode</td>
        <td><input type="text" class="form-control" name="postcode" size="20" value="<?php if(isset($_POST['postcode'])) {echo $_POST['postcode'];}?>"></td>
       </tr>
       
       <tr>
        <td width="150px">Huisnummer</td>
        <td><input type="text" class="form-control" name="huisnr" size="20" value="<?php if(isset($_POST['huisnr'])) {echo $_POST['huisnr'];}?>"></td>
       </tr>
       
       <tr>
        <td width="150px">Aantal inwonende</td>
        <td><input type="text" class="form-control" name="grootte" size="20" value="<?php if(isset($_POST['grootte'])) {echo $_POST['grootte'];}?>"></td>
       </tr>

       <tr>
        <td>Telefoonnummer</td>
        <td><input type="text" class="form-control" maxlength=10 name="tel" value="<?php if(isset($_POST['tel'])) {echo $_POST['tel'];}?>"></td>
       </tr>
       
       <tr>
        <td>Email</td>
        <td><input type="text" class="form-control" name="email" size="20" value="<?php if(isset($_POST['email'])) {echo $_POST['email'];}?>"></td>
       </tr>
       
       <tr>
        <td>Wachtwoord</td>
        <td><input type="password" class="form-control" name="passwd" size="20"></td>
       </tr>
       
       </table>
       </fieldset><br/>

       <input type="submit" class="btn btn-default" value="Registreer"/>



       </fieldset>
    </form>