<?php

$error = "";
$error_tag = "";
$ok_message = "";
$already_input = false;

if (isset($_POST['f_title'])){
	$already_input = true;
	if($_POST['f_title'] == "")
		$error = "Введите заглавие!";
	
	$f_title = $_POST['f_title'];
}
else
	$f_title = "";
	
if (isset($_POST['f_author']))
	$f_author = $_POST['f_author'];
else
	$f_author = "";
	
if (isset($_POST['f_url']))
	$f_url = $_POST['f_url'];
else
	$f_url = "";
	
if ($already_input==true && $error==""){
	require_once('database_connect.php');

	mysql_set_charset('utf8',$db);
	$query = mysql_query('SELECT * FROM sources WHERE title=\''.mysql_real_escape_string($f_title).'\'') 
		or die(mysql_error());
	if($x = mysql_fetch_array($query))
		$error = "Такой источник уже существует!";
	else{
		mysql_query('INSERT INTO sources VALUES(NULL,\''.mysql_real_escape_string($f_title).'\',\''.
			mysql_real_escape_string($f_author).'\',\''.mysql_real_escape_string($f_url).'\')')
			or die(mysql_error());
		$f_title = ""; $f_author = ""; $f_url = "";
		$ok_message = "Источник добавлен";
	}		
	mysql_close($db);
}

if ($error!="")
	$error_tag = "*1";
	
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
	    <li class="active"><a href="sourceadd.html">Add source</a></li>
	  </ul>
	</td>

	<td id="contents">
	  <!-- только для данной страницы -->
	  <h2>Add new source</h2>
	  <form action="sourceadd.php" method="POST">
	    <table class="form">
	      <tr>
		<td>
		  <label for="f_title" class="title">Title
		    <span class="error"><?=$error_tag?></span>
		  </label>
		</td>
		<td>
		  <input name="f_title" value="<?=$f_title?>" autocomplete=off ></input>
		</td>
	      </tr><tr>
		<td>
		  <label for="f_author" class="author">Author</label>
		</td>
		<td>
		  <input name="f_author" value="<?=$f_author?>" autocomplete=off ></input>
		</td>
	      </tr><tr>
		<td>
		  <label for="f_url" class="url">URL</label>
		</td>
		<td>
		  <input name="f_url" value="<?=$f_url?>" autocomplete=off ></input>
		</td>
	      </tr>
	    </table>
	    <div class="errors">
	      <span class="error"> <?=$error_tag?> <?=$error?></span>
		  <span> <?=$ok_message?> </span>
	    </div>
	    <input class="button" type="submit">
	    <input class="button" type="button" value="cancel" onclick="location='sources.php'">
	  </form>
	</td>
      </tr>
    </table>
  </body>
</html>