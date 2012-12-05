<?php

require_once('variables_set.php');

createArrays("title","author","url");
setVars();
	
if ($is_input['title'] && $_POST['title'] == "")
	$errors['title'] = "Введите заглавие!";
	
if ($is_input['title'] && $errors['title']==""){
	require_once('database_connect.php');

	mysql_set_charset('utf8',$db);
	$query = mysql_query('SELECT * FROM sources WHERE title=\''.mysql_real_escape_string($vars['title']).'\'') 
		or die(mysql_error());
	if($x = mysql_fetch_array($query))
		$errors['title'] = "Такой источник уже существует!";
	else{
		mysql_query('INSERT INTO sources VALUES(NULL,\''.mysql_real_escape_string($vars['title']).'\',\''.
			mysql_real_escape_string($vars['author']).'\',\''.mysql_real_escape_string($vars['url']).'\')')
			or die(mysql_error());
		$last_source_id = mysql_insert_id;
		
		header( 'HTTP/1.1 303 See Other' );
		header( 'Location: source.php?id='.$last_source_id);
	}		
	mysql_close($db);
}

if ($errors['title'] != "")
	$error_tags['title'] = "*1";
	
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
		  <label for="title" class="title">Title
		    <span class="error"><?=$error_tags['title']?></span>
		  </label>
		</td>
		<td>
		  <input name="title" value="<?=htmlspecialchars($vars['title'])?>" autocomplete=off ></input>
		</td>
	      </tr><tr>
		<td>
		  <label for="author" class="author">Author</label>
		</td>
		<td>
		  <input name="author" value="<?=htmlspecialchars($vars['author'])?>" autocomplete=off ></input>
		</td>
	      </tr><tr>
		<td>
		  <label for="url" class="url">URL</label>
		</td>
		<td>
		  <input name="url" value="<?=htmlspecialchars($vars['url'])?>" autocomplete=off ></input>
		</td>
	      </tr>
	    </table>
	    <div class="errors">
	      <span class="error"> <?=$error_tags['title']?> <?=$errors['title']?></span>
	    </div>
	    <input class="button" type="submit">
	    <input class="button" type="button" value="cancel" onclick="location='sources.php'">
	  </form>
	</td>
      </tr>
    </table>
  </body>
</html>