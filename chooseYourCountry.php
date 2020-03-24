<?php
	# Creates a cookie with the default country ID chosen by the user

	if ((isset($_POST["defaultCountry"]))&&($_POST["defaultCountry"] != '0')) {
		$WillYouEatMyCookie = setcookie("defaultCountry",$_POST["defaultCountry"],time() + (10 * 365 * 24 * 60 * 60),"../",null,null,true);		
		if ($WillYouEatMyCookie) header("location: index.php");
		else echo "Your brower has not accepted the cookie.<br/>It has to.";
	}
	include("php/MySQLconnection.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Default country</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/index.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	</head>
	<body onload="cookie()">
		<form method="post" action="./chooseYourCountry.php">
			<fieldset>
				<legend>
					Enter your default country:
				</legend>
<?php
				# dropdown list  with the country
				$stmt = $connectionMySQL->prepare("SELECT idCountry,nameCountry FROM Country;");
				$stmt->execute();
				echo "<select name='defaultCountry'>";
				echo "<option value='0'>Select a country</option>";
				while ($row = $stmt->fetch()) echo "<option value='".$row["idCountry"]."'>".$row["nameCountry"]."</option>";
				echo "</select>";
?>
				<input type="submit" value="Enter">
			</fieldset>
		</form>
		<script type="text/javascript">
			// displays an alert : the browser needs to accept cookies
			function cookie(){
				alert("Make sure your browser accepts cookies from this website.\nOtherwise you won't be able to acces the Phonebook.");
			}
		</script>
	</body>
</html>