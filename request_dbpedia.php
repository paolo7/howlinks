<?php
	function look_for_step_for_step($nodeid,$db_link,$db_dbpedia,$limit) {
		$nodeid=explode('---',$nodeid)[1];
		$step_children=array();
		$query="PREFIX prohow: <http://vocab.inf.ed.ac.uk/prohow#>

				SELECT ?step ?exact

				WHERE {
					<".$nodeid.">  prohow:has_step ?step .
					 ?step rdfs:label ?exact .
				}";
		if($limit>=0) $query.=" limit ".$limit;
		$result=sparql_request($query,$db_link);
		
		while( $row = sparql_fetch_array( $result ) )
		{
			$step_child= array(
				'id' => $row['step'],
				'name' => $row['exact'],
				'annotation' => 'step',
				'image' => '',
				'children' => array()
			);
			$step_children[] = $step_child;
		}
		if(sparql_num_rows($result) > 5) {
			$fake_node=array();
			$fake_node[] = array(
							'id' => 'http_extension',
							'name' => sparql_num_rows($result).' Steps',
							'annotation' => 'classifier_step',
							'image' => '',
							'children' => $step_children
			);
			return $fake_node;
		}
		return $step_children;
	}
	function look_for_methods_for_step($nodeid,$db_link,$db_dbpedia,$limit) {
		$nodeid=explode('---',$nodeid)[1];
		$step_children=array();
		$query="PREFIX prohow: <http://vocab.inf.ed.ac.uk/prohow#>

				SELECT ?step ?exact

				WHERE {
					<".$nodeid.">  prohow:has_method ?step .
					 ?step rdfs:label ?exact .
				}";
		if($limit>=0) $query.=" limit ".$limit;
		$result=sparql_request($query,$db_link);
		
		while( $row = sparql_fetch_array( $result ) )
		{
			$step_child= array(
				'id' => $row['step'],
				'name' => $row['exact'],
				'annotation' => 'method',
				'image' => '',
				'children' => array()
			);
			$step_children[] = $step_child;
		}
		if(sparql_num_rows($result) > 5) {
			$fake_node=array();
			$fake_node[] = array(
							'id' => 'http_extension',
							'name' => sparql_num_rows($result).' Steps',
							'annotation' => 'classifier_step',
							'image' => '',
							'children' => $step_children
			);
			return $fake_node;
		}
		return $step_children;
	}
?>