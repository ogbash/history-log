<?php

$is_description_input = false;
$is_content_input = false;
$errors_exist = false;
$errors = array();
	$errors['description_error'] = "";
	$errors['content_error'] = "";
	$errors['startdate_error'] = "";
	$errors['enddate_error'] = "";
$error_tags = array();
	$error_tags['description_error'] = "";
	$error_tags['content_error'] = "";
	$error_tags['startdate_error'] = "";
	$error_tags['enddate_error'] = "";
$error_no = 1;
$f_tags_array = array();

$source_id = $_GET['id'];
	
if (isset($_POST['f_description'])){
	$is_description_input = true;
	if ($_POST['f_description'] == ""){
		$errors_exist = true;
		$errors['description_error'] = "обязательно для заполнения";
		$error_tags['description_error'] = "*".$error_no;
		$error_no++;
	}
	$f_description = $_POST['f_description'];
	}
else
	$f_description = "";
	
if (isset($_POST['f_content'])){
	$is_content_input = true;
	if ($_POST['f_content'] == ""){
		$errors_exist = true;
		$errors['content_error'] = "обязательно для заполнения";
		$error_tags['content_error'] = "*".$error_no;
		$error_no++;
	}
	$f_content = $_POST['f_content'];
	}
else
	$f_content = "";
	
if (isset($_POST['f_startdate'])){
	if ($_POST['f_startdate']!="" && !strtotime($_POST['f_startdate'])){
		$errors_exist = true;
		$errors['startdate_error'] = "ошибка формата";
		$error_tags['startdate_error'] = "*".$error_no;
		$error_no++;
	}
	$f_startdate = $_POST['f_startdate'];
}
else
	$f_startdate = "";
	
if($f_startdate == "")
	$f_startdate_sql = "NULL";
else
	$f_startdate_sql = "'".$f_startdate."'";
	
if (isset($_POST['f_enddate'])){
	if ($_POST['f_enddate']!="" && !strtotime($_POST['f_enddate'])){
		$errors_exist = true;
		$errors['enddate_error'] = "ошибка формата";
		$error_tags['enddate_error'] = "*".$error_no;
		$error_no++;
	}
	$f_enddate = $_POST['f_enddate'];
}
else
	$f_enddate = "";

if($f_enddate == "")
	$f_enddate_sql = "NULL";
else
	$f_enddate_sql = "'".$f_enddate."'";
	
if (isset($_POST['f_tags']))
	$f_tags = $_POST['f_tags'];
else
	$f_tags = "";
	
require_once('database_connect.php');
mysql_set_charset('utf8',$db);

$query = mysql_query('SELECT * FROM sources WHERE id='.mysql_real_escape_string($source_id)) or die(mysql_error());

if ( $is_description_input && $is_content_input && $errors_exist == false ){
	mysql_query("INSERT INTO quotations VALUES(NULL,".mysql_real_escape_string($source_id).",'".
		mysql_real_escape_string($f_content)."','".mysql_real_escape_string($f_description)."',".
		mysql_real_escape_string($f_startdate_sql).",".mysql_real_escape_string($f_enddate_sql).")") 
		or die(mysql_error());
	$last_quote_id = mysql_insert_id();
	
	$f_tags_array = array_map('trim', explode(',' , $f_tags));
	for ($i=0; $i<sizeof($f_tags_array); $i++)
		$f_tags_array[$i] = "'".mysql_real_escape_string($f_tags_array[$i])."'";
	$f_tags_quoted = implode(',' , $f_tags_array);
	
	$query2 = mysql_query("SELECT * FROM tags WHERE name IN (".$f_tags_quoted.")") 
		or die(mysql_error());

	$existing_tags = array();
	while ($one_tag = mysql_fetch_array($query2))
		$existing_tags[] = "'".$one_tag['name']."'";

	for ($i=0; $i<sizeof($f_tags_array); $i++)
		if (!in_array($f_tags_array[$i], $existing_tags))
			mysql_query("INSERT INTO tags VALUES(NULL,".$f_tags_array[$i].")") 
				or die(mysql_error());
			
	$query2 = mysql_query("SELECT * FROM tags WHERE name IN (".$f_tags_quoted.")") 
		or die(mysql_error());
	
	while ($one_tag = mysql_fetch_array($query2))
		mysql_query("INSERT INTO quotation_tags VALUES(".mysql_real_escape_string($last_quote_id).",".
		mysql_real_escape_string($one_tag['id']).")") or die(mysql_error());
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
	    <?=$source['title']?>
	  </div>
	  <div class="author">
	    <?=$source['author']?>
	  </div>
	  <div class="url">
	    <a  target="_blank" href="<?=$source['url']?>"><?=$source['url']?></a>
	  </div>
	  <!-- форма -->
	  <form action="quoteadd.php?id=<?=$source_id?>" method="POST">
	    <table class="form">
	      <tr>
	      <!-- description -->
		<td>
		  <label for="f_description">Description
		    <span class="error"><?=$error_tags['description_error']?></span>
		  </label>
		</td>
		<td>
		  <input name="f_description" value="<?=$f_description?>"></input>
		</td>
	      </tr><tr>
	      <!-- content -->
		<td>
		  <label for="f_content">Content
		    <span class="error"><?=$error_tags['content_error']?></span>
		  </label>
		</td>
		<td>
		  <textarea name="f_content" cols="60" rows="10"><?=$f_content?></textarea>
		</td>
	      </tr><tr>
	      <!-- start date -->
		<td>
		  <label for="f_startdate">Start date
			<span class="error"><?=$error_tags['startdate_error']?></span>
		  </label>
		</td>
		<td>
		  <input name="f_startdate" value="<?=$f_startdate?>"></input>
		</td>
	      </tr><tr>
	      <!-- end date -->
		<td>
		  <label for="f_enddate">End date
		    <span class="error"><?=$error_tags['enddate_error']?></span>
		  </label>
		</td>
		<td>
		  <input name="f_enddate" value="<?=$f_enddate?>"></input>
		</td>
	      </tr><tr>
	      <!-- tags -->		
		<td>
		  <label for="f_tags">Tags</label>
		</td>
		<td>
		  <input name="f_tags" value="<?=$f_tags?>" style="width: 30em"></input>
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