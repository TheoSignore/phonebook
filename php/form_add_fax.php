<table>
	<tr>
		<td>Fax Name</td>
		<td>
			<input type="text" id="addFaxName">
		</td>
	</tr>
	<tr>
		<td>Fax Number</td>
		<td>
			<input type="text" id="addFaxNumber">
		</td>
	</tr>
	<tr>
		<td>Country</td>
		<td>
			<select id="addFaxCty" onchange="addFaxDispSite(this.value)">
<?php
	include("MySQLconnection.php");
	$stmt = $connectionMySQL->prepare("SELECT idCountry,nameCountry FROM Country;");
	$stmt->execute();
	echo "<option value=''>Select a country</option>";
	while ($row = $stmt->fetch()) {
		echo "<option value='".$row["idCountry"]."'>".$row["nameCountry"]."</option>";
	}
	echo "</select>";
?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Site</td>
		<td>
			<select id="addFaxSit" onchange="addFaxDispDep(this.value)">
			</select>
		</td>
	</tr>
	<tr>
		<td>Department</td>
		<td>
			<select id="addFaxDep">
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<button onclick="addFax()">Add</button>
		</td>
	</tr>
</table>
<script type="text/javascript">
	function addFax(){
		var faxName = document.getElementById("addFaxName").value;
		var faxNumber = document.getElementById("addFaxNumber").value;
		var faxCty = document.getElementById("addFaxCty").value;
		var faxSit = document.getElementById("addFaxSit").value;
		var faxDep = document.getElementById("addFaxDep").value;
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) {
	            alert(this.responseText);
	        }
	    };
	    xmlhttp.open("GET","php/addFax.php?name="+faxName+"&&number="+faxNumber+"&&site="+faxSit+"&&dep="+faxDep, true);
	    xmlhttp.send();
	}
	function addFaxDispSite(cty){
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) {
	            document.getElementById("addFaxSit").innerHTML = this.responseText;
	        }
	    };
	    xmlhttp.open("GET","php/modifForm.php?param=sit&&cty="+cty, true);
	    xmlhttp.send();
	}
	function addFaxDispDep(sit){
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) {
	            document.getElementById("addFaxDep").innerHTML = this.responseText;
	        }
	    };
	    xmlhttp.open("GET","php/modifForm.php?param=dep&&sit="+sit, true);
	    xmlhttp.send();
	}
</script>