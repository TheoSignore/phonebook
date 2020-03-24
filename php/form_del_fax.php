<?php 

?>
<table>
	<tr>
		<td>Country</td>
		<td>
			<select onchange="delFaxDispSite(this.value)">
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
		<td>Site</td>
		<td>
			<select id="delFaxSit" onchange="delFaxDispDep(this.value)">
			</select>
		</td>
		<td>Department</td>
		<td>
			<select id="delFaxDep" onchange="dispFax()">
			</select>
		</td>
	</tr>
</table>
<table id="fax" style="border: solid 0.07vw black; border-collapse: collapse; padding: 0.1vw;">
	
</table>
<script type="text/javascript">
	function deleteFax(idFax){
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) {
	            alert(this.responseText);
	            dispFax();
	        }
	    };
	    xmlhttp.open("GET","php/delFax.php?idFax="+idFax, true);
	    xmlhttp.send();
	}
	function dispFax(){
		var site = document.getElementById("delFaxSit").value;
		var dep = document.getElementById("delFaxDep").value;
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) {
	            document.getElementById("fax").innerHTML = this.responseText;
	        }
	    };
	    xmlhttp.open("GET","php/getFax.php?site="+site+"&&dep="+dep, true);
	    xmlhttp.send();
	}
	function delFaxDispSite(cty){
		document.getElementById("fax").innerHTML = "";
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) {
	            document.getElementById("delFaxSit").innerHTML = this.responseText;
	        }
	    };
	    xmlhttp.open("GET","php/modifForm.php?param=sit&&cty="+cty, true);
	    xmlhttp.send();
	}
	function delFaxDispDep(sit){
		document.getElementById("fax").innerHTML = "";
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) {
	            document.getElementById("delFaxDep").innerHTML = this.responseText;
	        }
	    };
	    xmlhttp.open("GET","php/modifForm.php?param=dep&&sit="+sit, true);
	    xmlhttp.send();
	}
</script>