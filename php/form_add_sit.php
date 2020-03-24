<!-- Form to add a site-->
<div>
	<table>
		<tr>
			<td>Country</td>
			<td>
				<select id="cty">
<?php
	include("MySQLconnection.php");
	$stmt = $connectionMySQL->prepare("select idCountry,nameCountry from Country;");
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		echo "<option value='".$row[0]."'>".$row[1]."</option>\r\n";
	}
?>
				</select>
			</td>
			<td id="message" rowspan='4'></td>
		</tr>
		<tr>
			<td>Group username (site ID)</td>
			<td><input type="text" id="sitGroup" onkeyup="changeDN(this.value)"></td>
		</tr>
		<tr>
			<td>Site name</td>
			<td><input type="text" id="sitName"></td>
		</tr>
		<tr>
			<td>Group path</td>
			<td id="autosize">
				<input type='text' size='130' id='cn' value="CN=XXXXX-AP-Phonebook,OU=Application-Groups (L),OU=Groups,OU=ONE,OU=FR,OU=_Organisation,DC=moviantogroup,DC=com" onkeyup='AutoSize(this.value)'>
			</td>
		</tr>
		<tr>
			<td>
				<button onclick="addSite()">Add</button>
			</td>
		</tr>
	</table>
</div>
<script>
function addSite() {
	document.getElementById("message").innerHTML = "";
	var sitGroup = document.getElementById("sitGroup").value;
	var sitName = document.getElementById("sitName").value;
	var cty = document.getElementById("cty").value;
	var cn = document.getElementById("cn").value;
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("message").innerHTML = this.responseText;
            document.getElementById("sitName").value ="";
            document.getElementById("sitGroup").value ="";
            document.getElementById("cn").value ="";
        }
    };
    xmlhttp.open("GET","php/checkSit.php?group="+sitGroup+"&&name="+sitName+"&&cty="+cty+"&&cn="+cn, true);
    xmlhttp.send();
}


// this is a little "autosize" function so the input field use to enter the site's cn get the size of it's content.
function AutoSize(cn){
	var sizeForInput = cn.length*1.2;
	var adaptedInput ="<input type='text' size='"+sizeForInput+"' value='"+cn+"' id='cn' onchange='AutoSize(this.value)'>";
	document.getElementById("autosize").innerHTML = adaptedInput;
}

function changeDN(dn){
	var adaptedInput ="<input type='text' size='130' value='CN="+dn+",OU=Application-Groups (L),OU=Groups,OU=ONE,OU=FR,OU=_Organisation,DC=moviantogroup,DC=com' id='cn' onchange='AutoSize(this.value)'>";
	document.getElementById("autosize").innerHTML = adaptedInput;
}
</script>