<?php

$source_id = $_GET['id'];

$db = mysql_connect('localhost','root','ares') or die("Connection failed!!!");

mysql_select_db('historylog') or die ("Databse selection failed!!!");

$query1 = mysql_query('SELECT * FROM sources WHERE id='.$source_id) or die("Database query failed!!!");
$query2 = mysql_query('SELECT * FROM quotations WHERE source_id='.$source_id) or die("Database query failed!!!");
$query3 = mysql_query('SELECT tags.name, quotations.id  
					FROM tags JOIN quotation_tags AS qts JOIN quotations
					WHERE tags.id=qts.tag_id AND quotations.id=qts.quotation_id 
						AND quotations.source_id='.$source_id) or die("Database query failed!!!");
						
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
	    <li><a href="quoteadd.html">Add quote</a></li>
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
