<?php
require_once('lib/source.php');
require_once('lib/quotation.php');
require_once('database_connect.php');

function quotation_to_form($quotation){
	$form = array();
	foreach(array('content','description') as $key) 
		$form[$key]=$quotation[$key];
		
	foreach(array('startdate','enddate') as $key)
		$form[$key]=$quotation[$key]==NULL?"":string($quotation[$key]);
		
	$form['tags'] = implode(',',$quotation['tags']);
	
	return $form;
}

$source_id = (int) $_GET['id'];
$source = getSource($source_id);

if(sizeof($_POST) == 0){
	// GET
	$quotation = newQuotation();
	$form_data = quotation_to_form($quotation);
	$errors = array();
	
} else{
	// POST
	$ret_arr = getErrors($_POST);
	$quotation = $ret_arr[0];
	$errors = $ret_arr[1];
	$form_data = $_POST;
	
	if ( sizeof($errors) == 0 ){
		// No errors -| save and redirect
		$quotation['source_id'] = $source_id;
		addQuotation($quotation);
		header( 'HTTP/1.1 303 See Other' );
		header( 'Location: source.php?id='.$source_id);
		mysql_close($db);
		exit;
		
	}else{
		$error_no = 1;
		$error_tags = array();
		foreach($errors as $key=>$value)
			$error_tags[$key] = "*".$error_no++;
	}
}

mysql_close($db);

?>
<?php require_once("pages/main_header.php"); ?>

	  <!-- только для данной страницы -->
	  <h2>Добавление цитаты</h2>
	  
	  <div class="title">
	    <?=htmlspecialchars($source['title'])?>
	  </div>
	  <div class="author">
	    <?=htmlspecialchars($source['author'])?>
	  </div>
	  <div class="url">
	    <a  target="_blank" href="<?=htmlspecialchars($source['url'])?>"><?=htmlspecialchars($source['url'])?></a>
	  </div>
	  <!-- форма -->
	  <form action="quoteadd.php?id=<?=$source_id?>" method="POST">
	    <table class="form">
	      <tr>
	      <!-- description -->
		<td>
		  <label for="description">Описание
		    <?php if(isset($errors['description'])){ ?><span class="error"><?=$error_tags['description']?></span> <?php }?>
		  </label>
		</td>
		<td>
		  <input name="description" autocomplete=off value="<?=htmlspecialchars($form_data['description'])?>"></input>
		</td>
	      </tr><tr>
	      <!-- content -->
		<td>
		  <label for="content">Содержание
		    <?php if(isset($errors['content'])){ ?><span class="error"><?=$error_tags['content']?></span> <?php }?>
		  </label>
		</td>
		<td>
		  <textarea name="content" cols="60" rows="10"><?=htmlspecialchars($form_data['content'])?></textarea>
		</td>
	      </tr><tr>
	      <!-- start date -->
		<td>
		  <label for="startdate">Начало
		    <?php if(isset($errors['startdate'])){ ?><span class="error"><?=$error_tags['startdate']?></span> <?php }?>
		  </label>
		</td>
		<td>
		  <input name="startdate" autocomplete=off value="<?=htmlspecialchars($form_data['startdate'])?>"></input>
		</td>
	      </tr><tr>
	      <!-- end date -->
		<td>
		  <label for="enddate">Конец
		    <?php if(isset($errors['enddate'])){ ?><span class="error"><?=$error_tags['enddate']?></span> <?php }?>
		  </label>
		</td>
		<td>
		  <input name="enddate" autocomplete=off value="<?=htmlspecialchars($form_data['enddate'])?>"></input>
		</td>
	      </tr><tr>
	      <!-- tags -->		
		<td>
		  <label for="tags">Метки</label>
		</td>
		<td>
		  <input name="tags" autocomplete=off value="<?=htmlspecialchars($form_data['tags'])?>" style="width: 30em"></input>
		</td>
	      </tr>
	    </table>
	    <div class="errors">
			<?php foreach($errors as $key => $value){ ?>
						<span class="error"><?=$error_tags[$key]?> <?=$value?></span><br>
			<?php } ?>
	    </div>
	    <input class="button" type="submit" value="Добавить">
	    <input class="button" type="button" value="Отменить" onclick="location='source.php?id=<?=$source_id?>'">
	  </form>

  <?php require_once("pages/main_footer.php"); ?>
