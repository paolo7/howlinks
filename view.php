<?php
#error_reporting(E_ALL);
#ini_set('display_errors', 1);
	require_once('security.php');
	if(isset($_GET["link"])){
		$link=$_GET['link'];
	}
	else $link='';
	
	if(isset($_GET["exact"])){
		$exact= $_GET['exact'];
	}
	else $exact='';
	
	#$link = escape_string(str_replace('%3', '&', $link));
	
	#$link='http://vocab.inf.ed.ac.uk/procont#?url='.$link;
	echo '<body onload="init(\''.$link.'\',\''.addslashes($exact).'\');">';
	?>

		<div style="width:100%;height:75px;background-color:white;padding-top:10px;padding-bottom:10px;
			color: #444444;
			background-color: #ddd;
			background-image: linear-gradient(#E5E5E5, #CFCFCF);
			box-shadow: -1px 2px 5px 1px rgba(0, 0, 0, 0.7);">
			<div style="width:100px;height:75px;margin-left:10px;float:left;"> 
				<a href="index.php"><img src="logo.png" width="100%" height="100%"/></a>
			</div>
			<div style="width:50%;height:100%;float:right;"> 
				<a href="index.php"><img src="legend.png" width="100%" height="100%"/></a>
			</div> 
		</div>
		
		<div id="graph" style="width:100%;height:100%;"> 
			<div id="infovis" style="width:100%;height:100%;"></div>   
		</div> 
		
		<div id="infobox" style="position:absolute;z-index:100;width:300px;height:100px;visibility:hidden;
			color: #444444;
			background-color: #ddd;
			background-image: linear-gradient(#E5E5E5, #CFCFCF);font-size:10px;
			box-shadow: -1px 2px 5px 1px rgba(0, 0, 0, 0.7);text-align:left;left:10px;padding:10px 10px 10px 10px;">
			<b><u>Info box</b></u>
		</div>
		<div id="loading" style="position:absolute;z-index:100;width:300px;height:100px;visibility:hidden;
			color: #444444;
			background-color: #ddd;
			background-image: linear-gradient(#E5E5E5, #CFCFCF);font-size:10px;
			box-shadow: -1px 2px 5px 1px rgba(0, 0, 0, 0.7);text-align:left;left:10px;padding:10px 10px 10px 10px;">
			<center><img src="loading.gif" width="100px" height="100px"/></center>
		</div>
				
	</body>
</html>

