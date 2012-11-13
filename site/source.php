<?php

$source_id = $_GET['id'];

require_once('database_connect.php');

//mysql_set_charset('utf8',$db); 

$query1 = mysql_query('SELECT * FROM sources WHERE id='.mysql_real_escape_string($source_id)) or die(mysql_error());
$query2 = mysql_query('SELECT * FROM quotations WHERE source_id='.mysql_real_escape_string($source_id)) or die(mysql_error());
$query3 = mysql_query('SELECT tags.name, quotations.id  
					FROM tags JOIN quotation_tags AS qts JOIN quotations
					WHERE tags.id=qts.tag_id AND quotations.id=qts.quotation_id 
						AND quotations.source_id='.mysql_real_escape_string($source_id)) or die(mysql_error());
						
$queried_tags = array();
while($tags = mysql_fetch_array($query3)){
	if (isset($queried_tags[$tags['id']]))
		$queried_tags[$tags['id']] = $queried_tags[$tags['id']].", ".$tags['name'];
	else
		$queried_tags[$tags['id']] = $tags['name'];
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
	    <li><a href="quoteadd.php">Add quote</a></li>
	  </ul>
	</td>
	
	<td id="contents">
	  <!-- только для данной страницы -->
	  <h2>Source</h2>

	  <?php 
		$source = mysql_fetch_array($query1);
	  ?>
	  
	  <div class="title">
	    <?=$source['title']?>
	  </div>
	  <div class="author">
	    <?=$source['author']?>
	  </div>
	  <div class="url">
	    <a  target="_blank" href=<?=$source['url']?>><?=$source['url']?></a>
	  </div>
	  
	  <span class="button"><a href="quoteadd.php?id=<?=$source_id?>">Add new quote</a></span>
		
	  <div>
	    <ul class="quotes">
		
		<?php while($quote = mysql_fetch_array($query2)){ ?>
		
	      <li class="quote">
		<span class="description">
		  <?=$quote['description']?>
		</span>
		<span class="dates">
		  <?=$quote['start_time']?> - <?=$quote['end_time']?>
		</span>
		<?php if(isset($queried_tags[$quote['id']])) { ?>
		<span class="tags">
		  <?=$queried_tags[$quote['id']]?>
		</span>
		<?php } ?>
		<span class="content">
&laquo;<?=$quote['content']?>&raquo;
		</span>
	      </li>
		  <?php } ?>
	    </ul>
	  </div>
	</td>

      </tr>
    </table>

  </body>
</html>
