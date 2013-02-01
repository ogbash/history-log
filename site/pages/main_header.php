<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="main.css" rel="stylesheet" type="text/css">
    <script src="js/jquery-1.8.3.js"></script>
  </head>
<body>
<?php 
   $PAGES = array("sources", "tags", "search");
   $PAGENAMES = array("sources"=>"Sources","tags"=>"Tags","search"=>"Search");
   if (!isset($ACTIVE_PAGE)) $ACTIVE_PAGE="";
?>
 <div id="site-header">
      Quote-mining & history-logging
    </div>
    <table>
      <tr>
	<td id="menu">
	  <ul>
   <?php
   foreach ($PAGES as $PAGE) {
     if ($PAGE==$ACTIVE_PAGE)
       echo '<li class="active"><a href="'.$PAGE.'.php">'.$PAGENAMES[$PAGE].'</a></li>';
     else
       echo '<li><a href="'.$PAGE.'.php">'.$PAGENAMES[$PAGE].'</a></li>';
   }
?>
          </ul>
	</td>
	
	<td id="contents">
