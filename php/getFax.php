<?php 
	if ((isset($_GET["site"]))&&(isset($_GET["dep"]))) {
		if ($_GET["dep"] != '') {
			include("MySQLconnection.php");
			$sql = "SELECT idFax,nameFax,numFax FROM Fax WHERE idGroup ='".$_GET["site"]."' AND idDep = ".$_GET["dep"].";";
			$stmt = $connectionMySQL->prepare($sql);
			$stmt->execute();
			if ($stmt->rowCount() != 0) {
				echo "<tr><th style='border: solid 0.07vw black; border-collapse: collapse; padding: 0.1vw;'>Name</th><th style='border: solid 0.07vw black; border-collapse: collapse; padding: 0.1vw;'>Number</th><th style='border: solid 0.07vw black; border-collapse: collapse; padding: 0.1vw;'>Delete</th></tr>";
				while ($row = $stmt->fetch()) {
					echo "<tr><td style='border: solid 0.07vw black; border-collapse: collapse; padding: 0.1vw;'>".$row["nameFax"]."</td><td style='border: solid 0.07vw black; border-collapse: collapse; padding: 0.1vw;'>".$row["numFax"]."</td><td style='border: solid 0.07vw black; border-collapse: collapse; padding: 0.1vw;'><button onclick=\"deleteFax('".$row["idFax"]."')\">delete</button></td></tr>";
				}
			}
			else echo "<tr><td>No printer found<td></tr>";
		}
		else echo "";
	}
?>