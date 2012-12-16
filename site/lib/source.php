<?php

require_once('mysql.php');

function getSource($source_id){
	$query = mysql_query('SELECT * FROM sources WHERE id='.mysql_real_escape_string($source_id)) or die(mysql_error());
	$source = mysql_fetch_array($query);
	return $source;
}

/**
 * Search source by title prefix string.
 * @return array of source objects
 */
function searchSourcesByTitle($title) {
  $q = mysql_query('SELECT * FROM sources WHERE title LIKE "'.esc($title).'%"') or die("mysql query failed");

  $result = array();
  while($source = mysql_fetch_assoc($q)) {
    array_push($result, $source);
  }

  return $result;
}

?>