<?php
ini_set('display_errors','1');

require_once("lib/utils.php");

$months_rus = array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь",
					"Октябрь", "Ноябрь", "Декабрь");

function newSearch() {
	return array ('f_startdate'=>'', 'f_enddate'=>'', 'f_tags'=>'');
}

function toSqlDate($dateTime) {
	return $dateTime->format('Y-m-d H:i:s');
}

function makeSearch($search) {

	$tags_array = $search['f_tags'];
	for($i=0; $i<sizeof($tags_array); $i++)
		$tags_array[$i] = "'".$tags_array[$i]."'";
	$tags_quoted = implode(",",$tags_array);

	$searchQueryStr = "SELECT * FROM quotations JOIN quotation_tags AS qts JOIN tags
					WHERE tags.id=qts.tag_id AND quotations.id=qts.quotation_id
					AND tags.name IN (".$tags_quoted.")";

	if (!($search['f_startdate'] == ''))
		$searchQueryStr .= " AND (quotations.start_time IS NULL 
						OR quotations.start_time>='".toSqlDate($search['f_startdate'])."')";
		
	if (!($search['f_enddate'] == ''))
		$searchQueryStr .= " AND (quotations.end_time IS NULL 
						OR quotations.end_time<='".toSqlDate($search['f_enddate'])."')";
						
	$searchQueryStr .= " GROUP BY quotations.id ORDER BY quotations.start_time";
	
	//echo($searchQueryStr);
						
	$searchQuery = mysql_query($searchQueryStr) or die(mysql_error());
	
	return $searchQuery;
}

function getErrors($search) {

	$errors = array();
	$new_search = newSearch();

	if (!($search['f_startdate'] == "")){
		try{
			$d = new DateTime($search['f_startdate']);
			$new_search['f_startdate'] = $d;
		}catch(Exception $e){
			$errors['f_startdate'] = "ошибка формата";
		}
	}
	
	if (!($search['f_enddate'] == "")){
		try{
			$d = new DateTime($search['f_enddate']);
			$new_search['f_enddate'] = $d;
		}catch(Exception $e){
			$errors['f_enddate'] = "ошибка формата";
		}
	}
	
	if ($search['f_tags'] == "")
		$errors['f_tags'] = "введите искомые слова";
	else
		$new_search['f_tags'] = $search['f_tags'];
	
	$new_search['f_tags'] = array_map('trim', explode('&' , $search['f_tags']));
	
	return array($new_search, $errors);
}

$search = newSearch();
$errors = array();
$form = $search;

if (sizeof($_GET)>0){
	$form = $_GET;
	foreach($search as $key=>$value)
			$search[$key] = $_GET[$key];
	$ret_err = getErrors($search);
	$search = $ret_err[0];
	$errors = $ret_err[1];
	
	if (sizeof($errors)==0){
		require_once('database_connect.php');
		$searchQuery = makeSearch($search);
		$searchResults = array();
		while($fetchSearch = mysql_fetch_array($searchQuery)){
			$searchResults[] = $fetchSearch;
			if($fetchSearch['start_time'] != "NULL"){
				$date = new DateTime($fetchSearch['start_time']);
				$year = $date->format('Y');
				$month = $months_rus[$date->format('m')-1];
			}
			else{
				$year = "Год не указан";
				$month = "Месяц не указан";
			}
			$searchResults[sizeof($searchResults)-1]['year'] = $year;
			$searchResults[sizeof($searchResults)-1]['month'] = $month;
		}
		mysql_close($db);
	}
	
	else {
		$error_tags = array();
		$error_no = 1;
		foreach($errors as $key=>$value){
			$error_tags[$key] = "*".$error_no;
			$error_no++;
		}
	}
}

?>

