<?php
	# to change the Active Directory password to use


	session_start();
	if ((!isset($_SESSION["auth"]))||($_SESSION["auth"] == false)) header ('location: authentification.php');

	# to decrypt/encrypt text with a key
	function xor_this($string,$key) {
	    $text = $string;
	    $outText = '';
	    for($i=0; $i<strlen($text);){
	        for($j=0; ($j<strlen($key) && $i<strlen($text)); $j++,$i++){
	            $outText .= $text{$i} ^ $key{$j};
	        }
	    }
	    return $outText;
	}

	# check the actual password is correct, check that the new pasword entries are matching, and update the mysql database
	if ((isset($_POST["oldwpa"]))&&(isset($_POST["wpa1"]))&&(isset($_POST["wpa2"]))) {
		include("php/MySQLconnection.php");
		$sql = "SELECT wpaKey FROM Password WHERE idwpa = 2;";
		$stmt = $connectionMySQL->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch();
		$theKey = "1ATeqP2Q6ua8QOVq";
		if (trim(xor_this($row["wpaKey"],"1ATeqP2Q6ua8QOVq")) == trim($_POST["oldwpa"])) {
			if (($_POST["wpa1"] == $_POST["wpa2"])&&($_POST["wpa1"] != '')) {
				$sql = "UPDATE Password SET wpaKey =\"".xor_this($_POST["wpa1"], $theKey)."\" WHERE idwpa = 2 ;";
				echo $sql;
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
		<form method="post" action="changeADPassword.php">
			<fieldset>
				<legend>
					CHANGE PASSWORD
				</legend>
				Password<input type="password" name="oldwpa" size="30"><br/>
				New password<input type="password" name="wpa1" size="30"><br/>
				Confirm password<input type="password" name="wpa2" size="30"><br/>
				<input type="submit" value="Modify password">
			</fieldset>
		</form>
	</body>
</html>
<?php
}
?>