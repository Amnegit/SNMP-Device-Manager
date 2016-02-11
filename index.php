<?php
$affiche = ""; //si l'on doit générer un tableau html ou pas
$error = 0; //en cas d'erreur gérer l'affichage
if(isset($_POST["ip"]) && isset($_POST["com"])){
	$com = escapeshellarg($_POST["com"]);
	$ip = $_POST["ip"];
	$names = array();//tableau qui va contenir le nom des ports
	$state = array(); //récupère un tableau avec si le port est down (rouge) ou up (vert)
	$id = array(); //id des ports sur le switch
	$vlan = array(); //vlan du switch
	$ips = array(); //ip du port
	$network = array(); //tableau qui pour chaque case contient array(IP,NETMASK)
	$stateVlans = array(); //disponibilité du vlan sur le switch gratz Leo

	if (!filter_var($ip, FILTER_VALIDATE_IP) === false) {//ccontrôle si l'ip est bien rentrée
		$req1 = shell_exec("snmpwalk -v 2c -c $com $ip .1.3.6.1.2.1.2.2.1.2.2 2>&1"); //oid pris au hasard
		if(strstr($req1,"No Response")){ //le serveur est injoignable, le programme ne fais pas d'action et affiche un message d'erreur
			echo $req1;
			$error = 1;
		}
		else{
			//parse la commande et génère le tableau $id
			$getNameInt = shell_exec("snmpwalk -v 2c -c $com $ip .1.3.6.1.2.1.31.1.1.1.1");
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $getNameInt) as $line){
				$names[] =explode('"',$line)[1];
				$idx = explode(" ",$line)[0];
				$id[] = explode(".",$idx)[11]; 
				
			}
			
			//parse la commande et génère le tableau $ips
			$getIP = shell_exec("snmpwalk -v 2c -c $com $ip .1.3.6.1.2.1.4.20.1.2");
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $getIP) as $line){
				$iptmp = explode(" ",$line)[0];
				$ipz = str_replace("iso.3.6.1.2.1.4.20.1.2.","",$iptmp); //récupère l'id du port
				$idz =  trim(explode(" ",$line)[3]);
				$ips[$idz][]= $ipz;
			}
			
			//parse la commande et génère le tableau $ips
			$getMask = shell_exec("snmpwalk -v 2c -c $com $ip .1.3.6.1.2.1.4.20.1.3");
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $getMask) as $line){
				$masktmp = explode(" ",$line);
				$ipz = str_replace("iso.3.6.1.2.1.4.20.1.3.","",$masktmp[0]);
				$mask = $masktmp[3];
				foreach($id as $ID){
					if($ips[$ID][0] == $ipz) $ips[$ID][] = $mask;
				}
			}
			
			//si une ip x.x.x.x est contenu dans $id et $ips alors remplir de ces infos le tableau $network
			foreach($id as $ID){
				if($ips[$ID]){
					$network[] = $ips[$ID];
				}
				else $network[] = array("-No ip set-","-No mask set-");
			}
			
			//parse la commande et génère le tableau $vlan
			foreach($id as $ID){
				$getVlan = shell_exec("snmpwalk -v 2c -c $com $ip 1.3.6.1.4.1.9.9.68.1.2.2.1.2.".$ID);
				if(strstr($getVlan,"No Such Instance")) $vlan[] = "-No Vlan Associed-";
				else $vlan[] = trim(explode(' ',$getVlan)[3]);
			}
			
			
			//parse la commande et génère le tableau $state
			$getState = shell_exec("snmpwalk -v 2c -c $com $ip .1.3.6.1.2.1.2.2.1.8");
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $getState) as $line){
				$s = explode(' ',$line)[3];
				if($s == 1) $s = "#00FF00";
				else if($s == 2) $s = "red";
				else $s = "grey";
				$state[] = $s;
			}
			
			//parse la commande et génère le tableau $stateVlans 
			$get_Vlan = shell_exec("snmpwalk -v 2c -c $com $ip 1.3.6.1.4.1.9.9.46.1.3.1.1.2");
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $get_Vlan) as $line){
				$tmp = explode(' ', $line);
				$vlan_2 = end(explode('.', $tmp[0]));
				$state_2 = end($tmp);
				if ($state_2 == 1) $stateVlans[$vlan_2] = "#00FF00";
				else $stateVlans[$vlan_2] = "red";
			}

			//on dit d'afficher
			$affiche = "ip";
		}
	} 
	else {
		echo htmlentities($ip)." is not a valid IP address";
		$error = 1;
	}
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<script src="http://code.jquery.com/jquery-2.2.0.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <link rel="icon" href="http://icones.gratuites.web.free.fr/data/Icon%20Vista%20Pack/switch.ico">
    <title>Switch Finder</title>
    <link href="./Cover Template for Bootstrap_files/bootstrap.min.css" rel="stylesheet">
    <link href="./Cover Template for Bootstrap_files/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <link href="./Cover Template for Bootstrap_files/cover.css" rel="stylesheet">
	
	

    <script src="./Cover Template for Bootstrap_files/ie-emulation-modes-warning.js"></script>
	<style type="text/css">a.tooltip {
    outline: none;
    text-decoration: none;
    border-bottom: dotted 1px blue;
    position: relative;
}

