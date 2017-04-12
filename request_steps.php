<?php
	function look_for_step($nodeid,$db_link,$db_dbpedia) {
		$nodeid=explode('---',$nodeid)[1];
		$children=array();
	
		$result=sparql_request("PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
								PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
								PREFIX prohow: <http://w3id.org/prohow#>

								SELECT ?step ?exact

								WHERE {
									<".$nodeid.">  prohow:has_step ?step .
									 ?step rdfs:label ?exact .
								}",$db_link);
		$requirement_map=array();
		
		$query="PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
				PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
				PREFIX prohow: <http://w3id.org/prohow#>
				
				SELECT ?entity ?requirement

				WHERE {
				   ";
				
		$count=0;
		while( $row = sparql_fetch_array( $result ) )
		{
			$url1=explode('&',substr($nodeid, 39))[0];
			$url2=explode('&',substr($row['step'], 39))[0];
			if($url1==$url2) {
				$annotation='step_process';
				$subchildren=null;
			}
			else {
				$annotation='step';
				$subchildren=look_for_step_for_step('XXX---'.$row['step'],$db_link,$db_dbpedia,1);
			}
			$child= array(
				'id' => $row['step'],
				'name' => (count($subchildren)==0?$row['exact']:'<u>'.$row['exact'].'</u>'),
				'annotation' => $annotation,
				'image' => '',
				'URI' => $row['step'],
				'children' => array()
			);
			$children[$row['step']] = $child;
			if($count > 0) $query.=" UNION ";
			$query.=" { <".$row['step']."> prohow:requires ?requirement  . BIND( <".$row['step']."> AS ?entity ) } ";
			$count++;
		}
		
		$query.=" } ";
		
		$final_children=array();
		$final_children2=array();
		
		if($count<=1)
			return $children;
		
		$result_order=sparql_request($query,$db_link);
		
		while( $row = sparql_fetch_array( $result_order ) )
		{
			$requirement_map[$row['entity']]=$row['requirement'];
			
		}
		
		// getting unfound links to add them first
		$count=0;
		foreach ($children as $key => $value){
			
			// seeking
			if(!array_key_exists($key,$requirement_map)) {
				$value['name']=(++$count).') '.$value['name'];
				$final_children[$key]=$value;
				$final_children2[]=$value;
				#echo "+A".$value."A+";
				
			}
		}
		
		while(count($requirement_map)>0) {
			// looking for a requierement existing in final_children
			$has_been_modified=false;
			foreach ($requirement_map as $key => $value){
				
				if(array_key_exists($value,$final_children)) {
					$children[$key]['name']=(++$count).') '.$children[$key]['name'];
					$final_children[$key]=$children[$key];
					$final_children2[]=$children[$key];
					unset($requirement_map[$key]);
					$has_been_modified=true;
					
					break;
				}
			}
			if(!$has_been_modified) {
				break;
			}
		}
		
		if(sparql_num_rows($result) > 5) {
			$fake_node=array();
			$fake_node[] = array(
							'id' => 'http_extension',
							'name' => sparql_num_rows($result).' Steps',
							'annotation' => 'classifier_step',
							'image' => '',
							'children' => $final_children2
			);
			return $fake_node;
		}
		return $final_children2;
	}	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function look_for_step_without($nodeid,$db_link,$db_dbpedia) {
		#$nodeid=explode('---',$nodeid)[1];
		$children=array();
	
		$result=sparql_request("PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
								PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
								PREFIX prohow: <http://w3id.org/prohow#>

								SELECT ?step ?exact

								WHERE {
									<".$nodeid."> prohow:has_step ?step .
									 ?step rdfs:label ?exact .
								}",$db_link);
								
		$requirement_map=array();
		
		$query="PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
				PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
				PREFIX prohow: <http://w3id.org/prohow#>
				
				SELECT ?entity ?requirement


				WHERE {
					{} ";
		$count=0;
		while( $row = sparql_fetch_array( $result ) ){
			if($count > 0) $query.=" UNION ";
			$query.="{ ".$row['step']." prohow:requires ?requirement  . } ";
			$subchildren=null;
			$child= array(
				'id' => $row['step'],
				'name' => (count($subchildren)==0?$row['exact']:'<u>'.$row['exact'].'</u>'),
				'annotation' => 'step_process',
				'image' => '',
				'URI' => $row['step'],
				'children' => array()
			);
			$children[$row['step']] = $child;
			$count++;
		}
		$query.=" } ";
		
		#$count=0;
		#while( $row = sparql_fetch_array( $result ) )
		#{
		#	$url1=explode('&',substr($nodeid, 39))[0];
		#	$url2=explode('&',substr($row['step'], 39))[0];
			#if($url1!=$url2) {
		#		$annotation='step_process';
		#		$subchildren=null;
			#}
			#else {
			#	$annotation='step';
			#	$subchildren=look_for_step_for_step('XXX---'.$row['step'],$db_link,$db_dbpedia,1);
			#}
		#	$child= array(
		#		'id' => $row['step'],
		#		'name' => (count($subchildren)==0?$row['exact']:'<u>'.$row['exact'].'</u>'),
		#		'annotation' => $annotation,
		#		'image' => '',
		#		'URI' => $row['step'],
		#		'children' => array()
		#	);
		#	$children[$row['step']] = $child;
		#	$query.="FILTER regex(str(?entity), \"".$row['step']."\", \"i\" ) ";
		#	$count++;
		#}
		
		#$query.=" } limit ".($count-1);
		
		$final_children=array();
		
		#if($count<=1)
		#	return $children;
		
		$result_order=sparql_request($query,$db_link);
		while( $row = sparql_fetch_array( $result_order ) )
		{
			$requirement_map[$row['entity']]=$row['requirement'];
		}
		
		// getting unfound links to add them first
		$count=0;
		foreach ($children as $key => $value){
			// seeking
			if(!array_key_exists($key,$requirement_map)) {
				$value['name']=(++$count).') '.$value['name'];
				$final_children[]=$value;
			}
		}
		
		while(count($requirement_map)>0) {
			// looking for a requierement existing in final_children
			$has_been_modified=false;
			foreach ($requirement_map as $key => $value){
				if(array_key_exists($value,$final_children)) {
					$children[$key]['name']=(++$count).') '.$children[$key]['name'];
					$final_children[]=$children[$key];
					unset($requirement_map[$key]);
					$has_been_modified=true;
					break;
				}
			}
			if(!$has_been_modified) {
				break;
			}
		}
		
		if(sparql_num_rows($result) > 5) {
			$fake_node=array();
			$fake_node[] = array(
							'id' => 'http_extension',
							'name' => sparql_num_rows($result).' Steps',
							'annotation' => 'classifier_step',
							'image' => '',
							'children' => $final_children
			);
			return $fake_node;
		}
		return $final_children;
	}
	
?>