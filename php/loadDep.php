<?php
	# This script load the departments in a dropdown list based on site ID
	include("MySQLconnection.php");
	if ($_GET["sit"] == "none") {
		echo "<option value='none'>No department do add</option>";
		exit;
	}	
	$stmt = $connectionMySQL->prepare("select idDep, typeDep from Dep where idDep not in (select idDep from Departments where idGroup=:sit);");
	$stmt->bindParam(":sit", $_GET["sit"]);
	$stmt->execute();
	$count = $stmt->rowCount();
	if ($count == 0) {
		echo "<option value='none'>No department to add</option>";
		exit;
	}
	$options = "";
	while ($row = $stmt->fetch()) {
		if ($row[0] != 0) $options .= "<option value='".$row[0]."'>".$row[1]."</option>";
	}
	if ($options == "") $options = "<option value='none'>No department to add</option>";
	echo $options;
?>