<?php
session_start();
$config = false;
$indexers = false;
$indexersprop = false;
if(is_file("../conf/config.php")){
	$config = true;
	require("../conf/config.php");
	if(isset($_POST['usr']) && isset($_POST['pwd'])){
		$u = escape_query($_POST['usr']);
		$p = escape_query($_POST['pwd']);
		$at = new AUTH($u,$p);
		if($at->checkToken()){
			$_SESSION['authtoken'] = serialize($at);
		}
	}
	elseif(isset($_POST['cpwd']) && isset($_SESSION['authtoken'])){
		$at = unserialize($_SESSION['authtoken']);
		$p = escape_query($_POST['cpwd']);
		$at->confirm($p);
	}
	if(isset($_SESSION['authtoken'])){
		$at = unserialize($_SESSION['authtoken']);
		if(!$at->checkToken() && $at->info[1] !="confirm"){
			header("location: logout.php");
		}
		elseif($at->checkToken()){
			if(is_file("../conf/indexsites.db")){
				$indexers = true; //check for indexers was good
				$inxs = file_get_contents("../conf/indexsites.db");
				$indexsites = unserialize($inxs);
				if(is_array($indexsites)){
					$indexersprop = true; //check for indexsites class was good
				}
			}
			
			$conf = new CONFIG;
			$sab = $conf->getSab();			
			$error = false;
		}
	}
	else{
		$error = true;
	}
	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Plexcloud - Music | Manage</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/excite-bike/jquery-ui.min.css" rel="stylesheet" type="text/css"></link>
<link href="../conf/style.css" rel="stylesheet" type="text/css"></link>
<script>
  $(function() {
    $( "#dialog" ).dialog();
  });
  $(function() {
		$("input[type=submit], button, a.button" )
		  .button();
	 });
</script>
</head>

<body>
<?php 
if($at ==NULL || (!$at->checkToken() && $at->info[1]!="confirm")){ ?>
	<div id="dialog" title="Login">
	  <form method="post" enctype="multipart/form-data">
      		<label>Username:<br />
                <input type="text" name="usr" value="" />
            </label>
            <br />
            <label>Password:<br />
                <input type="password" name="pwd" value="" />
            </label>
                <br />
                <input type="submit" value="Login" />
      </form>
	</div> <?php
}
elseif(isset($at) && $at->info[1]=="confirm"){ ?>
	<div id="dialog" title="Confirm new credentials">
	  <form method="post" enctype="multipart/form-data">
      		<label>Username:<br />
                <input disabled type="text" name="usr" value="<?php echo $at->getUsername(); ?>" />
            </label>
            <br />
            <label>Confirm Password:<br />
                <input type="password" name="cpwd" value="" />
            </label>
                <br />
                <input type="submit" value="Login" />
      </form>
	</div> <?php
} 
else{
	include("settings.php");
} ?>
<div id="info">
<?php
	$notify=false;
	if(isset($_SESSION['response'])){
		echo "<p>".$_SESSION['response']."</p>";
		unset($_SESSION['response']);
		$notify=true;
	}
	if($config === false){
		echo "<h3>Improper installation. Missing config.php</h3>";
	}
echo "auth Info: ";
var_dump($at->info);
echo "<br>error: ";
var_dump($error);
echo "confInfo: ";
var_dump($conf->info);
if($notify){ ?>
   	<script type="text/javascript">
		$(function() {
			$("#info").show();
			$( "#info" ).dialog();
		});
  </script>
<?php } ?>
</div>
</body>
</html>