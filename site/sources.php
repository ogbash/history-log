<?php

// connecting to mysql and selecting database "historylog"
$db = mysql_connect('localhost','root','ares') or die("Connection failed!!!");
mysql_select_db('historylog') or die ("Databse selection failed!!!");

// making query	from tables "sources" and "quotations"
$query = mysql_query('SELECT * FROM sources') or die("Database query failed!!!");
$query2 = mysql_query('SELECT quotations.source_id, quotations.start_time, quotations.end_time 
	FROM quotations') or die("Database query failed!!!");

$counts = array(); // array of integers, how many quotes per source
$start_dates = array(); // date&time array, earliest quote of each source
$end_dates = array(); // date&time array, latest quote of each source

while($one_count = mysql_fetch_array($query2)){

	// calculating how many quotes per source, if 0 then array cell is NULL
	if (isset($counts[$one_count['source_id']]))
		$counts[$one_count['source_id']]++;
	else 
		$counts[$one_count['source_id']] = 1;
	
	// calculating earliest quote of each source, if no dates then array cell is NULL
	if (isset($one_count['start_time']) && 
		( !isset($start_dates[$one_count['source_id']]) ||
		$one_count['start_time'] < $start_dates[$one_count['source_id']] ) )
			$start_dates[$one_count['source_id']] = $one_count['start_time'];
	
	// calculating latest quote of each source, if no dates then array cell is NULL
	if (isset($one_count['end_time']) && 
		( !isset($end_dates[$one_count['source_id']]) ||
		$one_count['end_time'] > $end_dates[$one_count['source_id']] ) )
			$end_dates[$one_count['source_id']] = $one_count['end_time'];
}

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
	    <li><a href="sourceadd.html">Add source</a></li>
	    <li><a href="quoteadd.html">Add quote</a></li>
	  </ul>
	</td>
	
	<td id="contents">
	  <h1>Source list</h1>
	  <ul class="source">
		<?php
		while($arr = mysql_fetch_array($query)){
		?>
	    <li class="source">
	      <span class="quotecount"><?=isset($counts[$arr['id']])? $counts[$arr['id']] : 0 ?></span>
	      <span class="title"><a href="source.php?id=<?=$arr['id']?>"><?=$arr['title']?></a></span>
	      <span class="author"><?=$arr['author']?></span>
	      <span class="dates"><?=isset($start_dates[$arr['id']])? $start_dates[$arr['id']] : "" ?> - 
			<?=isset($end_dates[$arr['id']])? $end_dates[$arr['id']] : "" ?></span>
	    </li>
		<?php } ?>
	  </ul>
	</td>

      </tr>
    </table>

</body>
</html>