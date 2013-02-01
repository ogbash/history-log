<?php

// connecting to mysql and selecting database "historylog"
require_once('database_connect.php');

// making query	from tables "sources" and "quotations"
$query = mysql_query('SELECT tags.id, tags.name, COUNT(quotation_tags.quotation_id) AS c
	FROM tags LEFT JOIN quotation_tags ON tags.id=quotation_tags.tag_id GROUP BY quotation_tags.tag_id ORDER BY c DESC')
	or die(mysql_error());

// queries are over, disconnecting from mysql
mysql_close($db);
?>
<?php 
$ACTIVE_PAGE="tags";
require_once("pages/main_header.php"); ?>

	  <h1>Метки</h1>

	  <ul class="source">
		<?php
		while($arr = mysql_fetch_array($query)){
		?>
	    <li class="tag">
	      <span class="tagcount"><?=$arr['c']?></span>
	      <span class="tags">
			<?=htmlspecialchars($arr['name'])?></a></span>
	    </li>
		<?php } ?>
	  </ul>


<?php require_once("pages/main_footer.php"); ?>

