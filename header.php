<!DOCTYPE html>
<html lang="en">
<head>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
	<meta charset="UTF-8">
	<title>FauxDB backend</title>
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700,400italic,700italic|Oswald:700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/app.css" />
</head>
<body class="<? if (isset($body_class)) { echo $body_class; } ?>">
<?
if (isset($_SESSION['msg'])) { ?>	
	<div data-alert class="alert-box text-center <?=$_SESSION['msg']->type?>">
	<? 
	// make sure this actuals displays (maybe it needs a show()? )
	echo $_SESSION['msg']->msg; unset($_SESSION['msg']); ?>
	<a href="#" class="close">&times;</a>
	</div>
<? } ?>
<div class="row">
	<div class="small-12 columns">
		<div class="logotext">
			<a href="/fauxdb/admin.php"><img src="/fauxdb/img/fauxdb_logo.png" class="logo" />
			<img src="/fauxdb/img/fauxdb_logotype_white.png" class="logotext" /></a>
		</div>
	</div>
</div>