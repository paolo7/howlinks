<?php
	require_once( "sparqllib.php" );
	
	$db1 = sparql_connect( "http://dydra.com/paolo-pareti/knowhow6/sparql" );
	if( !$db1 || $db1==null ) { $db1=null; echo 'No sparql server!'; 
exit; }
	$db2 = sparql_connect( "https://dbpedia.org/sparql" );
	if( !$db2 || $db2==null ) { $db2=null; }
	
	sparql_ns( "foaf","http://xmlns.com/foaf/0.1/" );
	
	function sparql_request($sparql,$db) {
		$result = sparql_query( $sparql ,$db ); 
		if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; return null; }
		return $result;
	}
?>
