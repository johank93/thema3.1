<?php
require('inc/connection.php');
$con = $GLOBALS['con'];


if (!empty($_POST)) {

    //Controle of alles is ingevuld
    $error = NULL;
    if (empty($_POST['grootte'])) {
        $error .= "<li>Aantal inwoners</li>";
    }
    if (empty($_POST['postcode'])) {
        $error .= "<li>Postcode</li>";
    }
    if (empty($_POST['huisnr'])) {
        $error .= "<li>Huisnummer</li>";
    }
    if (empty($_POST['tel'])) {
        $error .= "<li>Tel</li>";
    }
    if (empty($_POST['email'])) {
        $error .= "<li>Email</li>";
    }
    if (empty($_POST['passwd'])) {
        $error .= "<li>Wachtwoord is nog niet ingevuld</li>";
    }

    if (!validateEmail($_POST['email'])) {
        $error .= "<li>E-mail is ongeldig</li>";
    }
    if (!preg_match('/^[0-9\-]{10,11}$/', $_POST['tel']) AND ( !empty($_POST['tel']))) {
        $error .= "<li>Ongeldig telefoonnummer</li>";
    }

    if (!empty($error)) {

        echo "<div class='alert alert-danger'>";
        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        echo "<strong>Er zijn enkele verplichte velden niet (correct) ingevuld!</strong><br /><br>";
        echo "<ul class='errors'>" . $error . "</ul>";
        echo "</div>";
    } else {

        $postcode = mysqli_real_escape_string($con, $_POST['postcode']);
        $huisnr = mysqli_real_escape_string($con, $_POST['huisnr']);
        $grootte = mysqli_real_escape_string($con, $_POST['grootte']);
        $tel = mysqli_real_escape_string($con, $_POST['tel']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $passwd = mysqli_real_escape_string($con, $_POST['passwd']);

        $passwd = sha1($_POST['passwd']);

        $succes = false;

        $check_huishouden = $mysqli->query("SELECT * FROM huishouden WHERE postcode= '$postcode' and huisnummer = '$huisnr'") or die($mysqli->error);
        $check_huishouden = $mysqli->query("SELECT * FROM huishouden WHERE postcode= '$postcode' and huisnummer = '$huisnr'") or die($mysqli->error);
        if ($check_huishouden->num_rows == 0) {
            $check_postcode = $mysqli->query("SELECT * FROM postcode WHERE postcode= '$postcode' and minnumber <= '$huisnr' AND maxnumber >= '$huisnr'") or die($mysqli->error);
            if ($check_postcode->num_rows > 0) {


                $sql = "INSERT INTO huishouden (postcode, huisnummer, grootte, telefoonnummer) VALUES ('$postcode','$huisnr','$grootte','$tel')";
                $mysqli->query($sql) or die($mysqli->error);

                $lastid = $mysqli->insert_id;

                $mysqli->query("INSERT INTO gebruikers (email,wachtwoord,huishouden_id,type_id) VALUES ('$email', '$passwd', $lastid, 1)") or die($mysqli->error);

                $_SESSION['huishouden_id'] = $lastid;
                $_SESSION['type_id'] = 1;

                $succes = true;
            } else {
                echo "<div class='alert alert-danger'>";
                echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                echo "<strong>De ingevoerde postcode is nog niet beschikbaar voor metingen!</strong><br /><br>";
                echo "</div>";
            }
        } else {
            $row = $check_huishouden->fetch_row();
            $id = $row[0];
            $mysqli->query("INSERT INTO gebruikers (email,wachtwoord,huishouden_id,type_id) VALUES ('$email', '$passwd', $id, 2)") or die($mysqli->error);
            $_SESSION['huishouden_id'] = $row[0];
            $_SESSION['type_id'] = 2;
            $succes = true;
        }

        if ($succes) {
            $_SESSION['email'] = $_POST['email'];
            header('Location: ?p=apparaat');
        }
    }
}
?>

<form class="form-horizontal" method="POST">
    <legend>Registratie</legend>
    <div class="form-group">
        <label class="col-sm-2 control-label">Postcode</label>
        <div class="col-sm-2">
            <input type="text" class="form-control" name="postcode" id="inputPostcode3" placeholder="Postcode" value="<?php if (isset($_POST['postcode'])) {echo $_POST['postcode'];} ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Huisnummer</label>
        <div class="col-sm-2">
            <input type="text" class="form-control" name="huisnr" id="inputHuisnummer3" placeholder="Huisnummer" value="<?php if (isset($_POST['huisnr'])) {echo $_POST['huisnr'];} ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Aantal inwonende</label>
        <div class="col-sm-2">
            <input type="number" class="form-control" name="grootte" id="inputGrootte3" placeholder="Aantal inwonende" value="<?php if (isset($_POST['grootte'])) {echo $_POST['grootte'];} ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Telefoonnummer</label>
        <div class="col-sm-2">
            <input type="text" class="form-control" name="tel" maxlength=12 id="inputTelefoonnummer3" placeholder="Telefoonnummer" value="<?php if (isset($_POST['tel'])) {echo $_POST['tel'];} ?>">
        </div>
    </div>        
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
        <div class="col-sm-4">
            <input type="email" class="form-control" name="email" id="inputEmail3" placeholder="Email" value="<?php if (isset($_POST['email'])) {echo $_POST['email'];} ?>">
        </div>
    </div>     
    <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">Wachtwoord</label>
        <div class="col-sm-4">
            <input type="password" name="passwd" class="form-control" id="inputPassword3" placeholder="Wachtwoord">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">Registreer</button>
        </div>
    </div>
</form>