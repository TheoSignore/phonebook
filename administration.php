<?php
	# phonebook administration page

	# In case the user hasn't been authentified with the password, he is redirected to the authentification page
	session_start();
	if ((!isset($_SESSION["auth"]))||($_SESSION["auth"] == false)) header ('location: authentification.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>O&M Movianto - Phonebook - Administration</title>
		<link rel="stylesheet" type="text/css" href="css/index.css">
		<script type="text/javascript" src="js/jquery.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	</head>
	<body>
		<header>
			<table class="table_header">
				<tr>
					<td style="width: 151px"><img class="header_logo" src="images/OMMovianto-logo.png"></td>
					<td><h1>O&M Movianto&nbsp;&nbsp;&nbsp;Phonebook - administration</h1></td>
					<td><a href="index.php"><img title="Back to the phonebook" class="administration_logo" src="images/phonebook.png"></a></td>
				</tr>
				<tr>
					<td>
						<a href="changePassword.php"><button>Modify password</button></a>
					</td>
					<td>
						<button onclick="RunLdap2MySQLManualy()">Synchronise with Active Directory manualy</button>
					</td>
					<td>
						<a href="changeADPassword.php"><button>Modify Active Directory Password</button></a>
					</td>
				</tr>
			</table>
		</header>
		<!-- dropdown list to delete a  site or a country-->
		<h3>Add a new
			<select onchange="loadFormAdd(this.value)">
				<option value="sit">site</option>
				<option value="cty">country</option>
			</select>
		</h3>
		<div id="formAdd">
		</div>

		<!-- Form to modify some site's and country's information, or delete them-->
		<h3>Delete or modify a
			<select autocomplete='off' onchange="loadFormDel(this.value)">
				<option value="sit">site</option>
				<option value="cty">country</option>
			</select>
		</h3>
		<div id="formDel">
			<!--By default, the site form is displayed-->
			<table>
				<tr>
					<td>Select a country<td>
					<td>
			<?php
				# dropdown list  to choose the country
				include("php/MySQLconnection.php");
				$stmt = $connectionMySQL->prepare("SELECT idCountry,nameCountry FROM Country;");
				$stmt->execute();
				echo "<select id='cty' autocomplete='off' onchange='dispSite(this.value)'>";
				echo "<option value='0'>Select a country</option>";
				while ($row = $stmt->fetch()) {
					echo "<option value='".$row["idCountry"]."'>".$row["nameCountry"]."</option>";
				}
				echo "</select>";
			?>
					</td>
				</tr>
			</table>
			<table id="dispsite">
			</table>
			<h3 style="margin-top: 40px;">
				<select onchange="formPrint(this.value)">
					<option value='add'>Add</option>
					<option value='del'>Delete</option>
				</select>
				 a Fax
			</h3>
			<div id="divFax">
			<div>
			<script type="text/javascript">
				$('#divFax').load('php/form_add_fax.php');
				function formPrint(choice){
					switch(choice){
						case "add":
							$('#divFax').load('php/form_add_fax.php');
						break;
						case "del":
							$('#divFax').load('php/form_del_fax.php');
						break;
						default:
							$('#divFax').load('php/form_add_fax.php');
						break;
						
					}
				}
				// display the country's sites
				function dispSite(idCountry){
					if (idCountry != '0'){
						var xmlhttp = new XMLHttpRequest();
					    xmlhttp.onreadystatechange = function() {
					        if (this.readyState == 4 && this.status == 200) {
					            document.getElementById("dispsite").innerHTML = this.responseText;
					        }
					    };
					    xmlhttp.open("GET","php/dispSite.php?id="+idCountry, true);
					    xmlhttp.send();
					}
				}

				// the 2 following functions are used during the deletion process.
				// delSite: display how many departments and users are belonging the site
				// deleteTheSiteCompletely: delete the site, the departments and users that are belonging the site
				function delSite(id){
					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (this.readyState == 4 && this.status == 200) {
				        	var txt = this.responseText ;
				            if(confirm(txt)){
				            	if(confirm(txt)){
				            		deleteTheSiteCompletely(id);
				            	}
				            }
				        }
				    };
				    xmlhttp.open("GET","php/deletion.php?param=info&&sub=sit&&id="+id, true);  
				    xmlhttp.send();
				}
				function deleteTheSiteCompletely(id){
					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (this.readyState == 4 && this.status == 200) {
				            alert(this.responseText);
				        }
				    };
				    xmlhttp.open("GET","php/deletion.php?param=del&&sub=sit&&id="+id, true);
				    xmlhttp.send();
				    loadFormDel("sit");
				}

				// modify the AD path in the database in case of change of the AD architecture
				function modifCn(idGroup){
					var NewCn = prompt("Enter a new path:");
					if (NewDn != null) {
						if (NewCn == '') {
							alert("AD path must not be empty");
						}
						else {
							var xmlhttp = new XMLHttpRequest();
							xmlhttp.onreadystatechange = function() {
						    	if (this.readyState == 4 && this.status == 200) {
						    		alert(this.responseText);
								}
							};
							xmlhttp.open("GET","php/deletion.php?param=upd&&sub=cn&&id="+idGroup+"&&cn="+NewCn, false);
							xmlhttp.send();
							loadFormDel("sit");
						}
					}
				}
			</script>	
		</div>
		<script>
			function RunLdap2MySQLManualy(){
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
			    	if (this.readyState == 4 && this.status == 200) {
			    		alert("Synchronised.");
					}
				};
				xmlhttp.open("GET","php/Ldap2MySql.php?", true);
				xmlhttp.send();
			}
			// display the form to add the proper entity
			$('#formAdd').load('php/form_add_sit.php');
			function loadFormAdd(choice){
				switch(choice){
					case "sit":
						$('#formAdd').load('php/form_add_sit.php');
					break;
					case "cty":
						$('#formAdd').load('php/form_add_cty.php');
					break;
					default:
						$('#formAdd').load('php/form_add_sit.php');
					break;
					
				}
			}
			// display the form to delete the proper entity
			function loadFormDel(choice){
				switch(choice){
					case "sit":
						$('#formDel').load('php/form_del_sit.php');
					break;
					case "cty":
						$('#formDel').load('php/form_del_cty.php');
					break;
					default:
						$('#formDel').load('php/form_del_sit.php');
					break;
					
				}
			}
		</script>
		<footer>
			<h5>Developped by Théo Signore for O&M - Movianto</h5>
		</footer>
	</body>
</html>