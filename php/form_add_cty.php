<!-- form to add a country -->
<div>
	<table>
		<tr>
			<td>ID (2 uppercase letters)</td>
			<td><input type="text" id="ctyId" size='1'/></td>
			<td rowspan="4" id="result"></td>
		</tr>
		<tr>
			<td>Name</td>
			<td><input type="text" id="ctyName"/></td>
		</tr>
		<tr>
			<td>Region</td>
			<td>
				<select id="reg">
					<option value="N">North</option>
					<option value="S">South</option>
					<option value="E">East</option>
					<option value="W">West</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><button onclick="addCountry()">Add</button></td>
		</tr>
	</table>
</div>
<script>
function addCountry() {
	document.getElementById("result").innerHTML = "";
	var ctyId = document.getElementById("ctyId").value;
	var ctyName = document.getElementById("ctyName").value;
	var reg = document.getElementById("reg").value;
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("result").innerHTML = this.responseText;
            document.getElementById("ctyId").value ="";
            document.getElementById("ctyName").value ="";
        }
    };
    xmlhttp.open("GET","php/checkCty.php?idCty="+ctyId+"&&nameCty="+ctyName+"&&reg="+reg, true);
    xmlhttp.send();
}
</script>