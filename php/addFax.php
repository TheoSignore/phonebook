<?php 
	if ((isset($_GET["name"]))&&(isset($_GET["number"]))&&(isset($_GET["site"]))&&(isset($_GET["dep"]))) {
		include("MySQLconnection.php");
		$sql = "INSERT INTO Fax (nameFax,numFax,idDep,idGroup) VALUES (:name,:num,:dep,:site);";
		$stmt = $connectionMySQL->prepare($sql);
		$stmt->bindParam(":name",$_GET["name"]);
		$stmt->bindParam(":num",$_GET["number"]);
		$stmt->bindParam(":site",$_GET["site"]);
		$stmt->bindParam(":dep",$_GET["dep"]);
		try {
			$stmt->execute();
			echo "Fax added.";
		} catch (Exception $e) {
			echo "Failed to add fax:\n".$e->__toString();
		}	
	}
 ?>