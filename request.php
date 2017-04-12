<?php
	require_once('sparql.php');
	require_once('request_steps.php');
	require_once('request_requirements.php');
	require_once('request_input_output.php');
	require_once('request_dbpedia.php');
	require_once('security.php');	
	// find methods of a step
	function look_for_method($nodeid,$db_link,$db_dbpedia) {
		
		if (strpos($nodeid, '---') !== false) $nodeid=explode('---',$nodeid)[1];
		
		$children=array();
		$result=sparql_request("PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
								PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
								PREFIX prohow: <http://w3id.org/prohow#>

								SELECT ?method ?exact
								
								WHERE {
									<".$nodeid."> prohow:has_method ?method .
									?method rdfs:label ?exact .

								}",$db_link);
						
		while( $row = sparql_fetch_array( $result ) )
		{
			$child= array(
				'id' => $row['method'],
				'name' => $row['exact'],
				'annotation' => 'method',
				'image' => '',
				'dbpedia' => '',
				'children' => array()
			);
			$children[] = $child;
		}
		if(sparql_num_rows($result) > 5) {
			$fake_node=array();
			$fake_node[] = array(
							'id' => 'http_extension',
							'name' => sparql_num_rows($result).' Methods',
							'annotation' => 'classifier_method',
							'image' => '',
							'dbpedia' => '',
							'children' => $children
			);
			return $fake_node;
		}
		return $children;
	}
	// sparql request depending on the type of node
	
	$nodeid=$_GET['nodeid'];
	
	if(!isset($_GET["annotation"]))
		$annotation='method';
	else
		$annotation=$_GET['annotation'];
		
	if(!isset($_GET["dbpedia"]))
		$dbpedia='';
	else
		$dbpedia=$_GET['dbpedia'];
		
	$children=array();
	
	//$nodeid=escape_string($nodeid);
	//$dbpedia=escape_string($dbpedia);

	$tmp=look_for_method($nodeid,$db1,$db2);
	foreach ($tmp as $value) { $children[] = $value; }
	$tmp=look_for_step($nodeid,$db1,$db2);
	foreach ($tmp as $value) { $children[] = $value; }
	$tmp=look_for_requirements($nodeid,$db1,$db2);
	foreach ($tmp as $value) { $children[] = $value; }

	switch($annotation) {
		
		case 'step':
		#	$tmp=look_for_step($nodeid,$db1,$db2,$solr);
		#	foreach ($tmp as $value) { $children[] = $value; }
		break;
		case 'input':
			$tmp=look_for_dbpedia($nodeid,$db1,$db2);
			foreach ($tmp as $value) { $children[] = $value; }
		case 'output':
			if(strlen($dbpedia)>0) {
				$tmp=look_for_dbpedia_input($nodeid,$dbpedia,$db1,$db2);
				foreach ($tmp as $value) { $children[] = $value; }
				$tmp=look_for_dbpedia_output($nodeid,$dbpedia,$db1,$db2,5);
				foreach ($tmp as $value) { $children[] = $value; }
		#		$tmp=look_for_step($nodeid,$db1,$db2,$solr);
		#		foreach ($tmp as $value) { $children[] = $value; }
			}
		break;
		case 'process':
		case 'supplier':
		case 'step_process':
		case 'supplied':
			$tmp=look_for_output($nodeid,$db1,$db2);
			foreach ($tmp as $value) { $children[] = $value; }
		#	$tmp=look_for_method($nodeid,$db1,$db2);
		#	foreach ($tmp as $value) { $children[] = $value; }
		case 'method':
			#$tmp=look_for_step($nodeid,$db1,$db2,$solr);
			#foreach ($tmp as $value) { $children[] = $value; }
			#$tmp=look_for_requirements($nodeid,$db1,$db2);
			#foreach ($tmp as $value) { $children[] = $value; }
		break;
		case 'requirement':
			$tmp=look_for_dbpedia($nodeid,$db1,$db2);
			foreach ($tmp as $value) { $children[] = $value; }
			$tmp=look_for_group_of_ingredients($nodeid,$db1,$db2);
			foreach ($tmp as $value) { $children[] = $value; }
		break;
	}
	
	$obj=array(
		'id' => $nodeid,
		'name' => $nodeid,
		'children' => $children
	);
	
	$tree = json_encode($obj);
	echo $tree;
?>
