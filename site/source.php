<?php
require_once('lib/utils.php');

$source_id = (int) $_GET['id'];

require_once('database_connect.php');

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
<?php require_once("pages/main_header.php"); ?>

	  <!-- только для данной страницы -->
	  <h2>Источник</h2>

	  <?php 
		$source = mysql_fetch_array($query1);
	  ?>
	  
	  <div class="title">
	    <?=htmlspecialchars($source['title'])?>
	  </div>
	  <div class="author">
	    <?=htmlspecialchars($source['author'])?>
	  </div>
	  <div class="url">
	    <a  target="_blank" href=<?=htmlspecialchars($source['url'])?>><?=htmlspecialchars($source['url'])?></a>
	  </div>
	  
	  <span class="button"><a href="quoteadd.php?id=<?=$source_id?>">Добавить цитату</a></span>
		
	  <div>
	    <ul class="quotes">
		
		<?php while($quote = mysql_fetch_array($query2)){ ?>
		
	      <li class="quote">
		<span class="description">
		  <?=htmlspecialchars($quote['description'])?>
		</span>
		<span class="dates">
		 <?=htmlspecialchars(formatRange(toDateTime($quote['start_time']), toDateTime($quote['end_time'])))?>
		</span>
		<?php if(isset($queried_tags[$quote['id']])) { ?>
		<span class="tags">
		  <?=$queried_tags[$quote['id']]?>
		</span>
		<?php } ?>
		<span class="content">
			&laquo;<?=nl2br(trim(htmlspecialchars($quote['content'])))?>&raquo;
		</span>
	      </li>
		  <?php } ?>
	    </ul>
	  </div>

<?php require_once("pages/main_footer.php"); ?>
