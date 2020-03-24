<?php
	# loads the sites of a country in a dropdown list
	include("MySQLconnection.php");
	if ($_GET["cty"] == "none") {
		echo "<option value='none'>No site found</option>";
		exit;
	}	
	$stmt = $connectionMySQL->prepare("select idGroup,nameSite from Site where idCountry=:cty;");
	$stmt->bindParam(":cty", $_GET["cty"]);
	$stmt->execute();
	$count = $stmt->rowCount();
	if ($count == 0) {
		echo "<option value='none'>No site found</option>";
		exit;
	}
	$options = "";
	while ($row = $stmt->fetch()) {
		$options .= "<option value='".$row[0]."'>".$row[1]."</option>";
	}
	echo $options;
?>