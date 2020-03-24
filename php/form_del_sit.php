<!-- Form to delete a site-->
<table>
	<tr>
		<td>Select a country<td>
		<td>
<?php
	include("MySQLconnection.php");
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
<script type="text/javascript">
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

	// displays information about what a site deletion implies
	function delSite(id){
		var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() {
	        if (this.readyState == 4 && this.status == 200) {
	        	var txt = this.responseText ;
	        	// double confirmation to be sure the user is sure.
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

	// definetly deletes the site, the departments and the user it containes
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

	// Modifies the site's cn in case the Active Directory architectures changes
	function modifCn(idGroup){
		var NewCn = prompt("Enter a new path:");
		if (NewDn != null) {
			if (NewCn == '') alert("AD path must not be empty");
			else {
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
			    	if (this.readyState == 4 && this.status == 200) alert(this.responseText);
				};
				xmlhttp.open("GET","php/deletion.php?param=upd&&sub=cn&&id="+idGroup+"&&cn="+NewCn, false);
				xmlhttp.send();
				loadFormDel("sit");
			}
		}
	}
</script>