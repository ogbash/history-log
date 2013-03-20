<?php
require_once('lib/utils.php');

$source_id = (int) $_GET['id'];

require_once('database_connect.php');

$query1 = mysql_query('SELECT * FROM sources WHERE id='.escape($source_id)) or die(mysql_error());
$query2 = mysql_query('SELECT * FROM quotations WHERE source_id='.escape($source_id)) or die(mysql_error());
$query3 = mysql_query('SELECT tags.name, quotations.id  
					FROM tags JOIN quotation_tags AS qts JOIN quotations
					WHERE tags.id=qts.tag_id AND quotations.id=qts.quotation_id 
						AND quotations.source_id='.escape($source_id)) or die(mysql_error());
						
$queried_tags = array();
while($tags = mysql_fetch_array($query3)){
	if (isset($queried_tags[$tags['id']]))
		$queried_tags[$tags['id']] = $queried_tags[$tags['id']].", ".$tags['name'];
	else
		$queried_tags[$tags['id']] = $tags['name'];
	}
	
mysql_close($db);

$source_output_data = mysql_fetch_array($query1);


$quote_output_data = array();
while($arr = mysql_fetch_array($query2)){
	$arr['dates'] = formatRange(toDateTime($arr['start_time']), toDateTime($arr['end_time']));
	if ($queried_tags[$arr['id']])
		$arr['tags'] = $queried_tags[$arr['id']];
	$arr['content'] = "&laquo;".nl2br(trim(htmlspecialchars($arr['content'])))."&raquo;";
	$quote_output_data[] = $arr;
}

require_once('smarty/HLSmarty.php');
$smarty = new MySmarty();

$smarty->assign('source', $source_output_data);
$smarty->assign('quote', $quote_output_data);
$smarty->assign('source_id', $source_id);

$smarty->display('source.tpl');

?>