<?php
	require_once('security.php');
	require_once('sparql.php');
	if( isset($_POST['search'])) {
		//TODO here we must test injection
		$arg= escape_string(strtolower($_POST['search']));
		$list = explode (' ', $arg);
		echo 'Results for "'.$arg.'" :';
		// display all best links
		
		
		$query="PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
				PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
				PREFIX prohow: <http://w3id.org/prohow#>

		SELECT distinct ?main ?label



		WHERE { 
		    ?main rdf:type prohow:instruction_set . 
			?main prohow:has_step | prohow:has_method ?stepmethod . 
			?main rdfs:label ?label . ";
			
			
		for($i=0;$i<count($list);$i++) 
			#$query.= "?label bif:contains \"'".$list[$i]."'\" . ";
			$query.="FILTER regex(str(?label), \"".$list[$i]."\", \"i\" ) ";
			#$query.="?exact bif:contains \"".$list[$i]."\" .";
		
		$query.="}
		limit 100";
		$result=sparql_request($query,$db1,$db2);
		
		if($result==false||sparql_num_rows($result)==0) {
			print '<br/>No result avalaible for search "'.$arg.'"';
		}
		else {
			print "<br/><br/><table style=\"text_align:left;\">";
			while( $row = sparql_fetch_array( $result ) )
			{
				print "<tr>";
				print '<td><a href="index.php?page=view&link='.urlencode($row['main']).'&exact='.$row['label'].'">'.$row['label'].'</a></td>';
				print "</tr>";
			}
			print "</table>";
		}
		
		
	}
	else 
		print "<br/>Please enter a valid argument!";
?>
