<?php
require('inc/connection.php');

if (!isset($_SESSION['huishouden_id'])) {
    echo '<meta http-equiv="refresh" content="0;URL=?p=login" />';     
}

$sql = "SELECT apparaat.id AS id, naam, type, merk "
        . "FROM apparaat "
        . "LEFT JOIN apparaat_huishouden ON apparaat.id = apparaat_huishouden.apparaat_fk "
        . "WHERE apparaat_huishouden.huishouden_fk = " . $_SESSION['huishouden_id'];

$result = $mysqli->query($sql) or die($mysqli->error);

?>
        <a class="btn btn-default" href="?p=apparaat_toevoegen" role="button">Apparaat toevoegen</a><br /><br />
<?php 
    if ($result->num_rows > 0) {
?>
        <table class='table table-condensed table-bordered dataTable' cellspacing=0>
		<thead>
			<tr>
				<td><b>Apparaat</b></td>
				<td><b>Merk</b></td>
				<td><b>Typenummer</b></td>
			</tr>
		</thead>
		<tbody id='myTable'>
		<?php
                    while ($row = $result->fetch_assoc()) {
                        echo"<tr>";
                            echo"<td><a href='?p=apparaat_meting&id=".$row['id']."'>".$row['naam']."</a></td>";
                            echo"<td>".$row['merk']."</td>";
                            echo"<td>".$row['type']."</td>";
                        echo"</tr>";
                    }
		?>
                </tbody>
        </table>
 <?php 
    }
    else {
        echo "<div class='alert alert-info'>";
        echo "<strong>U heeft nog geen apparaten ingevoerd</strong><br />";
        echo "Klik op 'Aparaat toevoegen' om een apparaat in te voeren";
        echo "</div>";
    }