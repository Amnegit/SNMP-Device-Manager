<?php
//api qui va permettre d'effectuer une action depuis les ports
// ici c'est pour assigner un vlan à un port mais on peut imaginer faire des fonctions de ce style pour n'importe quoi



if (isset($_POST['id']) && isset($_POST['vlan']) && isset($_POST['ip']) && isset($_POST['community'])){
	//récupérations des champs du port
	$newVlan = escapeshellarg($_POST['vlan']);
	$targetedId = escapeshellcmd($_POST['id']);
	$ip = escapeshellarg($_POST['ip']);
	$com = escapeshellarg($_POST['community']);
	echo $ip.$ip;
	//envoie la commande avec l'oid pour set le vlan du port
	$error = shell_exec("snmpset -v 2c -c $com $ip 1.3.6.1.4.1.9.9.68.1.2.2.1.2.$targetedId  i  $newVlan 2>&1 1> /dev/null") ;
//debug	echo "snmpset -v 2c -c $com $ip 1.3.6.1.4.1.9.9.68.1.2.2.1.2.$targetedId  i  $newVlan 2>&1 1> /dev/null";
	echo $error;
	if ($error == "") echo "Moved in vlan ".htmlentities($newVlan);
	else echo "Erreur de saisie. Erreur : <br>".htmlentities($error);
}
else echo "<img src='http://seveninchesofyourtime.com/wp-content/uploads/2014/05/Yawning-Red-Panda.jpg' style='width:100%;height:100%'/>";//degage
?>