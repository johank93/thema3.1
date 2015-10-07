<?php

require('inc/connection.php');

echo "<legend>Gebruikers beheren</legend>";

if (checkAuthorization(0)) {
    $sql = "SELECT gebruikers.*,postcode.street,huishouden.huisnummer,huishouden.postcode,postcode.city FROM gebruikers INNER JOIN huishouden ON huishouden.id = gebruikers.huishouden_id INNER JOIN postcode ON postcode.postcode = huishouden.postcode ORDER BY huishouden_id";
} else if (checkAuthorization(1)) {
    $sql = "SELECT * FROM gebruikers WHERE huishouden_id=" . $_SESSION['huishouden_id'];
} else {
    echo '<meta http-equiv="refresh" content="0;URL=?p=login" />';
}

$aantal_huisgenoten = 0;
$result = $mysqli->query($sql) or die($mysqli->error);
while ($row = $result->fetch_assoc()) {
    if ($row['type_id'] == 2) {
        $aantal_huisgenoten++;
    }
}
if ($aantal_huisgenoten < 4) {
    echo '<a class="btn btn-default" href="?p=gebruikers&action=add" role="button">Gebruiker toevoegen</a><br /><br />';
}

if (isset($_SESSION['huishouden_id'])) {
    if (isset($_GET['action'])) {
        if (isset($_POST['adduser'])) {

            //Controle of alles is ingevuld
            $error = NULL;
            if (checkAuthorization(0)) {
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
                } elseif (!preg_match('/^[0-9\-]{10,11}$/', $_POST['tel']) AND ( !empty($_POST['tel']))) {
                    $error .= "<li>Ongeldig telefoonnummer</li>";
                }
            }
            if (empty($_POST['email'])) {
                $error .= "<li>Email</li>";
            } elseif (!validateEmail($_POST['email'])) {
                $error .= "<li>E-mail is ongeldig</li>";
            }
            if (empty($_POST['passwd'])) {
                $error .= "<li>Wachtwoord is nog niet ingevuld</li>";
            }

            if (!empty($error)) {

                echo "<div class='alert alert-danger'>";
                echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                echo "<strong>Er zijn enkele verplichte velden niet (correct) ingevuld!</strong><br /><br>";
                echo "<ul class='errors'>" . $error . "</ul>";
                echo "</div>";
            } else {
                if (checkAuthorization(0)) {
                    $postcode = $mysqli->real_escape_string($_POST['postcode']);
                    $huisnr = $mysqli->real_escape_string($_POST['huisnr']);
                    $grootte = $mysqli->real_escape_string($_POST['grootte']);
                    $tel = $mysqli->real_escape_string($_POST['tel']);
                }
                $email = $mysqli->real_escape_string($_POST['email']);
                $passwd = $mysqli->real_escape_string($_POST['passwd']);

                $passwd = sha1($_POST['passwd']);

                $succes = false;

                if (checkAuthorization(0)) {
                    $check_huishouden = $mysqli->query("SELECT * FROM huishouden WHERE postcode= '$postcode' and huisnummer = '$huisnr'") or die($mysqli->error);
                    if ($check_huishouden->num_rows == 0) {
                        $check_postcode = $mysqli->query("SELECT * FROM postcode WHERE postcode= '$postcode' and minnumber <= '$huisnr' AND maxnumber >= '$huisnr'") or die($mysqli->error);
                        if ($check_postcode->num_rows > 0) {


                            $sql = "INSERT INTO huishouden (postcode, huisnummer, grootte, telefoonnummer) VALUES ('$postcode','$huisnr','$grootte','$tel')";
                            $mysqli->query($sql) or die($mysqli->error);

                            $lastid = $mysqli->insert_id;

                            $mysqli->query("INSERT INTO gebruikers (email,wachtwoord,huishouden_id,type_id) VALUES ('$email', '$passwd', $lastid, 1)") or die($mysqli->error);

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
                        $succes = true;
                    }
                } else {
                    $mysqli->query("INSERT INTO gebruikers (email,wachtwoord,huishouden_id,type_id) VALUES ('$email', '$passwd', " . $_SESSION['huishouden_id'] . ", 2)") or die($mysqli->error);
                    $succes = true;
                }

                if ($succes) {

                    echo "<div class='alert alert-success'>";
                    echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                    echo "<strong>Je bent succesvol geregistreerd!</strong><br /><br>";
                    echo "</div>";

                    echo "<b></b>";

                    header('Location: ?p=gebruikers');
                }
            }
        }
        if ($_GET['action'] == "add") {
            $form = '<form class="form-horizontal" method="POST">
                <legend>Registratie</legend>';
            if (checkAuthorization(0)) {
            $form .= '<div class="form-group">
                    <label class="col-sm-2 control-label">Postcode</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="postcode" id="inputPostcode3" placeholder="Postcode" value="';
                            if (isset($_POST["postcode"])) {
                                $form .= $_POST["postcode"];
                            }
                        $form .= '">
                    </div>
                </div>';             
            $form .= '<div class="form-group">
                    <label class="col-sm-2 control-label">Huisnummer</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="huisnr" id="inputHuisnummer3" placeholder="Huisnummer" value="';
                            if (isset($_POST["huisnr"])) {
                                $form .= $_POST["huisnr"];
                            }
                        $form .= '">
                    </div>
                </div>';             
            $form .= '<div class="form-group">
                    <label class="col-sm-2 control-label">Aantal inwonende</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" name="grootte" id="inputGrootte3" placeholder="Aantal inwonende" value="';
                            if (isset($_POST["grootte"])) {
                                $form .= $_POST["grootte"];
                            }
                        $form .= '">
                    </div>
                </div>';             
            $form .= '<div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Telefoonnummer</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="tel" maxlength=12 id="inputTelefoonnummer3" placeholder="Telefoonnummer" value="';
                            if (isset($_POST["tel"])) {
                                $form .= $_POST["tel"];
                            }
                        $form .= '">
                    </div>
                </div>';             
            }
            $form .= '<div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-4">
                        <input type="email" class="form-control" name="email" id="inputEmail3" placeholder="Email" value="';
                            if (isset($_POST["email"])) {
                                $form .= $_POST["email"];
                            }
                        $form .= '">
                    </div>
                </div>';     
            $form .= '    <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Wachtwoord</label>
                    <div class="col-sm-4">
                        <input type="password" name="passwd" class="form-control" id="inputPassword3" placeholder="Wachtwoord">
                    </div>
                </div>';
            $form .= '    <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" id="adduser" name="adduser" class="btn btn-default">Huisgenoot toevoegen</button>
                    </div>
                </div>';
            $form .= '</form>';

            echo $form;
        } else if ($_GET['action'] == "delete" && isset($_GET['email'])) {
            $email = $_GET['email'];
            $mysqli->query("DELETE FROM gebruikers WHERE email = '$email' AND huishouden_id = " . $_SESSION['huishouden_id']) or die($mysqli->error);
            header('Location: ?p=gebruikers');
        }
    } else {
        if ($result->num_rows == 0) {
            echo "<p>Er zijn nog geen gebruikers ingevoerd!</p>";
        } else {
            echo "<table class='table table-condensed table-bordered dataTable' cellspacing=0>";
            echo "<tr>";
            echo "<th>#</th>";
            echo "<th>E-mail</th>";
            echo "<th>Type</th>";
            if (checkAuthorization(0)) {
                echo "<th>Straat</th><th>Huisnummer</th><th>Postcode</th><th>Plaats</th>";
            }
            echo "</tr>";
            $result = $mysqli->query($sql) or die($mysqli->error);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                if (checkAuthorization(1) && ($row['type_id'] == 2 || ($row['type_id'] == 1 && $_SESSION['type_id'] == 0))) {
                    echo "<td><a href='?p=gebruikers&action=delete&email=" . $row['email'] . "'><i class='glyphicon glyphicon-remove'></i></a></td>";
                } else {
                    echo "<td></td>";
                }

                echo "<td>" . $row['email'] . "</td>";
                switch ($row['type_id']) {
                    case 0:
                        echo "<td>Beheerder</td>";
                        break;
                    case 1:
                        echo "<td>Gebruiker</td>";
                        break;
                    case 2:
                        echo "<td>Huisgenoot</td>";
                        break;
                }
                if (checkAuthorization(0)) {
                    echo "<td>" . $row['street'] . "</td>";
                    echo "<td>" . $row['huisnummer'] . "</td>";
                    echo "<td>" . $row['postcode'] . "</td>";
                    echo "<td>" . $row['city'] . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }
} else {
    echo '<meta http-equiv="refresh" content="0;URL=?p=login" />';
}