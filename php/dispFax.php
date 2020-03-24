
<?php
	if (isset($_GET["sit"])) {
		include("MySQLconnection.php");
		$sql = "SELECT numfax,idDep FROM Fax WHERE idGroup='".$_GET["sit"]."' ORDER BY idDep;";
		$stmt = $connectionMySQL->prepare($sql);
		try {
			$stmt->execute();
		} catch (Exception $e) {
			echo "error: ".$e->__toString();
		}
		if ($stmt->rowCount() == 0) {
			echo "";
			exit();
		}
		$tabnum = array();
		$numDep = 0;
		while ($row = $stmt->fetch()) {
			$dep = $row["idDep"];
			$stmt2 = $connectionMySQL->prepare("SELECT typeDep FROM Dep WHERE idDep=".$row["idDep"].";");
			try {
				$stmt2->execute();
			} catch (Exception $e) {
				echo "error: ".$e->__toString();
			}
			$row2 = $stmt2->fetch();
			$dep = $row2[0];
			$idk = array_slice($tabnum,-1,1);
			if ((isset($idk[1]))&&($dep == $idk[1])) {
				$tabnum[] = array(0=>$row["numfax"],1=>$dep);
			}
			else {
				$tabnum[] = array(0=>"STOP",1=>"STOP");
				$tabnum[] = array(0=>$row["numfax"],1=>$dep);
			}	
		}
		echo "<h3>Fax and services</h3>";
		for ($i=0; $i <sizeof($tabnum) ; $i++) { 
			if ($tabnum[$i][1] == "STOP") {
				echo "</table><table class='faxbox'><tr><th>".$tabnum[$i+1][1]."</th></tr>";
			}
			else {
				echo "<tr><td class='faxtd'>".$tabnum[$i][0]."</td></tr>";
			}
		}
		echo "</table>";
	}
?>