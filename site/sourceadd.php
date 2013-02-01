<?php

$source = array('title'=>'','author'=>'','url'=>'');
$errors = array();

if (sizeof($_POST)>0){
	foreach($source as $key=>$value)
		$source[$key] = $_POST[$key];
	
	if ($source['title']=="")
		$errors['title'] = "Введите заглавие!";
	
	else{
		require_once('database_connect.php');

		$query = mysql_query('SELECT * FROM sources WHERE title=\''.mysql_real_escape_string($source['title']).'\'') 
			or die(mysql_error());
		if($x = mysql_fetch_array($query))
			$errors['title'] = "Такой источник уже существует!";
		else{
			mysql_query('INSERT INTO sources VALUES(NULL,\''.mysql_real_escape_string($source['title']).'\',\''.
				mysql_real_escape_string($source['author']).'\',\''.mysql_real_escape_string($source['url']).'\')')
				or die(mysql_error());
			$last_source_id = mysql_insert_id();
		
			header( 'HTTP/1.1 303 See Other' );
			header( 'Location: source.php?id='.$last_source_id);
			mysql_close($db);
			exit;
		}
		mysql_close($db);
	}
}

if (isset($errors['title']))
	$error_tags['title'] = "*1";
	
?>

<?php require_once("pages/main_header.php"); ?>

	  <!-- только для данной страницы -->
	  <h2>Add new source</h2>
	  <form action="sourceadd.php" method="POST">
	    <table class="form">
	      <tr>
		<td>
		  <label for="title" class="title">Title
		    <?php if (isset($errors['title'])) {?><span class="error"><?=$error_tags['title']?></span><?php }?>
		  </label>
		</td>
		<td>
		  <input name="title" value="<?=htmlspecialchars($source['title'])?>" autocomplete=off ></input>
		</td>
	      </tr><tr>
		<td>
		  <label for="author" class="author">Author</label>
		</td>
		<td>
		  <input name="author" value="<?=htmlspecialchars($source['author'])?>" autocomplete=off ></input>
		</td>
	      </tr><tr>
		<td>
		  <label for="url" class="url">URL</label>
		</td>
		<td>
		  <input name="url" value="<?=htmlspecialchars($source['url'])?>" autocomplete=off ></input>
		</td>
	      </tr>
	    </table>
	    <div class="errors">
			<?php foreach($errors as $key => $value){ ?>
			<span class="error"> <?=$error_tags[$key]?> <?=$errors[$key]?></span>
			<?php } ?>
	    </div>
	    <input class="button" type="submit">
	    <input class="button" type="button" value="cancel" onclick="location='sources.php'">
	  </form>

<?php require_once("pages/main_footer.php"); ?>
