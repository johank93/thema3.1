<?php
ob_start();
session_destroy();

	echo"<h1> Uitloggen</h1>";
	echo"<hr>";
	echo"<br>";
	 echo "U wordt uitgelogd.";
				header('Refresh: 2; URL=index.php');



?>