<?php
include("excel.php");
$excel = getExcel('/var/www/html/rezo/Hosts.xlsx','/var/www/html/rezo/xls/');
$vlans = array();
for($i=0;$i<count($excel);$i++){
	foreach($excel[$i] as $aa => $bb){
		if(strstr($aa,"VLAN")) $vlane = $bb;
		$change = 0;
		foreach($vlans as $vlan){
			if($vlane == $vlan) $change++;
		}
		if($change == 0 && $vlane!=""){
			$vlans[] = $vlane;
		}
	}
}
//var_dump($excel);
//var_dump($vlans);
if(isset($_POST["vlan"])){
	for($i=0;$i<count($excel);$i++){
		$data = var_export($excel[$i],true);
		if(strstr($data,$_POST["vlan"])){
			if(strstr($data,"DISPO")){
				//echo $data."111111111";
				foreach($excel[$i] as $ee=>$uu){
					if(!strstr($ee,"VLAN") && !strstr($uu,"DIS")) $ip = $uu;
				}
			}
		}
	}
	echo $ip;
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="http://icones.gratuites.web.free.fr/data/Icon%20Vista%20Pack/switch.ico">
    <title>Switch Finder</title>
    <link href="./Cover Template for Bootstrap_files/bootstrap.min.css" rel="stylesheet">
    <link href="./Cover Template for Bootstrap_files/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <link href="./Cover Template for Bootstrap_files/cover.css" rel="stylesheet">
    <script src="./Cover Template for Bootstrap_files/ie-emulation-modes-warning.js"></script><style type="text/css"></style>
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
                  <li><a href="http://getbootstrap.com/examples/cover/#">Home</a></li>
                  <li class="active"><a href="sip.php">IP </a></li>
                  <li><a href="http://getbootstrap.com/examples/cover/#">Contact</a></li>
                </ul>
              </nav>
            </div>
          </div>

          <div class="inner cover">
            <h1 class="cover-heading">Get a static ip for a specifique vlan</h1>
			<form action="" method="post">
				<select style="color:black" name="vlan">
<?php foreach($vlans as $vlan){echo "<option style='color:black' value='$vlan'>$vlan</option>";} ?>
				</select><br><br>
				<button class="btn btn-lg btn-default" type="submit">Get Static Ip</button><br><br><br>
			</form>
<?php if(isset($ip)) echo "<h2>IP disponible sur le ".$_POST["vlan"]." :<br><br>$ip</h2>"; ?>
		  
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
  

</body></html>