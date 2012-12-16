<?php

function newQuotation(){
	return array ('description'=>'', 'content'=>'', 'startdate'=>NULL, 'enddate'=>NULL, 'tags'=>array());
}

function toSqlDate($dateTime) {
	return $dateTime->format('Y-m-d H:i:s');
}

function addQuotation ($quotation) {

	$qstr = "INSERT INTO quotations VALUES(NULL,"
		.mysql_real_escape_string($quotation['source_id']).",'"
		.mysql_real_escape_string($quotation['content'])."','"
		.mysql_real_escape_string($quotation['description'])."',"
		.($quotation['startdate']==NULL? "NULL" : "'".toSqlDate($quotation['startdate'])."'").","
		.($quotation['enddate']==NULL? "NULL" : "'".toSqlDate($quotation['enddate'])."'").")";
		
	echo($qstr);
	
	mysql_query($qstr) or die(mysql_error());
	$last_quote_id = mysql_insert_id();
	
	//$tags_array = array_map('trim', explode(',' , $quotation['tags']));
	$tags_array = array();
	for ($i=0; $i<sizeof($quotation['tags']); $i++)
		$tags_array[$i] = "'".mysql_real_escape_string($quotation['tags'][$i])."'";
	$tags_quoted = implode(',' , $tags_array);
	
	$query2 = mysql_query("SELECT * FROM tags WHERE name IN (".$tags_quoted.")") 
		or die(mysql_error());

	$existing_tags = array();
	while ($one_tag = mysql_fetch_array($query2))
		$existing_tags[] = "'".$one_tag['name']."'";

	for ($i=0; $i<sizeof($tags_array); $i++)
		if (!in_array($tags_array[$i], $existing_tags))
			mysql_query("INSERT INTO tags VALUES(NULL,".$tags_array[$i].")") 
				or die(mysql_error());
			
	$query2 = mysql_query("SELECT * FROM tags WHERE name IN (".$tags_quoted.")") 
		or die(mysql_error());
	
	while ($one_tag = mysql_fetch_array($query2))
		mysql_query("INSERT INTO quotation_tags VALUES(".mysql_real_escape_string($last_quote_id).",".
		mysql_real_escape_string($one_tag['id']).")") or die(mysql_error());
}

function getErrors ($quotation){

	$errors = array();
	$new_quote = newQuotation();

	if ($quotation['description'] == "")
		$errors['description'] = "обязательно для заполнения";
	else
		$new_quote['description'] = $quotation['description'];
		
	if ($quotation['content'] == "")
		$errors['content'] = "обязательно для заполнения";
	else
		$new_quote['content'] = $quotation['content'];
		
	if (!($quotation['startdate'] == "")){
		try{
			$d = new DateTime($quotation['startdate']);
			$new_quote['startdate'] = $d;
		}catch(Exception $e){
			$errors['startdate'] = "ошибка формата";
		}
	}
	
	if (!($quotation['enddate'] == "")){
		try{
			$d = new DateTime($quotation['enddate']);
			$new_quote['enddate'] = $d;
		}catch(Exception $e){
			$errors['enddate'] = "ошибка формата";
		}
	}
	
	$new_quote['tags'] = array_map('trim', explode(',' , $quotation['tags']));
	
	return array($new_quote, $errors);
	
}

?>