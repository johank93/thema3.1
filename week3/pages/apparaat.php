<?php
$con = mysqli_connect('localhost','root','','nrg');

    if (!$con) {
        die('Could not connect: ' . mysqli_error($con));
    }

$_SESSION['email'] = "fabiangroenewold@gmail.com";
    	if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];
            $huishoudenidsql = "SELECT id FROM huishouden WHERE email = '$email'";
            
            $huishoudenid = mysqli_query($con, $huishoudenidsql);
            
?>
        <a class="btn btn-default" href="?p=apparaat_toevoegen" role="button">Apparaat toevoegen</a><br /><br />
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
                
                    while($row = mysqli_fetch_assoc($huishoudenid)) {
                        $sql = "SELECT * FROM apparaat 
                                LEFT JOIN apparaat_huishouden
                                ON apparaat.id = apparaat_huishouden.apparaat_fk
                                WHERE apparaat_huishouden.huishouden_fk = $row[id]
                                ";
                    }
                    $res = mysqli_query($con, $sql);
                    if(isset($res)){
                        while($row = mysqli_fetch_assoc($res)) {

                            echo"<tr>";
                                echo"<td>".$row['naam']."</td>";
                                echo"<td>".$row['merk']."</td>";
                                echo"<td>".$row['type']."</td>";
                            echo"</tr>";
                        }
                    }
		?>
        </table>
		
        
<?php
    }else{
        header('Location: ?p=registreren'); 
        echo "nosession";
            
    }

