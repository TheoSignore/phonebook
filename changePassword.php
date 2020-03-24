<?php
	# to change the administration password

	session_start();
	# This make sure the user can't change the password without beeing identified.
	if ((!isset($_SESSION["auth"]))||($_SESSION["auth"] == false)) header ('location: authentification.php');


	# This check if the actual password is good and that the new passwords entered are the same.
	# Then it update the database.
	if ((isset($_POST["oldwpa"]))&&(isset($_POST["wpa1"]))&&(isset($_POST["wpa2"]))) {
		include("php/MySQLconnection.php");
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
				if($count == 3) str_replace($h1,$h2,$hash[$i]);
			}
		}
		if (password_verify($_POST["oldwpa"], $hash)) {
			if (($_POST["wpa1"] == $_POST["wpa2"])&&($_POST["wpa1"] != '')) {
				$sql = "UPDATE Password SET wpaKey ='".password_hash($_POST["wpa1"], PASSWORD_DEFAULT)."' WHERE idwpa = 1";
				$stmt = $connectionMySQL->prepare($sql);
				try {
					$stmt->execute();
				} catch (Exception $e) {
					echo "Failed <br/>".$e->getMessage();	
				}
				echo "Password changed.<br/><a href='administration.php'>Back to the Phonebook</a>";
			}
			else echo "Password do not match.<br/>";
		}
		else echo "Wrong password<br/>";
	}
	else { 
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Change password</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/index.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	</head>
	<body>
		<form method="post" action="changePassword.php">
			<fieldset>
				<legend>
					CHANGE PASSWORD
				</legend>
				Password<input type="password" name="oldwpa" size="30"><br/>
				New password<input type="password" name="wpa1" size="30"><br/>
				Confirm new password<input type="password" name="wpa2" size="30"><br/>
				<input type="submit" value="Modify password">
			</fieldset>
		</form>
	</body>
</html>
<?php
}
?>