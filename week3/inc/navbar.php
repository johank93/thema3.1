<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">WebSiteName</a>
    </div>
    <div>
      <ul class="nav navbar-nav">
        <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "home") echo " class='active'"; } else echo " class='active'"; ?>><a href="?p=home">Home</a></li>
        <?php 
            if (!isset($_SESSION['huishouden_id'])) {
        ?>
                <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "login") echo " class='active'"; } ?>><a href="?p=login">Login</a></li>
                <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "registreren") echo " class='active'"; } ?>><a href="?p=registreren">Registreren</a></li>
        <?php
            }
            if (isset($_SESSION['huishouden_id'])) {
        ?>
                <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "apparaat") echo " class='active'"; } ?>><a href="?p=apparaat">Apparaatbeheer</a></li>
                <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "overzicht") echo " class='active'"; } ?>><a href="#">Overzicht</a></li>
                <li <?php if (isset($_GET['p'])) { if ($_GET['p'] == "logout") echo " class='active'"; } ?>><a href="?p=logout">Uitloggen</a></li>
        <?php
            }
        ?>
      </ul>
    </div>
  </div>
</nav>