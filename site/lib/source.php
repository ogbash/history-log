<?php
function getSource($source_id){
	$query = mysql_query('SELECT * FROM sources WHERE id='.mysql_real_escape_string($source_id)) or die(mysql_error());
	$source = mysql_fetch_array($query);
	return $source;
}
?>