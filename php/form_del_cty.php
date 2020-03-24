<?php
	# form to delete the country
	include("MySQLconnection.php");
	$stmt = $connectionMySQL->prepare("select idCountry,nameCountry,dn from Country;");
	$stmt->execute();
	echo "<table class='main'>
			<tr>
				<th>Country code</th>
				<th>Country name</th>
				<th colspan='2'>AD Path</th>
				<th>Delete ?</th>
			</tr>";
	while ($row = $stmt->fetch()) echo "
									<tr>
										<td class='info'>".$row["idCountry"]."</td>
										<td class='info'>".$row["nameCountry"]."</td>
										<td class='info'>".$row["dn"]."</td>
										<td class='info'><button onclick=\"modifDn('".$row["idCountry"]."')\">Modify</button></td>
										<td class='info'><button onclick=\"delCountry('".$row["idCountry"]."')\">Delete</button></td>
									</tr>";
?>
</table>
<script type="text/javascript">

	// inform the user of what deleting the country implies
	function delCountry(id){
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) {
	        	var txt = this.responseText ;
	        	// Double confirmation because you never know how clumsy the user can be
	            if(confirm(txt)){
	            	if(confirm(txt)) deleteTheCountryCompletely(id);
	            }
	        }
	    };
	    xmlhttp.open("GET","php/deletion.php?param=info&&sub=cty&&id="+id, true);
	    xmlhttp.send();
	}

	// definetly deletes the country
	function deleteTheCountryCompletely(id){
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) alert(this.responseText);
	    };
	    xmlhttp.open("GET","php/deletion.php?param=del&&sub=cty&&id="+id, true);
	    xmlhttp.send();
	    loadFormDel("cty");
	}

	// modify the country's dn in case the Active Directory architecture changes.
	function modifDn(idCountry){
		var NewDn = prompt("Enter a new path:");
		if (NewDn != null) {
			if (NewDn == '') alert("AD path must not be empty");
			else {
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
			    	if (this.readyState == 4 && this.status == 200) alert(this.responseText);
				};
				xmlhttp.open("GET","php/deletion.php?param=upd&&sub=dn&&id="+idCountry+"&&dn="+NewDn, false);
				xmlhttp.send();
				loadFormDel("cty");
			}
		}
	}
</script>