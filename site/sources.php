<?php

// connecting to mysql and selecting database "historylog"
$db = mysql_connect('localhost','root','ares') or die(mysql_error());
mysql_select_db('historylog') or die (mysql_error());
mysql_set_charset('utf8',$db); 

// making query	from tables "sources" and "quotations"
$query = mysql_query('SELECT sources.id, sources.title, sources.author, quotations.source_id, 
	MIN(quotations.start_time) AS min_time, MAX(quotations.end_time) AS max_time, COUNT(quotations.source_id) AS c
	FROM sources LEFT JOIN quotations ON sources.id=quotations.source_id GROUP BY sources.id') 
	or die("Database query failed!!!".mysql_error());

// queries are over, disconnecting from mysql
mysql_close($db);
?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="main.css" rel="stylesheet" type="text/css">
  </head>
<body>

 <div id="site-header">
      Quote-mining & history-logging
    </div>
    <table>
      <tr>
	<td id="menu">
	  <ul>
	    <li class="active"><a href="sources.html">Sources</a></li>
	    <li><a href="tags.html">Tags</a></li>
	    <li><a href="events.html">Events</a></li>
	    <li><a href="sourceadd.php">Add source</a></li>
	    <li><a href="quoteadd.php">Add quote</a></li>
	  </ul>
	</td>
	
	<td id="contents">
	  <h1>Source list</h1>
	  <span class="button"><a href="sourceadd.php">Add new source</a></span>
	  <ul class="source">
		<?php
		while($arr = mysql_fetch_array($query)){
		?>
	    <li class="source">
	      <span class="quotecount"><?=$arr['c']?></span>
	      <span class="title"><a href="source.php?id=<?=$arr['id']?>"><?=$arr['title']?></a></span>
	      <span class="author"><?=$arr['author']?></span>
	      <span class="dates"><?=$arr['min_time']?> - <?=$arr['max_time']?></span>
	    </li>
		<?php } ?>
	  </ul>
	</td>

      </tr>
    </table>

</body>
</html>