<?php
	# script that authentify the user and redirect him to the administration page in case the password is correct. The password's hash is stored in the database.
	# The user can access the administration page as long as he is authentified with a boolean session index that desappear after the browser closes. 

	session_start();
	if ((isset($_SESSION["auth"]))&&($_SESSION["auth"] == true)) header ('location: administration.php');

	include("php/MySQLconnection.php");

	# this part get the password's hash from the database, clear it from spaces and '$' because those special characters generates errors.
	# then it compares it with the hash of the password entered by the user. If they match, the user is redirected to the administration page.
	# Otherwise an error message appear.
	$sql = "SELECT wpaKey FROM Password WHERE idwpa = 1";
	$stmt = $connectionMySQL->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch();
	$h1 = array(0=>'$');
	$h2 = array(0=>'\$');
	$hash = trim($row["wpaKey"]);
	$count = 0;
	for ($i=0; $i < strlen($hash) ; $i++) { 
		if ($hash[$i] == "$") {
			$count++;
			if($count == 3){
				str_replace($h1,$h2,$hash[$i]);
			}
		}
	}
	if ((isset($_POST["wpa"]))&&(password_verify($_POST["wpa"],$hash))) {
		$_SESSION["auth"] = true;
		header ('location: administration.php');
	}
	else {
		if (isset($_POST["wpa"])) {
			$_SESSION["auth"] = false;
			echo "Wrong password.";
		}
		
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Authentification</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/index.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	</head>
	<body>
		<form method="post" action="authentification.php">
			<fieldset>
				<legend>
					PASSWORD:
				</legend>
				<input type="password" name="wpa" size="30"><input type="submit" value="Enter">
			</fieldset>
		</form>
	</body>
</html>