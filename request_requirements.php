<?php	
	function get_dbpedia($ingredient,$db_link,$db_dbpedia) {
		$array_dbpedia=array();
		$result=sparql_request("PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
								PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
								PREFIX prohow: <http://w3id.org/prohow#>
								SELECT ?dbpedia            
								WHERE {
									<".$ingredient.">  rdf:type ?dbpedia .
								
								}",$db_link);
		
		while( $row = sparql_fetch_array( $result ) )
		{
			$dbpedia_information=get_dbpedia_information($row['dbpedia'],$db_dbpedia);
			$exact=$dbpedia_information['exact'];
			$image=$dbpedia_information['image'];
			if($exact=='')
				$exact=$row['dbpedia'];
			if (strlen($image)==0) $image='';
			$array_dbpedia[]=array('exact' => $exact,'name' => $row['dbpedia'], 'image'=> $image);
		}
		return $array_dbpedia;
	}
	function get_dbpedia_information($link,$db_dbpedia) {
		if($db_dbpedia==null) return array('exact' => '','image' => '');
		$dbpedia_result=sparql_request("SELECT ?exact ?i WHERE {
											<".$link."> <http://dbpedia.org/ontology/thumbnail> ?i .
											<".$link."> rdfs:label ?exact .
											filter(langMatches(lang(?exact),\"EN\"))
										} LIMIT 1",$db_dbpedia);
	
		if($dbpedia_row = sparql_fetch_array( $dbpedia_result )) {
			return array('exact' => $dbpedia_row['exact'],'image' => $dbpedia_row['i']);
		}
		return array('exact' => '','image' => '');
	}
	
	function look_for_dbpedia($nodeid,$db_link,$db_dbpedia) {
		if (strpos($nodeid, '---') !== false) $nodeid=explode('---',$nodeid)[1];
		$children=array();
		
		$result=get_dbpedia($nodeid,$db_link,$db_dbpedia);
						
		for($i=0;$i<count($result);$i++)
		{
			$row=$result[$i];
			$child= array(
				'id' => $row['name'],
				'name' => (($row['exact']=='')?$row['name']:$row['exact']),
				'annotation' => 'input',
				'image' => $row['image'],
				'dbpedia' => $row['name'],
				'children' => array()
			);
			$children[] = $child;
		}
		
		return $children;
	}
	
	function look_for_requirements($nodeid,$db_link,$db_dbpedia) {
		if (strpos($nodeid, '---') !== false)  $nodeid=explode('---',$nodeid)[1];
		$children=array();
		
		$result=sparql_request("PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
								PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
								PREFIX prohow: <http://w3id.org/prohow#>

								SELECT ?step ?exact

								WHERE {
    
								<".$nodeid."> prohow:requires ?step .
								?step rdfs:label ?exact .

							}",$db_link);
						
		while( $row = sparql_fetch_array( $result ) )
		{
			$dbpedia=get_dbpedia($row['step'],$db_link,$db_dbpedia);
			if(count($dbpedia)==0) {
				$child= array(
					'id' => $row['step'],
					'name' => $row['exact'],
					'annotation' => 'requirement',
					'image' => '',
					'dbpedia' => '',
					'children' => array()
				);
			}
			else if(count($dbpedia)==1) {
				$first=reset($dbpedia);
				$child= array(
					'id' => $row['step'],
					'name' => ($first['exact']=='')?$row['exact']:$first['exact'].' ('.$row['exact'].')',
					'annotation' => 'input',
					'image' => $first['image'],
					'dbpedia' => '',
					'children' => array()
				);
			}
			else {
				$child= array(
					'id' => $row['step'],
					'name' => $row['exact'].' ('.count($dbpedia).' ingredients)',
					'annotation' => 'requirement',
					'image' => '',
					'dbpedia' => '',
					'children' => array(),
				);
			}
			$children[] = $child;
		}
		
		if(count($children) > 5) {
			$fake_node=array();
			$fake_node[] = array(
							'id' => $nodeid.'_requirements',
							'name' => count($children).' Requirements',
							'annotation' => 'classifier_requirement',
							'image' => '',
							'dbpedia' => '',
							'children' => $children
			);
			return $fake_node;
		}
		
		return $children;
	}
	
	// find requirements for a group of ingredients
	function look_for_group_of_ingredients($nodeid,$db_link,$db_dbpedia) {
		if (strpos($nodeid, '---') !== false) $nodeid=explode('---',$nodeid)[1];
		$children=array();
		$result=sparql_request("PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
								PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
								PREFIX prohow: <http://w3id.org/prohow#>

								SELECT ?step ?exact

								WHERE {
    
								<".$nodeid."> prohow:has_step ?step .
								?step rdfs:label ?exact .

							}",$db_link);
						
		while( $row = sparql_fetch_array( $result ) )
		{
			$dbpedia=get_dbpedia($row['step'],$db_link,$db_dbpedia);
			
			if(count($dbpedia)==0) {
				$url1=explode('&',substr($nodeid, 39))[0];
				$url2=explode('&',substr($row['step'], 39))[0];
				if($url1!=$url2)
					$annotation='step_process';
				else
					$annotation='requirement';
				$child= array(
					'id' => $row['step'],
					'name' => $row['exact'],
					'annotation' => $annotation,
					'image' => '',
					'dbpedia' => '',
					'children' => array()
				);
			}
			else if(count($dbpedia)==1) {
				$first=reset($dbpedia);
				$child= array(
					'id' => $row['step'],
					'name' => ($first['exact']=='')?$row['exact']:$first['exact'],
					'annotation' => 'input',
					'image' => $first['image'],
					'dbpedia' => $first['name'],
					'children' => array()
				);
			}
			else {
				$child= array(
					'id' => $row['step'],
					'name' => $row['exact'].' ('.count($dbpedia).' ingredients)',
					'annotation' => 'requirement',
					'image' => '',
					'dbpedia' => '',
					'children' => array()
				);
			}
			$children[] = $child;
		}
		return $children;
	}
	
	// find dbpedia ingredient output for a process
	function look_for_output($process_node,$db_link,$db_dbpedia) {
		if (strpos($process_node, '---') !== false) $process_node=explode('---',$process_node)[1];
		$children=array();
		$result=sparql_request("PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
								PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
								PREFIX prohow: <http://w3id.org/prohow#>
								
								SELECT ?dbpedia 

								
								WHERE { 
									?node prohow:has_method <".$process_node."> .
									?node rdf:type ?dbpedia .
								}",$db_link);
		
		while( $row = sparql_fetch_array( $result ) )
		{
			$dbpedia_information=get_dbpedia_information($row['dbpedia'],$db_dbpedia);
			$exact=$dbpedia_information['exact'];
			$image=$dbpedia_information['image'];
			if($exact=='')
				$exact=$row['dbpedia'];
			if (strlen($image)==0) $image='';
			
			$child= array(
				'id' => $row['dbpedia'],
				'name' => $exact,
				'annotation' => 'output',
				'image' => $image,
				'dbpedia' => $row['dbpedia'],
				'children' => array()
			);
			
			$children[] = $child;
		}
		return $children;
	}
?>
