<?php
ini_set('display_errors', '1');
require_once('lib/utils.php');

// connecting to mysql and selecting database "historylog"
require_once('database_connect.php');

// making query	from tables "sources" and "quotations"
$query = mysql_query('SELECT sources.id, sources.title, sources.author, quotations.source_id, 
	MIN(quotations.start_time) AS min_time, MAX(quotations.end_time) AS max_time, COUNT(quotations.source_id) AS c
	FROM sources LEFT JOIN quotations ON sources.id=quotations.source_id GROUP BY sources.id') 
	or die(mysql_error());

// queries are over, disconnecting from mysql
mysql_close($db);
?>
<?php 
$ACTIVE_PAGE="sources";
require_once("pages/main_header.php"); ?>

	  <h1>Список источников</h1>
	  <span class="button"><a href="sourceadd.php">Добавить новый источник</a></span>
	  <ul class="source">
		<?php
		while($arr = mysql_fetch_array($query)){
		?>
	    <li class="source">
	      <span class="quotecount"><?=$arr['c']?></span>
	      <span class="title"><a href="source.php?id=<?=$arr['id']?>">
			<?=htmlspecialchars($arr['title'])?></a></span>
	      <span class="author"><?=htmlspecialchars($arr['author'])?></span>
		<span class="dates"><?=htmlspecialchars(formatRange(toDateTime($arr['min_time']), toDateTime($arr['max_time'])))?></span>
	    </li>
		<?php } ?>
	  </ul>

<?php require_once("pages/main_footer.php"); ?>
