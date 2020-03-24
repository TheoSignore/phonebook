<?php
	# Connection to the Phonebook MySQL database. In the case of an error during the connection process, see ../logs/MySQLconnection.txt

	$log = fopen("MySQLconnection.txt","a+");
	$msg = "\r\n>".date('Y-m-d G:i:s')." - MySQLconnection.php - script to connect \"Phonebook MySQL database\".\r\n";
	fputs($log,$msg);
	try {
		$usr="phonebook";
		$psswd="Movianto95";
		$connectionMySQL = new PDO("mysql:host=localhost;dbname=phonebook;charset=utf8", $usr, $psswd);
		$connectionMySQL->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connectionMySQL->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		fputs($log,"Connected to MySql database.\r\n");
		fclose($log);
	}
	catch(Exception $e){
	    fputs($log,"Connection to MySql database failed.\r\n".$e->getMessage().".\r\n");
	    fclose($log);
	    exit;
	}
?>