<?php
	# script that displays sites in the country chosen in the "form_del_sites"

	include("MySQLconnection.php");
	$sql = "SELECT * FROM Site WHERE idCountry='".$_GET["id"]."';";
	$stmt = $connectionMySQL->prepare($sql);
	$stmt->execute();
	if ($stmt->rowCount() != 0) {
		echo "
			<tr>
				<th>Country</th>
				<th>Name</th>
				<th>Security Group</th>
				<th colspan='2'>AD path</th>
				<th>Delete ?</th>
			</tr>";
		while ($row = $stmt->fetch()) {
			$sizeForInput = strlen($row["cn"])*1.2;
			echo "
				<tr>
					<td class='info'>".$row["idCountry"]."</td>
					<td class='info'>".$row["nameSite"]."</td>
					<td class='info'>".$row["idGroup"]."</td>
					<td class='info'>".$row["cn"]."</td>
					<td class='info'><button onclick=\"modifCn('".$row["idGroup"]."')\">Modify</button></td>
					<td class='info'><button onclick=\"delSite('".$row["idGroup"]."')\">Delete</button></td>
				</tr>";
		}
	}
	else echo "
			<tr>
				<td style='color: white;'>No site found.</td>
			</tr>";
	
?>