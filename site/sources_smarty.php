<?php
ini_set('display_errors', '1');
require_once('lib/utils.php');

// connecting to mysql and selecting database "historylog"
require_once('database_connect.php');

// making query	from tables "sources" and "quotations"
$query = mysql_query('SELECT sources.id, sources.title, sources.author, quotations.source_id, 
	MIN(quotations.start_time) AS min_time, MAX(quotations.end_time) AS max_time, COUNT(quotations.source_id) AS c
	FROM sources LEFT JOIN quotations ON sources.id=quotations.source_id GROUP BY sources.id') 
	or die(mysql_error());

// queries are over, disconnecting from mysql
mysql_close($db);

$ACTIVE_PAGE="sources_smarty";
//require_once("pages/main_header.php");

$output_data = array();
while($arr = mysql_fetch_array($query)){
	$arr['dates'] = formatRange(toDateTime($arr['min_time']), toDateTime($arr['max_time']));
	$output_data[] = $arr;
}

require_once('smarty/HLSmarty.php');
$smarty = new MySmarty();

$smarty->assign('arr', $output_data);
$smarty->assign('ACTIVE_PAGE', $ACTIVE_PAGE);

$smarty->display('sources.tpl');

?>