<?php
//.1.3.6.1.2.1.7.5.1.1 172.16.1.100
$content = "";
$error = 0;
if(isset($_POST["ip"]) && isset($_POST["com"])){
	$com = $_POST["com"];
	$ip = $_POST["ip"];
	$names = array();
	$state = array();
	$id = array();
	$vlan = array();
	$ips = array();
	$network = array();
	$stateVlans = array();
	if (!filter_var($ip, FILTER_VALIDATE_IP) === false) {
		$req1 = shell_exec("snmpwalk -v 2c -c $com $ip .1.3.6.1.2.1.2.2.1.2.2 2>&1");
		if(strstr($req1,"No Response")){
			echo $req1;
			$error = 1;
		}
		else{
			$getNameInt = shell_exec("snmpwalk -v 2c -c $com $ip .1.3.6.1.2.1.31.1.1.1.1");
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $getNameInt) as $line){
				//$fullname =  substr($fullname,0,2).substr($fullname,-4,4);
				$names[] =explode('"',$line)[1];
				$idx = explode(" ",$line)[0];
				$id[] = explode(".",$idx)[11];
				
			}
			
			$getIP = shell_exec("snmpwalk -v 2c -c $com $ip .1.3.6.1.2.1.4.20.1.2");
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $getIP) as $line){
				$iptmp = explode(" ",$line)[0];
				$ipz = str_replace("iso.3.6.1.2.1.4.20.1.2.","",$iptmp);
				$idz =  trim(explode(" ",$line)[3]);
				$ips[$idz][]= $ipz;
			}
			
			$getMask = shell_exec("snmpwalk -v 2c -c $com $ip .1.3.6.1.2.1.4.20.1.3");
			//echo $getMask;
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $getMask) as $line){
				$masktmp = explode(" ",$line);
				$ipz = str_replace("iso.3.6.1.2.1.4.20.1.3.","",$masktmp[0]);
				$mask = $masktmp[3];
				foreach($id as $ID){
					if($ips[$ID][0] == $ipz) $ips[$ID][] = $mask;
				}
			}
			
			foreach($id as $ID){
				if($ips[$ID]){
					$network[] = $ips[$ID];
				}
				else $network[] = array("-No ip set-","-No mask set-");
			}
			
			//var_dump($ips);
			
			foreach($id as $ID){
				$getVlan = shell_exec("snmpwalk -v 2c -c $com $ip 1.3.6.1.4.1.9.9.68.1.2.2.1.2.".$ID);
				if(strstr($getVlan,"No Such Instance")) $vlan[] = "-No Vlan Associed-";
				else $vlan[] = trim(explode(' ',$getVlan)[3]);
			}
			
			
			
			$getState = shell_exec("snmpwalk -v 2c -c $com $ip .1.3.6.1.2.1.2.2.1.8");
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $getState) as $line){
				$s = explode(' ',$line)[3];
				if($s == 1) $s = "#00FF00";
				else if($s == 2) $s = "red";
				else $s = "grey";
				$state[] = $s;
			}
			
			$get_Vlan = shell_exec("snmpwalk -v 2c -c $com $ip 1.3.6.1.4.1.9.9.46.1.3.1.1.2");
			foreach(preg_split("/((\r?\n)|(\r\n?))/", $get_Vlan) as $line){
				$tmp = explode(' ', $line);
				$vlan_2 = end(explode('.', $tmp[0]));
				$state_2 = end($tmp);
				if ($state_2 == 1) $stateVlans[$vlan_2] = "#00FF00";
				else $stateVlans[$vlan_2] = "red";
			}
			$content = "ip";
			//print_r($names);
			//print_r($state);
			//print_r($id);
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
	<style type="text/css" src="style.css"></style>
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
<?php if($content == "") echo '<h1 class="cover-heading">Enter ip address</h1>'; ?>
			<?php if(!isset($_POST["ip"]) || $error == 1){ ?>
			<form action="" method="post">
				<input name="ip" type="text" placeholder="X.X.X.X" style="color:black"/><br>
				<input name="com" type="txt" placeholder="Community" style="color:black"/> <br><br>
				<button class="btn btn-lg btn-default" type="submit">Get infos</button><br><br><br>
			</form>
			<?php } ?>
<?php
	if($content == "ip"){
		echo "<table border=1>";
		if((count($names)-1)%2 == 0) $size = (count($names)-1)/2;
		else $size = count($names)/2;
		echo "<tr>";
		for($i = 0;$i<$size;$i++){
			echo "<th>".$names[$i]."</th>";
		}
		echo "</tr><tr>";
		for($i = 0;$i<$size;$i++){
			//$popup = "Vlan : ".$vlan[$i]." - IP Address : ".$network[$i][0]." - NetMask :".$network[$i][1];
			//echo '<td style="height:30px;background-color:'.$state[$i].'" onclick="alert(\''.$popup.'\')"></td>';
			//echo '<td style="height:30px;background-color:'.$state[$i].'" data-toggle="tooltip" data-placement="top" title="'.$popup.'</td>';
			echo "<td style='height:30px;background-color:".$state[$i]."' id='".$id[$i]."' onclick='showInfos(\"".$id[$i]."\")' vlan='".$vlan[$i]."' ip='".$_POST['ip']."' sip='".$network[$i][0]."' nom='".$names[$i]."' netmask='".$network[$i][1]."' community='".$_POST['com']."'></td>";
			
		}
		
		echo "</tr><tr>";
		for($i = $size;$i<($size*2);$i++){
			//$popup = "Vlan : ".$vlan[$i];
			//echo '<td style="height:30px;background-color:'.$state[$i].'" onclick="alert(\''.$popup.'\')"></td>';
			echo "<td style='height:30px;background-color:".$state[$i]."' id='".$id[$i]."' onclick='showInfos(\"".$id[$i]."\")' vlan='".$vlan[$i]."' ip='".$_POST['ip']."' sip='".$network[$i][0]."' nom='".$names[$i]."' netmask='".$network[$i][1]."' community='".$_POST['com']."'></td>";
		}
		echo "</tr><tr>";
		for($i =$size;$i<($size*2);$i++){
			echo "<th>".$names[$i]."</th>";
		}
		echo "</table>";
	}
?>

<?php 
//if(!isset($_POST["ip"]) || $error == 1){
?>

<!-- A mettre ou tu veux -->
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
//}
		echo "<br><br><table border=1>
			<tr><th>Vlan ID</th><th>State</th></tr>";
		foreach($stateVlans as $vlan=>$color){
			if($vlan!=""){
				echo "<tr><td>VLAN $vlan</td><td style='background-color:$color;height:30px'></td></tr>";
			}
		}
		echo "</table>";


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