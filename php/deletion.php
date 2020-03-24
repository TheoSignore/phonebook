<?php
# script that recursively deletes countries, sites, departments and users, after giving some informations about it:
# If a country is about to be deleted, it informs the user it will also delete sites, departments and users in this country.
# And it does the same thing with the sites

	include("MySQLconnection.php");
	if ($_GET["param"] == "info" ) {
		switch ($_GET["sub"]) {
			case "cty":
				$sql = "SELECT idGroup FROM Site WHERE idCountry ='".$_GET["id"]."';";
				$stmt = $connectionMySQL->prepare($sql);
				$stmt->execute();
				$countSite = $stmt->rowCount();
				$countDep = 0;
				$countUser = 0;
				while ($row = $stmt->fetch()) {
					$sql2 = "SELECT idGroup FROM Departments WHERE idGroup = '".$row["idGroup"]."';";
					$stmt2 = $connectionMySQL->prepare($sql2);
					$stmt2->execute();
					$countDep += $stmt2->rowCount();
					$row2 = $stmt2->fetch();
					$sql3 = "SELECT count(idUser) as num FROM Users WHERE idGroup='".$row["idGroup"]."';";
					$stmt3= $connectionMySQL->query($sql3);
					$row3 = $stmt3->fetch();
					$stmt3->closeCursor();
					$countUser += $row3["num"];
				}
				echo "By deleting this country, you will also delete:\n".$countSite." site(s)\n".$countDep." departments\nand ".$countUser." user(s)\n\n Are you sure ?";
				break;
				
			case "sit":
				$sql3 = "SELECT count(idUser) as num FROM Users WHERE idGroup='".$_GET["id"]."';";
				$stmt3= $connectionMySQL->query($sql3);
				$countUser = $stmt3->fetch();
				$stmt3->closeCursor();
				$sql4 = "SELECT count(idGroup) as num FROM Departments WHERE idGroup='".$_GET["id"]."';";
				$stmt4= $connectionMySQL->query($sql4);
				$countDep = $stmt4->fetch();
				$stmt4->closeCursor();
				echo "By deleting this site, you will also delete:\n".$countDep["num"]." departments\nand ".$countUser["num"]." user(s)\n\n Are you sure ?";
				break;

			default:
				exit;
				break;
		}
	}
	else {
		if ($_GET["param"] == "upd") {
			switch ($_GET["sub"]) {
				case 'dn':
					$sql4 = "UPDATE Country SET dn='".$_GET["dn"]."' WHERE idCountry='".$_GET["id"]."';";
					$stmt4 = $connectionMySQL->prepare($sql4);
					try {
						$stmt4->execute();
						echo "Path modified";
					} catch (Exception $e) {
						echo "Failed\n".$e->getMessage();
					}
					break;

				case 'cn':
					$sql4 = "UPDATE Site SET cn='".$_GET["cn"]."' WHERE idGroup='".$_GET["id"]."';";
					$stmt4 = $connectionMySQL->prepare($sql4);
					try {
						$stmt4->execute();
						echo "Path modified";
					} catch (Exception $e) {
						echo "Failed\n".$e->getMessage();
					}
					break;

				default:
					break;
			}
		}
		else{
			switch ($_GET["sub"]) {
			case "cty":
				$sql = "SELECT idGroup FROM Site WHERE idCountry ='".$_GET["id"]."';";
				$stmt = $connectionMySQL->prepare($sql);
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					$sql2 = "DELETE FROM Users WHERE idGroup ='".$row["idGroup"]."';";
					$stmt2 = $connectionMySQL->prepare($sql2);
					$stmt2->execute();
					$sql2 = "DELETE FROM Departments WHERE idGroup ='".$row["idGroup"]."';";
					$stmt2 = $connectionMySQL->prepare($sql2);
					$stmt2->execute();
				}
				$sql = "DELETE FROM Country WHERE idCountry ='".$_GET["id"]."';";
				$stmt = $connectionMySQL->prepare($sql);
				
				try {
					$stmt->execute();
					echo "Country deleted.";
				} catch (Exception $e){
					echo "Deletion failed:\n".$e->getMessage();
				}
				break;

			case "sit":
				$sql = "DELETE FROM Fax WHERE idGroup ='".$_GET["id"]."';";
				$stmt = $connectionMySQL->prepare($sql);
				$stmt->execute();
				$sql = "DELETE FROM Users WHERE idGroup ='".$_GET["id"]."';";
				$stmt = $connectionMySQL->prepare($sql);
				$stmt->execute();
				$sql = "DELETE FROM Departments WHERE idGroup ='".$_GET["id"]."';";
				$stmt = $connectionMySQL->prepare($sql);
				$stmt->execute();
				$sql = "DELETE FROM Site WHERE idGroup ='".$_GET["id"]."';";
				$stmt = $connectionMySQL->prepare($sql);
				try {
					$stmt->execute();
					echo "Site deleted.";
				} catch (Exception $e){
					echo "Deletion failed:\n".$e->getMessage();
				}
				break;

			default:
				break;
			}
		}
		
	}
?>