a.tooltip strong {
    line-height: 30px;
}

a.tooltip > span {
    width: 300px;
    padding: 10px 20px;
    margin-top: 0;
    margin-left: -120px;
    opacity: 0;
    visibility: hidden;
    z-index: 10;
    position: absolute;
    font-family: Arial;
    font-size: 12px;
    font-style: normal;
    border-radius: 3px;
    box-shadow: 2px 2px 2px #999;
    -webkit-transition-property: opacity, margin-top, visibility, margin-left;
    -webkit-transition-duration: 0.4s, 0.3s, 0.4s, 0.3s;
    -webkit-transition-timing-function: ease-in-out, ease-in-out, ease-in-out, ease-in-out;
    transition-property: opacity, margin-top, visibility, margin-left;
    transition-duration: 0.4s, 0.3s, 0.4s, 0.3s;
    transition-timing-function: 
        ease-in-out, ease-in-out, ease-in-out, ease-in-out;
}

/*a.tooltip > span:hover,*/
a.tooltip:hover > span {
    opacity: 1;
    text-decoration: none;
    visibility: visible;
    overflow: visible;
    margin-top: 50px;
    display: inline;
    margin-left: -90px;
}

a.tooltip span b {
    width: 15px;
    height: 15px;
    margin-left: 40px;
    margin-top: -19px;
    display: block;
    position: absolute;
    -webkit-transform: rotate(-45deg);
    -moz-transform: rotate(-45deg);
    -o-transform: rotate(-45deg);
    transform: rotate(-45deg);
    -webkit-box-shadow: inset -1px 1px 0 #fff;
    -moz-box-shadow: inset 0 1px 0 #fff;
    -o-box-shadow: inset 0 1px 0 #fff;
    box-shadow: inset 0 1px 0 #fff;
    display: none\0/;
    *display: none;
}    

