<?php

require_once('variables_set.php');

$errors_exist = false;

createArrays("description","content","startdate","enddate","tags");
setVars();

$error_no = 1;
$f_tags_array = array();

$source_id = (int) $_GET['id'];

require_once('database_connect.php');

mysql_set_charset('utf8',$db);
	
if ($is_input['description'] && $_POST['description'] == ""){
	$errors_exist = true;
	$errors['description'] = "обязательно для заполнения";
	$error_tags['description'] = "*".$error_no;
	$error_no++;
}

if ($is_input['content'] && $_POST['content'] == ""){
	$errors_exist = true;
	$errors['content'] = "обязательно для заполнения";
	$error_tags['content'] = "*".$error_no;
	$error_no++;
}
	
if ($is_input['startdate'] && $_POST['startdate']!="" && !strtotime($_POST['startdate'])){
	$errors_exist = true;
	$errors['startdate'] = "ошибка формата";
	$error_tags['startdate'] = "*".$error_no;
	$error_no++;
}
	
if($vars['startdate'] == "")
	$startdate_sql = "NULL";
else
	$startdate_sql = "'".mysql_real_escape_string($vars['startdate'])."'";
	
if ($is_input['enddate'] && $_POST['enddate']!="" && !strtotime($_POST['enddate'])){
	$errors_exist = true;
	$errors['enddate'] = "ошибка формата";
	$error_tags['enddate'] = "*".$error_no;
	$error_no++;
}

if($vars['enddate'] == "")
	$enddate_sql = "NULL";
else
	$enddate_sql = "'".mysql_real_escape_string($vars['enddate'])."'";

$query = mysql_query('SELECT * FROM sources WHERE id='.mysql_real_escape_string($source_id)) or die(mysql_error());

if ( $is_input['description'] && $is_input['content'] && $errors_exist == false ){
	mysql_query("INSERT INTO quotations VALUES(NULL,".mysql_real_escape_string($source_id).",'".
		mysql_real_escape_string($vars['content'])."','".mysql_real_escape_string($vars['description'])."',".
		$startdate_sql.",".$enddate_sql.")") or die(mysql_error());
	$last_quote_id = mysql_insert_id();
	
	$tags_array = array_map('trim', explode(',' , $vars['tags']));
	for ($i=0; $i<sizeof($tags_array); $i++)
		$tags_array[$i] = "'".mysql_real_escape_string($tags_array[$i])."'";
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
		
	header( 'HTTP/1.1 303 See Other' );
	header( 'Location: source.php?id='.$source_id);
	mysql_close($db);
	exit;

}

mysql_close($db);

?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="main.css" rel="stylesheet" type="text/css">
  </head>
  
  <!-- общее для всех страниц -->
  <body>
    <div id="site-header">
      Quote-mining & history-logging
    </div>
    <table>
      <tr>
	<td id="menu">
	  <ul>
	    <li><a href="sources.php">Sources</a></li>
	    <li><a href="tags.html">Tags</a></li>
	    <li><a href="events.html">Events</a></li>
	    <li><a href="sourceadd.php">Add source</a></li>
	  </ul>
	</td>

	<td id="contents">
	  <!-- только для данной страницы -->
	  <h2>Add new quote</h2>
	  
	  <?php $source = mysql_fetch_array($query) ?>
	  
	  <div class="title">
	    <?=htmlspecialchars($source['title'])?>
	  </div>
	  <div class="author">
	    <?=htmlspecialchars($source['author'])?>
	  </div>
	  <div class="url">
	    <a  target="_blank" href="<?=htmlspecialchars($source['url'])?>"><?=htmlspecialchars($source['url'])?></a>
	  </div>
	  <!-- форма -->
	  <form action="quoteadd.php?id=<?=$source_id?>" method="POST">
	    <table class="form">
	      <tr>
	      <!-- description -->
		<td>
		  <label for="description">Description
		    <span class="error"><?=$error_tags['description']?></span>
		  </label>
		</td>
		<td>
		  <input name="description" autocomplete=off value="<?=htmlspecialchars($vars['description'])?>"></input>
		</td>
	      </tr><tr>
	      <!-- content -->
		<td>
		  <label for="content">Content
		    <span class="error"><?=$error_tags['content']?></span>
		  </label>
		</td>
		<td>
		  <textarea name="content" cols="60" rows="10"><?=htmlspecialchars($vars['content'])?></textarea>
		</td>
	      </tr><tr>
	      <!-- start date -->
		<td>
		  <label for="startdate">Start date
			<span class="error"><?=$error_tags['startdate']?></span>
		  </label>
		</td>
		<td>
		  <input name="startdate" autocomplete=off value="<?=htmlspecialchars($vars['startdate'])?>"></input>
		</td>
	      </tr><tr>
	      <!-- end date -->
		<td>
		  <label for="enddate">End date
		    <span class="error"><?=$error_tags['enddate']?></span>
		  </label>
		</td>
		<td>
		  <input name="enddate" autocomplete=off value="<?=htmlspecialchars($vars['enddate'])?>"></input>
		</td>
	      </tr><tr>
	      <!-- tags -->		
		<td>
		  <label for="tags">Tags</label>
		</td>
		<td>
		  <input name="tags" autocomplete=off value="<?=htmlspecialchars($vars['tags'])?>" style="width: 30em"></input>
		</td>
	      </tr>
	    </table>
	    <div class="errors">
			<?php foreach($errors as $key => $value){
					if ($value != ""){?>
						<span class="error"><?=$error_tags[$key]?> <?=$value?></span><br>
			<?php }} ?>
	    </div>
	    <input class="button" type="submit">
	    <input class="button" type="button" value="cancel" onclick="location='source.php?id=<?=$source_id?>'">
	  </form>
	</td>
      </tr>
    </table>
  </body>
</html>