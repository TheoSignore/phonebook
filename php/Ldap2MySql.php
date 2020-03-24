<?php
	$log = fopen("Ldap2MySql.txt","a+");
	$msg = "\r\n>".date('Y-m-d G:i:s')." - Ldap2MySql.php - script to transfer users found in \"O&M Movianto AD\" into to Phonebook MySQL database.\r\n";
	fputs($log,$msg);
	fclose($log);
	include("MySQLconnection.php");
	include("LdapConnection.php");
	$updated= 0;
	$insertion = 0;
	$countError = 0;
	$log = fopen("Ldap2MySql.txt","a+");

# This script synchronise the O&M Movianto Active Directory database and the Phonebook MySQL database.

# This script will get the users that are belonging to site groups in the country's directory

	# Getting the country's dn
	$sql = "select dn, idCountry from Country;";
	try {
		$stmt = $connectionMySQL->prepare($sql);   
		$stmt->execute();
	} catch (Exception $e) {
		fputs($log,"Failed to get countries.\r\n".$e->getMessage().".\r\n\r\n");
		fclose($log);
		exit;
	}
	while ($row = $stmt->fetch()) {	
		$dn = $row["dn"];
		$idcountry = $row["idCountry"];
		$sql = "select idGroup,cn from Site where idCountry='".$idcountry."';";	
		try {
			$stmt2 = $connectionMySQL->prepare($sql);   
			$stmt2->execute();
		} catch (Exception $e) {
			fputs($log,"Failed to get Sites.\r\n".$e->getMessage().".\r\n\r\n");
			fclose($log);
			exit;
		}
		# getting the sites dn
		while ($row = $stmt2->fetch()) {
			$brackets = array(0=>'(', 1=>')');
			$antibracket = array(0=>"\(", 1=>"\)");
			$site = str_replace ($brackets,$antibracket,$row["cn"]);	
			$SmallGroup = $row["idGroup"];
			$filter = "(&(objectclass=user)(memberof=".$site."))";

			$sr = ldap_search($connectionLdap,$dn,$filter);

			# In case of LDAP query error: displays an error number, message and explanation. Also displays the distinguished name used, the query's filter and format.
			if (!$sr) {
				$msg ="DN: ".ldap_dn2ufn($dn)."\r\nDN: ".$dn."\r\nFilter: ".$filter."\r\nError n°".ldap_errno($connectionLdap)."\r\nldap_error: ".ldap_error($connectionLdap)."\r\n";
				ldap_get_option($connectionLdap, LDAP_OPT_DIAGNOSTIC_MESSAGE, $error);
				$msg .= "ldap_get_option: $error \r\n";
				fputs($log,$msg);
				fclose($log);
			    exit;
			}
			else fputs($log,"Query succesfuly proceeded.\r\n");

			$data = ldap_get_entries($connectionLdap, $sr);
			if ($data["count"] != 0) {
				# add new departments based on the attributes "department" of the selected users
				for ($i=0; $i <$data["count"] ; $i++) {
					if(isset($data[$i]["department"][0])){
						try {
							$sql="select idDep from Dep where typeDep='".$data[$i]["department"][0]."';";
							$stmt3 = $connectionMySQL->prepare($sql);   
							$stmt3->execute();
						} catch (Exception $e) {
							fputs($log,"Failed line 64.\r\n".$e->getMessage().".\r\n\r\n");
							fclose($log);
							exit;
						}
						$count = $stmt3->rowCount();
						if ($count == 0) {
							try {
								$sql="select idDep from Dep;";
								$stmt7 = $connectionMySQL->prepare($sql);   
								$stmt7->execute();
							} catch (Exception $e) {
								fputs($log,"Failed 66.\r\n".$e->getMessage().".\r\n\r\n");
								fclose($log);
								exit;
							}
							$count2 = $stmt7->rowCount();
							$sql = "insert into Dep(idDep,typeDep) values(".$count2.",'".$data[$i]["department"][0]."');";
							$prepare = $connectionMySQL->prepare($sql);   
							$prepare->execute();
							$sql="select idDep from Dep where typeDep='".$data[$i]["department"][0]."';";
							$stmt4 = $connectionMySQL->prepare($sql);   
							$stmt4->execute();
							$row2 = $stmt4->fetch();
							$sql = "insert into Departments(idDep,idGroup) values(".$row2["idDep"].",'".$SmallGroup."');";
							$prepare = $connectionMySQL->prepare($sql);
							$prepare->execute();
						}
						else {
							$sql = "select * from Departments inner join Dep on Departments.idDep = Dep.idDep where typeDep='".$data[$i]["department"][0]."' and idGroup='".$SmallGroup."';";
							$stmt5 = $connectionMySQL->prepare($sql);   
							$stmt5->execute();
							$count = $stmt5->rowCount();
							if ($count == 0) {
								$sql = "select idDep from Dep where typeDep='".$data[$i]["department"][0]."';";
								$stmt6 = $connectionMySQL->prepare($sql);
								$stmt6->execute();
								$row3 = $stmt6->fetch();
								$sql = "insert into Departments(idDep,idGroup) values('".$row3["idDep"]."','".$SmallGroup."');";
								$prepare = $connectionMySQL->prepare($sql);
								$prepare->execute();
							}
						}
					}
				}
				# updates or add users
				for ($i=0;$i<$data["count"];$i++){	
					$sql = "select * from Users where idUser='".$data[$i]["samaccountname"][0]."';";
					$res = $connectionMySQL->prepare($sql);
					$res->execute();
					$count = $res->rowCount();
					if ($count == 0) {
						if (isset($date[$i]["homePhone"][0])) $homephone = $data[$i]["homePhone"][0];
						else $homePhone = -1;
						if (isset($data[$i]["title"][0])) $title = $data[$i]["title"][0]; 
						else $title = "none";
						if (isset($data[$i]["department"][0])) {
							$sql = "select idDep from Dep where typeDep ='".$data[$i]["department"][0]."';";
							$res = $connectionMySQL->prepare($sql);
							$res->execute();
							$row = $res->fetch();
							$dep = $row["idDep"];
						} 
						else $dep = 0;
						if (isset($data[$i]["mail"][0])) $mail = $data[$i]["mail"][0]; 
						else $mail = "none";
						if (isset($data[$i]["ipPhone"][0])) $ipPhone = $data[$i]["ipPhone"][0]; 
						else $ipPhone = "none";
						if (isset($data[$i]["telephoneNumber"][0])) $telephoneNumber = $data[$i]["telephoneNumber"][0]; 
						else $telephoneNumber = "none";
						if (isset($data[$i]["mobile"][0])) $mobile = $data[$i]["mobile"][0]; 
						else $mobile = "none";
						$sql = "insert into Users (idUser,nameUser,firstNameUser,title,idDep,eMail,internCisco,phoneIntern,phoneExtern,phoneMobile,idGroup) values ('".$data[$i]["samaccountname"][0]."','".utf8_encode($data[$i]["sn"][0])."','".utf8_encode($data[$i]["givenname"][0])."','".$title."',".$dep.",'".$mail."','".$ipPhone."',".$homePhone.",'".$telephoneNumber."','".$mobile."','".$SmallGroup."');";
						try {
							$prepare = $connectionMySQL->prepare($sql);   
							$prepare->execute();
							$msg = "User ".$data[$i]["samaccountname"][0]." has been inserted.\r\n";
							$insertion++;
							fputs($log,$msg);
						} catch (Exception $e) {
				    		fputs($log,"Failed to insert ".$data[$i]["samaccountname"][0].".\r\n".$e->getMessage().".\r\n\r\n");
				    		$countError++;
						}
					}
					else {
						$row = $res->fetch();
						# The following tests are here to update the informations a user that already exists. 
						#title
						$attributesMySQL = array(0=>"title",1=>"eMail",2=>"internCisco",3=>"phoneExtern",4=>"phoneMobile");
						$attributesAD = array(0=>"title",1=>"mail",2=>"ipphone",3=>"telephonenumber",4=>"mobile");
						for ($m=0; $m < sizeof($attributesAD) ; $m++) { 
							if ((!isset($data[$i][$attributesAD[$m]][0]))&&($row[$attributesMySQL[$m]] != "none")){
								$sql= "update Users set title='none' where idUser='".$data[$i]["samaccountname"][0]."';";
								$res2 = $connectionMySQL->prepare($sql);
								$res2->execute();
								$updated++;
							}
							if ((isset($data[$i][$attributesAD[$m]][0]))&&($row[$attributesMySQL[$m]] != $data[$i][$attributesAD[$m]][0])){
								$sql= "update Users set title='".$data[$i][$attributesAD[$m]][0]."' where idUser='".$data[$i]["samaccountname"][0]."';";
								$res3 = $connectionMySQL->prepare($sql);
								$res3->execute();
								$updated++;
							}
						}
						
						#phoneIntern - homePhone
						if ((!isset($data[$i]["homephone"][0]))&&($row["phoneIntern"] != -1)){
							$sql= "update Users set phoneIntern= -1 where idUser='".$data[$i]["samaccountname"][0]."';";
							$res8 = $connectionMySQL->prepare($sql);
							$res8->execute();
							$updated++;
						}
						if ((isset($data[$i]["homephone"][0]))&&($row["phoneIntern"] != $data[$i]["homephone"][0])){
							$sql= "update Users set phoneIntern='".$data[$i]["homephone"][0]."' where idUser='".$data[$i]["samaccountname"][0]."';";
							$res9 = $connectionMySQL->prepare($sql);
							$res9->execute();
							$updated++;
						}
						#idDep - department
						if (isset($data[$i]["department"][0])) {
							$sql = "SELECT idDep FROM Dep WHERE typeDep='".$data[$i]["department"][0]."'";
							$stmt8 = $connectionMySQL->prepare($sql);
							$stmt8->execute();
							$row4 = $stmt8->fetch();
						
							if ((!isset($row4["idDep"]))&&($row["idDep"] != 0)){
								$sql= "update Users set idDep=0 where idUser='".$data[$i]["samaccountname"][0]."';";
								$res14 = $connectionMySQL->prepare($sql);
								$res14->execute();
								$updated++;
							}
							if ((isset($row4["idDep"]))&&($row["idDep"] != $row4["idDep"])){
								$sql= "update Users set idDep='".$row4["idDep"]."' where idUser='".$data[$i]["samaccountname"][0]."';";
								$res15 = $connectionMySQL->prepare($sql);
								$res15->execute();
								$updated++;
							}
						}

						# handle the case a user changes of site/group
						$sql = "SELECT cn FROM Site INNER JOIN Users ON Site.idGroup = Users.idGroup WHERE Site.idGroup='".$row["idGroup"]."'";
						$stmt9 = $connectionMySQL->prepare($sql);
						$stmt9->execute();
						$row5 = $stmt9->fetch();
						if ((!isset($data[$i]["memberof"][0]))&&($row["idGroup"] != "NO GROUP")){
							$sql= "update Users set idGroup='NO GROUP' where idUser='".$data[$i]["samaccountname"][0]."';";
							$res16 = $connectionMySQL->prepare($sql);
							$res16->execute();
							$updated++;
						}
						if ((isset($data[$i]["memberof"][0]))&&($row5["cn"] != $data[$i]["memberof"][0])){
							$sql = "SELECT idGroup FROM Site WHERE cn ='".$data[$i]["memberof"][0]."';";
							$res17 = $connectionMySQL->prepare($sql);
							$res17->execute();
							$count5 = $res17->rowCount();
							$row7 = $res17->fetch();
							if ($count5 != 0) {
								$sql= "update Users set idGroup='".$row7["idGroup"]."' where idUser='".$data[$i]["samaccountname"][0]."';";
								$res18 = $connectionMySQL->prepare($sql);
								$res18->execute();
								$updated++;
							}
							else {
								$sql= "update Users set idGroup='NO GROUP' where idUser='".$data[$i]["samaccountname"][0]."';";
								$res19 = $connectionMySQL->prepare($sql);
								$res19->execute();
								$updated++;
							}
						}
					}
				}

				# delete the users that are not anymore in the the Active Directory database
				$deleted = 0;
				$sql = "select idUser from Users where idGroup='".$SmallGroup."';";
				$stmt9 = $connectionMySQL->prepare($sql);
				$stmt9->execute();
				$count3 = $stmt9->rowCount();
				for ($i=0;$i<$count3;$i++) {
					$row6 = $stmt9->fetch();
					$exist = 0;
					for($j=0;$j<$data["count"];$j++){
						if ($data[$j]["samaccountname"][0] == $row6["idUser"]) { 
							$exist = 1;
						}
					}
					if ($exist == 0) {
						$sql = "delete from Users where idUser='".$row6["idUser"]."';";
						$stmt10 = $connectionMySQL->prepare($sql);
						$stmt10->execute();
						$deleted++;
					}
				}
			}
/*
	(\ _ /) 
	(='.'=)  
	(")-(") follow the white rabbit
*/
		}
	}
	$depdel = 0 ;
	$sql = "select idGroup,idDep from Departments";
	$stmt = $connectionMySQL->prepare($sql);   
	$stmt->execute();
	while ($deps = $stmt->fetch()) {
		$sql2 = "select idUser from Users where idDep = '".$deps["idDep"]."' and idGroup = '".$deps["idGroup"]."';";
		$stmt2 = $connectionMySQL->prepare($sql2);   
		$stmt2->execute();
		$count = $stmt2->rowCount();
		if ($count == 0) {
			$sqlf = "DELETE FROM Fax WHERE idDep = '".$deps["idDep"]."' AND idGroup = '".$deps["idGroup"]."';";
			$stmtf = $connectionMySQL->prepare($sqlf);
			$stmtf->execute();
			$sql3 = "DELETE FROM Departments WHERE idDep = '".$deps["idDep"]."' AND idGroup = '".$deps["idGroup"]."';";
			$stmt3 = $connectionMySQL->prepare($sql3);
			try {
			  	$stmt3->execute();
			  	$depdel++;
			} catch (Exception $e) {
			  	fputs($log,"Error deleting department.\r\n");
			}  
			
		}
	}
	

	fputs($log,"Error = ".$countError."\r\nUser inserted ".$insertion.".\r\nUser updated ".$updated.".\r\nUser deleted ".$deleted.".\r\nDepartments deleted ".$depdel.".\r\n");
	fclose($log);
?>