<?php
	# Check the country parameters before inserting it

	include("MySQLconnection.php");
	$log = fopen("../logs/general_log.txt","a+");
	$msg = "\r\n>".date('Y-m-d G:i:s')." - checkCty.php\r\n";
	fputs($log,$msg);

	# checking the country's ID
	if ((!isset($_GET["idCty"]))||(empty($_GET["idCty"]))||(is_numeric($_GET["idCty"]))||(strlen($_GET["idCty"]) != 2)||(!ctype_upper($_GET["idCty"]))) {
		echo "Error: invalid ID format. Must be 2 uppercase letters.";
		exit;
	}
	if ((!isset($_GET["nameCty"]))||(empty($_GET["nameCty"]))||(is_numeric($_GET["nameCty"]))) {
		echo "Error: invalid country name format. Must not be empty or numeric.";
		exit;
	}
	try {
		$stmt = $connectionMySQL->prepare("select * from Country where idCountry=:idCty ;");
		$stmt->bindParam(":idCty", $_GET["idCty"]);
		$stmt->execute();
		$count = $stmt->rowCount();
		fputs($log,"Query succesful result=".$count."\r\n");	
	} catch (Exception $e) {
	    fputs($log,"Failed\r\n".$e->getMessage().".\r\n\r\n");
	    fclose($log);
	}
	if ($count != 0){
		echo "Error: A country already exists with this ID.";
		exit;
	} 

	# checking the country's name
	try {
		$stmt = $connectionMySQL->prepare("select * from Country where nameCountry=:nameCty ;");
		$stmt->bindParam(":nameCty", $_GET["nameCty"]);
		$stmt->execute();
		$count = $stmt->rowCount();
		fputs($log,"Query succesful result=".$count."\r\n");	
	} catch (Exception $e) {
   		fputs($log,"Failed\r\n".$e->getMessage().".\r\n\r\n");
    	fclose($log);
	}
	if ($count != 0) {
		echo "Error: A country already exists with this name.";
		exit;
	}

	# inserting it the country. the 'dn' is created by adding the country to a piece of dn.
	try {
		$stmt = $connectionMySQL->prepare("insert into Country(idCountry,nameCountry,idRegion,dn) values (:idCty,:nameCty,:reg,:dn);");
		$stmt->bindParam(":idCty", $_GET["idCty"]);
		$stmt->bindParam(":nameCty", $_GET["nameCty"]);
		$stmt->bindParam(":reg", $_GET["reg"]);
		$dn = "OU=".$_GET["idCty"].",OU=_Organisation,DC=moviantogroup,DC=com";
		$stmt->bindParam(":dn", $dn);
		$stmt->execute();
		echo "The country has been succesfuly added";
		fputs($log,"Query succesful\r\n");	
		fclose($log);
	} catch (Exception $e) {
   		fputs($log,"Failed\r\n".$e->getMessage().".\r\n\r\n");
    	fclose($log);
	}
?>