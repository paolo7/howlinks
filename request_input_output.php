<?php

	// find processes that produce a dbpedia ingredient
	function look_for_dbpedia_input($entityURI,$dbpedia,$db_link,$db_dbpedia) {
		$entityURI=explode('---',$entityURI)[1];
		$children=array();
		$result=sparql_request("PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
								PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
								PREFIX prohow: <http://w3id.org/prohow#>

								SELECT ?method ?exact


								WHERE {
									?node rdf:type <".$dbpedia."> .
									?node prohow:has_method ?method .
									?method rdfs:label ?exact .
								}",$db_link);
		
		while( $row = sparql_fetch_array( $result ) )
		{
			$child= array(
				'id' => $row['method'],
				'name' => $row['exact'],
				'annotation' => 'supplier',
				'image' => '',
				'dbpedia' => '',
				'children' => array()
			);
			
			$children[] = $child;
		}
		return $children;
	}
	// find processes that need a particular dbpedia ingredient
	function look_for_dbpedia_output($entityURI,$dbpedia,$db_link,$db_dbpedia,$limit) {
		$entityURI=explode('---',$entityURI)[1];
		$children=array();
		$result=sparql_request('PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
								PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
								PREFIX prohow: <http://w3id.org/prohow#>

								SELECT ?method ?exact

								WHERE {
									?node rdf:type <'.$dbpedia.'> .
									?method prohow:requires ?node .
									?method rdfs:label ?exact .
								} limit 50',$db_link);
		if(sparql_num_rows($result)>$limit) {
		
			$count=0;
			while( $row = sparql_fetch_array( $result ) )
			{
				$child= array(
					'id' => $row['method'],
					'name' => $row['exact'],
					'annotation' => 'supplied',
					'image' => '',
					'dbpedia' => '',
					'children' => array()
				);
				
				$children[] = $child;
				
				if($count++>=$limit) break;
			}
			
			$fake_children=array();
			while( $row = sparql_fetch_array( $result ) )
			{
				$child= array(
					'id' => $row['method'],
					'name' => $row['exact'],
					'annotation' => 'supplied',
					'image' => '',
					'dbpedia' => '',
					'children' => array()
				);
				$fake_children[] = $child;
			}
		
			$fake_node= array(
				'id' => 'http_extension',
				'name' => 'More supplied process',
				'annotation' => 'supplied_extension',
				'image' => '',
				'dbpedia' => '',
				'children' => $fake_children
			);
			$children[] = $fake_node;
		}
		else
			while( $row = sparql_fetch_array( $result ) )
			{
				$child= array(
					'id' => $row['method'],
					'name' => $row['exact'],
					'annotation' => 'supplied',
					'image' => '',
					'dbpedia' => '',
					'children' => array()
				);
				
				$children[] = $child;
			}
		return $children;
	}
?>
