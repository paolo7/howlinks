<?php
	function escape_string($p) {
		if (strpos($p,'#') !== false) exit;
                if (strpos($p,'\'') !== false) exit;
                if (strpos($p,'`') !== false) exit;

		
		return $p;
	}
?>

