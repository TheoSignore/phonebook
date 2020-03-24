<?php
	# script that changes the search form based on the different parameters sent
	include("MySQLconnection.php");
	if ((isset($_GET["param"]))&&(!empty($_GET["param"]))) {
		switch ($_GET["param"]) {
			case "cty":
					$sql = "SELECT idCountry,nameCountry FROM Country WHERE idRegion = ? ;";
					$stmt = $connectionMySQL->prepare($sql);
					$stmt->bindParam(1,$_GET["reg"]);
					$stmt->execute();
					$count = $stmt->rowCount();
					if ($count != 0) {
						echo "<option value=''>Select a country</option>";
						while ($row = $stmt->fetch()) {
							echo "<option value='".$row[0]."'>".$row[1]."</option>";
						}
					}
					else echo "<option value=''>No country found</option>";
				break;
			case "sit":
					$sql = "SELECT idGroup,nameSite FROM Site WHERE idCountry = ? ;";
					$stmt = $connectionMySQL->prepare($sql);
					$stmt->bindParam(1,$_GET["cty"]);
					$stmt->execute();
					$count = $stmt->rowCount();
					if ($count != 0) {
						echo "<option value=''>Select a site</option>";
						while ($row = $stmt->fetch()) {
							echo "<option value='".$row[0]."'>".$row[1]."</option>";
						}
					}
					else echo "<option value=''>No site found</option>";
					
				break;
			case "dep":
					$sql = "SELECT Departments.idDep,typeDep FROM Dep INNER JOIN Departments ON Dep.idDep = Departments.idDep WHERE idGroup = ? ;";
					$stmt = $connectionMySQL->prepare($sql);
					$stmt->bindParam(1,$_GET["sit"]);
					try {
						$stmt->execute();
					} catch (Exception $e) {
						echo "failed ".$e->getMessage();
					}
					
					$count = $stmt->rowCount();
					if (isset($_GET["fax"])) {
						if ($count != 0) {
							while ($row = $stmt->fetch()) {
								echo "<li onclick=\"dispFax('".$row[0]."')\">".$row[1]."</li>";
							}
						}
						else echo "<li>No department found</li>";
					}
					else {
						if ($count != 0) {
							echo "<option value=''>Select a department</option>";
							while ($row = $stmt->fetch()) {
								echo "<option value='".$row[0]."'>".$row[1]."</option>";
							}
						}
						else echo "<option value=''>No department found</option>";
					}
				break;
			default:
				exit;
				break;
		}
	}
?>