a.tooltip > span {
	color: #000000; 
	background: #FBF5E6;
	background: -webkit-linear-gradient(top, #FBF5E6, #FFFFFF);
	background: linear-gradient(top, #FBF5E6, #FFFFFF);	    
	border: 1px solid #CFB57C;	     
}    
	  
a.tooltip span b {
	background: #FBF5E6;
	border-top: 1px solid #CFB57C;
	border-right: 1px solid #CFB57C;
}
#infoPanel{
	color:black;
	display:none;

}

</style>
  </head>

  <body>

    <div class="site-wrapper">

      <div class="site-wrapper-inner">

        <div class="cover-container">

          <div class="masthead clearfix">
            <div class="inner">
              <h3 class="masthead-brand">Cover</h3>
              <nav>
                <ul class="nav masthead-nav">
                  <li class="active"><a href="#">New search</a></li>
                  <li><a href="sip.php">Get A Static IP</a></li>
                </ul>
              </nav>
            </div>
          </div>

          <div class="inner cover">
            <h1 class="cover-heading">Enter ip address</h1>
			<?php if(!isset($_POST["ip"]) || $error == 1){ /* si le formlaire a été soummis et sans erreur*/ ?>
			<form action="" method="post">
				<input name="ip" type="text" placeholder="X.X.X.X" style="color:black"/><br>
				<input name="com" type="txt" placeholder="Community" style="color:black"/> <br><br>
				<button class="btn btn-lg btn-default" type="submit">Get infos</button><br><br><br>
			</form>
			<?php } ?>
<?php
	if($affiche == "ip"){
		$size=0; //nombre de colonnes du tableau html
		//génération du tableau
		echo "<table border=1>";
		//on coupe la taille du tableau en 2 pour que l'affichage html soit dynamique en fonction du nombre de ports du switch
		if((count($names)-1)%2 == 0) $size = (count($names)-1)/2;
		else $size = count($names)/2;
		echo "<tr>";
		//affichage de la première ligne
		for($i = 0;$i<$size;$i++){
			echo "<th>".$names[$i]."</th>";
		}
		echo "</tr><tr>";
		//affichage de la deuxième ligne
		for($i = 0;$i<$size;$i++){
			//on met toutes les options en attributs html avec les informations des tableaux pour les ports
			echo "<td style='height:30px;background-color:".$state[$i]."' id='".$id[$i]."' onclick='showInfos(\"".$id[$i]."\")' vlan='".$vlan[$i]."' ip='".$_POST['ip']."' sip='".$network[$i][0]."' nom='".$names[$i]."' netmask='".$network[$i][1]."' community='".$_POST['com']."'></td>";
			
		}
		
		echo "</tr><tr>";
		//affichage de la troisième ligne
		for($i = $size;$i<($size*2);$i++){
			echo "<td style='height:30px;background-color:".$state[$i]."' id='".$id[$i]."' onclick='showInfos(\"".$id[$i]."\")' vlan='".$vlan[$i]."' ip='".$_POST['ip']."' sip='".$network[$i][0]."' nom='".$names[$i]."' netmask='".$network[$i][1]."' community='".$_POST['com']."'></td>";
		}
		echo "</tr><tr>";
		//affichage de la quatrième ligne
		for($i =$size;$i<($size*2);$i++){
			echo "<th>".$names[$i]."</th>";
		}
		echo "</table>";
	}
?>

<?php 
//if(!isset($_POST["ip"]) || $error == 1){debug
?>
<!-- div qui va générer la popup lors du click sur un port -->
<div class="row" id="editDiv">
	<div class="col-md-12">
		<div id="infoPanel" class="panel panel-default">
		  <div class="panel-heading">
		    <div class="row">
		    	<div class="col-md-3" id="infosName"></div>
		    		<div class="col-md-1 col-md-offset-8">
		    			<span onclick="closeInfo()" class="glyphicon glyphicon-remove"></span>
		    		</div>
		    	</div>
		  	</div>
		  	<form class="form-horizontal">
				<div class="form-group">
				    <label class="col-sm-2 control-label">IP</label>
					<div class="col-sm-10">
					    <p class="form-control-static" id="infosSIP"></p>
		   			</div>
		  		</div>
		  		<div class="form-group">
		  			<label for="" class="col-sm-2 control-label">NETMASK</label> 
		  			<div class="col-sm-10">
		    			<p class="form-control-static" id="infosMASK"></p>
		    		</div>
		  		</div>
		  		<div class="form-group">
		   			<label for="inputVlan" class="col-sm-2 control-label">VLAN</label>
		    		<div class="col-sm-10">
		    	  		<input class="form-control" type="text" name="vlanID" id="infosVLAN"/>
		    		</div>
		  		</div>
		  		
		  		<span id="infosID" infosID=""></span>
				<span id="infosIP" infosIP=""></span>
		  		<span id="infosCommunity" infosCommunity=""></span>
			</form>
			<div class="row">
				<div class="col-md-2 col-md-offset-10">
					<button onclick="send()" class="btn button">Send</button>
				</div>
			</div>
			<div class="row">
				<div id="result" class="col-md-10 col-md-offset-1">
				</div>
			</div>
		</div>
	</div>
</div>
<!-- // -->

<?php
//}debug
		//affichage du tableau pour l'état des vlans
		echo "<br><br><table border=1>
			<tr><th>Vlan ID</th><th>State</th></tr>";
		foreach($stateVlans as $vlan=>$color){
			if($vlan!=""){
				echo "<tr><td>VLAN $vlan</td><td style='background-color:$color;height:30px'></td></tr>";
			}
		}
		echo "</table>";

		//affichages possibles à rajouter
?>
		  </div>
		 
          <div class="mastfoot">
            <div class="inner">
              <p>Made by our companie :)))))</p>
            </div>
          </div>

        </div>

      </div>

    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./Cover Template for Bootstrap_files/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="./Cover Template for Bootstrap_files/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="./Cover Template for Bootstrap_files/ie10-viewport-bug-workaround.js"></script>
	<script type="text/javascript">
	function showInfos(id){		
		$("#infoPanel").css("display","block");
		$("#infosSIP").html($("#"+id).attr('sip'));
		$("#infosIP").attr('infosip',$("#"+id).attr('ip'));
		$("#infosVLAN").attr('value',$("#"+id).attr('vlan'));
		$("#infosMASK").html($("#"+id).attr('netmask'));
		$("#infosName").html($("#"+id).attr('nom'));
		$("#infosID").attr('infosID',id);
		$("#infosCommunity").attr('infosCommunity',$("#"+id).attr('community'));
	}

	function closeInfo(){
		$("#infoPanel").css("display","none");
	}

	function send(){
		$.ajax({
			type:"POST",
			url:"setvlan.php",
			data:{
				"id":$("#infosID").attr('infosID'),
				"vlan":$("#infosVLAN").val(),
				"ip":$("#infosIP").attr('infosip'),
				"community":$("#infosCommunity").attr('infosCommunity')
			},
			success:function(res){
				$("#result").css("display","block");
				if(res.search("Move")!=-1){
					$("#result").html('<div class="alert alert-success" role="alert">'+res+'</div>');
				}else{
					$("#result").html('<div class="alert alert-danger" role="alert">'+res+'</div>');
				}
			}
		}); 
	}
</script>
  

</body></html>