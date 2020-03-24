<?php
	# Erase the default country cookie
	if(setcookie("defaultCountry","",time()-1,"../")) echo "cookie added";
	if (isset($_COOKIE["defaultCountry"])) echo "<br/>Still here: ".$_COOKIE["defaultCountry"];
?>