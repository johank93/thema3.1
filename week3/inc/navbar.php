<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">NRG</a>
    </div>
    <div>
      <ul class="nav navbar-nav">
        <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "home") echo " class='active'"; } else echo " class='active'"; ?>><a href="?p=home">Home</a></li>
        <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "apparaat") echo " class='active'"; } ?>><a href="?p=apparaat">Apparaatbeheer</a></li>
        <?php 
            if (!isset($_SESSION['huishouden_id'])) {
        ?>
                <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "login") echo " class='active'"; } ?>><a href="?p=login">Login</a></li>
                <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "registreren") echo " class='active'"; } ?>><a href="?p=registreren">Registreren</a></li>
        <?php
            }
            if (isset($_SESSION['huishouden_id'])) {
        ?>         
                <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "overzicht") echo " class='active'"; } ?>><a href="?p=overzicht">Overzicht</a></li>
        <?php
            if (checkAuthorization(1)) {
        ?>                
                <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "gebruikers") echo " class='active'"; } ?>><a href="?p=gebruikers">Gebruikers</a></li>
        <?php
            }
        ?>
                <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "logout") echo " class='active'"; } ?>><a href="?p=logout">Uitloggen</a></li>
                
        <?php
            }
        ?>
      </ul>
        <?php 
        if (checkAuthorization(2)) {
            ?>
        <ul class="nav navbar-nav pull-right" style="padding: 15px 0">
            <li>Ingelogt als 
                <?php 
                echo $_SESSION['email'] . " ";
                switch ($_SESSION['type_id']) {
                            case 0:
                                echo "(Beheerder)";
                                break;
                            case 1:
                                echo "(Gebruiker)";
                                break;
                            case 2:
                                echo "(Huisgenoot)";
                                break;
                        }
                ?></li>
            </ul>
            <?php 
            
                }
                ?>
    </div>
  </div>
</nav>