<?php
$ACTIVE_PAGE="search";
require_once("pages/main_header.php"); ?>
	
	<script>
	  function item_visible(elem, visible) {
	    if (!visible) {
	      elem.classList.add("item-closed")
	      elem.classList.remove("item-opened")
            } else {
	      elem.classList.add("item-opened")
	      elem.classList.remove("item-closed")
            }
	  }

	  function toggle_year(year) {
	    elem = document.getElementById("y"+year)
	    elem_det = document.getElementById("y"+year+"-details")
            isVisible = !(elem_det.style.display=="none")

            // change arrow
	    item_visible(elem, !isVisible)

	    // show/hide details
	    if (isVisible) display = "none"
	    else display = "block"
	    elem_det.style.display = display
          }

	  function toggle_quote(quoteId) {
	    elem = document.getElementById("q"+quoteId)
	    elem_det = document.getElementById("q"+quoteId+"-details")
            isVisible = !(elem_det.style.display=="none")

            // change arrow
	    item_visible(elem, !isVisible)

	    // show/hide details
	    if (isVisible) display = "none"
	    else display = "block"
	    elem_det.style.display = display
	  }
	  $(document).ready(function(){
		$("li > span:first-child").click(function () {
			var li = $(this).parent();
			var closed = li.hasClass("item-closed");
			var children = li.children();
			if (closed) {
				children.filter(".collapsable").show();
			} else {
				children.filter(".collapsable").hide();
			}
			li.toggleClass("item-closed item-opened")
		});
	});

	  
	</script>
	  <h1>Поиск цитат</h1>
	  <form action="search.php" method="GET">
	    <table class="form">
	     <tr>
	      <!-- start date -->
		<td>
		  <label for="f_startdate">Начало
		  <?php if(isset($error_tags['f_startdate'])){ ?><span class="error"><?=$error_tags['f_startdate']?>
		  </span> <?php }?></label>
		</td>
		<td>
		  <input name="f_startdate" autocomplete=off value="<?=$form['f_startdate']?>"></input>
		</td>
	      </tr><tr>
	      <!-- end date -->
		<td>
		  <label for="f_enddate">Конец
		  <?php if(isset($error_tags['f_enddate'])){ ?><span class="error"><?=$error_tags['f_enddate']?>
		  </span> <?php }?></label>
		</td>
		<td>
		  <input name="f_enddate" autocomplete=off value="<?=$form['f_enddate']?>"></input>
		</td>
	      </tr><tr>
	      <!-- tags -->
		<td>
		  <label for="f_tags">Метки
		  <?php if(isset($error_tags['f_tags'])){ ?><span class="error"><?=$error_tags['f_tags']?></span> <?php }?>
		  </label>
		</td>
		<td>
		  <input name="f_tags" autocomplete=off value="<?=$form['f_tags']?>" style="width: 40em"></input>
		</td>
	      </tr>
	    </table>
	    <input class="button" type="submit" value="Искать">

	  </form>
	  
	  <div class="errors">
			<?php foreach($errors as $key => $value){ ?>
						<span class="error"><?=$error_tags[$key]?> <?=$value?></span><br>
			<?php } ?>
	    </div>

	  <h2>Результаты поиска</h2>
	  <div id="resultCanvas">
	    <canvas width="600" style="cursor: pointer;"> </canvas>
	    <div></div>
	  </div>
	  
	  <ul class="years">
	  
	  <?php 	if(isset($searchResults)) {
				for($i=0; $i<sizeof($searchResults); $i++){
				$year = $searchResults[$i]['year'];
				$month = $searchResults[$i]['month'];
		?>
		<?php if($i==0 || $year != $searchResults[$i-1]['year']) { ?>
        <li class="item-closed" id="<?="y".$year?>">
			<!-- start another year -->
			<span class="item-header"><?=$year?></span>
			<!-- months -->
			<ul class="collapsable months" id="<?="y".$year."-details"?>" style="display: none">
		<?php } ?>
			<?php if($i==0 || $month != $searchResults[$i-1]['month']) { ?>
				<!-- start another month -->
				<li class="item-closed" id="y1991m1"><span class="item-header"><?=$month?></span>
			<?php } ?>
					<ul class="collapsable quotes" style="display: none">
						<!-- цитата -->
						<li class="quote item-closed" id="<?="q".$searchResults[$i]['quotation_id']?>">
							<span class="description item-header">
								<?=$searchResults[$i]['description']?>
							</span>
							<div class="quote-details collapsable" id="<?="q".$searchResults[$i]['quotation_id']."-details"?>" style="display:none">
								<span class="dates">
									<?=$searchResults[$i]['start_time']?> - <?=$searchResults[$i]['end_time']?>
								</span>
								<span class="tags">
									tags
								</span>
								<span class="content">
                                         				&laquo;<?=nl2br(trim(htmlspecialchars($searchResults[$i]['content'])))?>&raquo;
								</span>
							</div>
						</li>
					</ul> <!-- quotes -->
			<?php if($i==sizeof($searchResults)-1 || $month != $searchResults[$i+1]['month']) { ?>
				</li>
			<?php } ?>
		<?php if($i==sizeof($searchResults)-1 || $year != $searchResults[$i+1]['year']) { ?>
			</ul> <!-- months -->
	    </li>
		<?php } ?>
		<?php
			} }?>
		</ul>

	    <script src="js/timeline.js"></script>
	    <script>
	    var quotations = {
	    <?php
	    foreach($searchResults as $result) {
	      echo $result["quotation_id"].": {";
	      echo 'id:'.$result["quotation_id"].',';
	      echo 'start_time: '.javascriptDate(new DateTime($result['start_time'])).',';
	      echo 'end_time: '.javascriptDate(new DateTime($result['end_time'])).'';
	      echo "},\n";
	    } ?>
	    };
qc = new QuotationsCanvas($("#resultCanvas"), quotations);
	    </script>

<?php require_once("pages/main_footer.php"); ?>
