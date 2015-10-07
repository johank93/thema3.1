<?php
require('inc/connection.php');

if (!empty($_POST)) {

    //Controle of alles is ingevuld
    $error = NULL;
    if (empty($_POST['email'])) {
        $error .= "<li>Email is nog niet ingevuld.</li>";
    }
    if (empty($_POST['password'])) {
        $error .= "<li>Wachtwoord is nog niet ingevuld.</li>";
    }

    if (empty($error)) {

        $email = mysql_real_escape_string($_POST['email']);
        $wachtwoord = sha1($_POST['password']);

        $result = $mysqli->query("SELECT * FROM gebruikers WHERE email = '" . $email . "' AND wachtwoord = '" . $wachtwoord . "'") or die($mysqli->error);


        if ($result->num_rows == 0) {
            $error .= "<li>De combinatie van email en/of wachtwoord is onjuist!</li>";
        } else {
            /* fetch object array */
            while ($row = $result->fetch_row()) {
                $_SESSION['huishouden_id'] = $row[2];
                $_SESSION['type_id'] = $row[3];
                $_SESSION['email'] = $row[0];
            }
            echo '<meta http-equiv="refresh" content="0;URL=?p=home" />';
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
?>
<form class="form-horizontal" method="POST">
    <legend>Inloggen</legend>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
        <div class="col-sm-4">
            <input type="email" class="form-control" name="email" id="inputEmail3" placeholder="Email" value="<?php if (isset($_POST['email'])) {echo $_POST['email'];} ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">Wachtwoord</label>
        <div class="col-sm-4">
            <input type="password" name="password" class="form-control" id="inputPassword3" placeholder="Wachtwoord">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">Inloggen</button>
        </div>
    </div>
</form>