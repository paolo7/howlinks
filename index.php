<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
	require_once( "sparql.php" );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>HowLinks - Get information about "How to" process</title>

		<!-- CSS Files -->
		<link type="text/css" href="./Tests/css/base.css" rel="stylesheet" />
		<link type="text/css" href="./Tests/css/Spacetree.css" rel="stylesheet" />

		<!--[if IE]><script language="javascript" type="text/javascript" src="../../Extras/excanvas.js"></script><![endif]-->

		<!-- JIT Library File -->
		<script language="javascript" type="text/javascript" src="./Jit/jit.js"></script>

		<!-- Example File -->
		<script language="javascript" type="text/javascript" src="tree.js"></script>
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

	</head>
	
	<?php
		if(isset($_GET["page"])){
			$page=$_GET['page'];
		}
		else $page='';
		
		switch($page) {
			case 'portal': include('portal.php'); break;
			case 'view': include('view.php'); break;
			default : include('portal.php'); break;
		}
	?>
</html>