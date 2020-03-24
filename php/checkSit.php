<?php
	# script that verify the site's parameters before inserting it 

	include("MySQLconnection.php");
	$log = fopen("../logs/general_log.txt","a+");
	$msg = "\r\n>".date('Y-m-d G:i:s')." - checkSit.php\r\n";
	fputs($log,$msg);

	# check the site ID
	if ((!isset($_GET["group"]))||(empty($_GET["group"]))) {
		echo "Error: Site ID must be defined.";
		exit;
	}
	if ((!isset($_GET["name"]))||(empty($_GET["name"]))||(is_numeric($_GET["name"]))) {
		echo "Error: invalid site name format. Must be defined and non-numeric.";
		exit;
	}
	try {
		$stmt = $connectionMySQL->prepare("select * from Site where idGroup=:group ;");
		$stmt->bindParam(":group", $_GET["group"]);
		$stmt->execute();
		$count = $stmt->rowCount();
		fputs($log,"Query succesful result=".$count."\r\n");	
	} catch (Exception $e) {
	    fputs($log,"Failed\r\n".$e->getMessage().".\r\n\r\n");
	    fclose($log);
	}
	if ($count != 0){
		echo "Error: A site already exists with this ID.";
		exit;
	}

	# Check if a site doesn't already exist in this country with this name 
	try {
		$stmt = $connectionMySQL->prepare("select * from Site where nameSite=:name and idCountry=:cty ;");
		$stmt->bindParam(":name", $_GET["name"]);
		$stmt->bindParam(":cty", $_GET["cty"]);
		$stmt->execute();
		$count = $stmt->rowCount();
		fputs($log,"Query succesful result=".$count."\r\n");	
	} catch (Exception $e) {
   		fputs($log,"Failed\r\n".$e->getMessage().".\r\n\r\n");
    	fclose($log);
	}
	if ($count != 0) {
		echo "Error: A site already exists with this name in the same country.";
		exit;
	}

	# Insert the site into the data base
	try {
		$stmt = $connectionMySQL->prepare("insert into Site(idGroup,idCountry,nameSite,cn) values (:group,:cty,:name,:cn);");
		$stmt->bindParam(":cty", $_GET["cty"]);
		$stmt->bindParam(":name", $_GET["name"]);
		$stmt->bindParam(":group", $_GET["group"]);
		$stmt->bindParam(":cn", $_GET["cn"]);
		$stmt->execute();
		echo "The site has been succesfuly added";
		fputs($log,"Query succesful\r\n");	
		fclose($log);
	} catch (Exception $e) {
   		fputs($log,"Failed\r\n".$e->getMessage().".\r\n\r\n");
    	fclose($log);
	}
?>