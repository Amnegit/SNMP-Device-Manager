<?php

if (isset($_POST['id']) && isset($_POST['vlan']) && isset($_POST['ip']) && isset($_POST['community'])){
	$newVlan = escapeshellarg($_POST['vlan']);
	$targetedId = escapeshellcmd($_POST['id']);
	$ip = escapeshellarg($_POST['ip']);
	$com = escapeshellarg($_POST['community']);
	//echo $ip.$ip;
	$error = shell_exec("snmpset -v 2c -c $com $ip 1.3.6.1.4.1.9.9.68.1.2.2.1.2.$targetedId  i  $newVlan 2>&1 1> /dev/null") ;
	//var_dump($_POST);
	//echo "snmpset -v 2c -c $com $ip 1.3.6.1.4.1.9.9.68.1.2.2.1.2.$targetedId  i  $newVlan 2>&1 1> /dev/null";
	//echo $error;
	if ($error == "") echo "Moved in vlan $newVlan";
	else echo "Erreur de saisie. Erreur : <br>$error";
}
else echo "let the fuck up";
?>