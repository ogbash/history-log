<?php

$db = mysql_connect('localhost','root','ares') or die("Connection failed!!!");

mysql_select_db('historylog') or die ("Databse selection failed!!!");
	
$query = mysql_query('SELECT * FROM sources') or die("Database query failed!!!");

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
	      <span class="quotecount">3</span>
	      <span class="title"><a href="source.php?id=<?=$arr['id']?>"><?=$arr['title']?></a></span>
	      <span class="author"><?=$arr['author']?></span>
	      <span class="dates">1.01.1991 - 1.01.1993</span>
	    </li>
		<?php } ?>
	  </ul>
	</td>

      </tr>
    </table>

</body>
</html>