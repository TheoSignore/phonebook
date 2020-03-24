<?php
# Connection to the O&M Movianto database. In the case of an error during the connection process, see ../logs/LdapConnection.txt
	$log = fopen("LdapConnection.txt","a+");
	$msg = "\r\n>".date('Y-m-d G:i:s')." - LdapConnection.php - script to connect \"O&M Movianto AD database\".\r\n";
	fputs($log,$msg);

	function connectAndBind($server,$login,$password,$log){
		$connectionLdap = ldap_connect($server);
		if (!$connectionLdap){
			fputs($log,"Connection to ".$server." AD database failed.\r\n");
			return false;
		}
		else {
			fputs($log,"Succesfuly connected to ".$server." AD database.\r\n");
			$bindLdap = ldap_bind($connectionLdap,$login,$password);
			if(!$bindLdap){
				$msg = "Binding failed to ".$server."\r\nError ".ldap_errno($connectionLdap)."\r\nldap_error: ".ldap_error($connectionLdap)."\r\n";
				ldap_get_option($connectionLdap, LDAP_OPT_DIAGNOSTIC_MESSAGE, $error);
				$msg .= "ldap_get_option: $error \r\n";
			    $msg .= "ID used: ".ldap_dn2ufn($login)."\r\n>>".$login."\r\n";
			    fputs($log,$msg."\r\n");
			    return false;
			}
			else {
				fputs($log,"Succesfuly binded to AD database.\r\n");
				return array(1=>$connectionLdap,2=>$bindLdap);
				
			}
		}
	}

	function xor_this($string,$key) {
	    $text = $string;
	    $outText = '';
	    for($i=0; $i<strlen($text);){
	        for($j=0; ($j<strlen($key) && $i<strlen($text)); $j++,$i++){
	            $outText .= $text{$i} ^ $key{$j};
	        }
	    }
	    return $outText;
	}

	

	$server = "SFRONEPWDC01.moviantogroup.com";
	$server2 = "sdefr2pwdc02.moviantogroup.com";
	$loginLdap = "CN=FRONE-svcPrinter,OU=Service-Accounts,OU=Technical-Accounts,OU=ONE,OU=FR,OU=_Organisation,DC=moviantogroup,DC=com";
	fclose($log);
	include("MySQLconnection.php");
	$log = fopen("LdapConnection.txt","a+");
	$sql = "SELECT wpaKey FROM Password WHERE idwpa = 2";
	$stmt = $connectionMySQL->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch();

	$passwordLdap = xor_this($row["wpaKey"],"1ATeqP2Q6ua8QOVq");

	$ldap = connectAndBind($server,$loginLdap,$passwordLdap,$log);
	if ($ldap) {
		$connectionLdap = $ldap[1];
		$bindLdap = $ldap[2];
	}
	else {
		$ldap = connectAndBind($server2,$loginLdap,$passwordLdap,$log);
		if ($ldap) {
			$connectionLdap = $ldap[1];
			$bindLdap = $ldap[2];
		}
	}
	fclose($log);
	
?>