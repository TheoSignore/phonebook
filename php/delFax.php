<?php 
	if (isset($_GET["idFax"])) {
		include("MySQLconnection.php");
		$sql = "DELETE FROM Fax WHERE idFax =".$_GET["idFax"].";";
		$stmt = $connectionMySQL->prepare($sql);
		try {
			$stmt->execute();
			echo "Printer deleted.";
		} catch (Exception $e) {
			echo "Failed to delete the printer:\n".$e->__toString();
		}
	}
?>