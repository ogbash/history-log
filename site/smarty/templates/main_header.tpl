<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="main.css" rel="stylesheet" type="text/css">
    <script src="js/jquery-1.8.3.js"></script>
  </head>
<body>

{$PAGES = ["sources_smarty", "tags", "search"]}
{$PAGENAMES = ["sources_smarty"=>"Источники","tags"=>"Метки","search"=>"Поиск"]}
{if !isset($ACTIVE_PAGE)} {$ACTIVE_PAGE=""} {/if}

<div id="site-header">
      История по цитатам
    </div>
    <table>
      <tr>
	<td id="menu">
	  <ul>
	{foreach from=$PAGES item=PAGE}
		{if $PAGE==$ACTIVE_PAGE}
			<li class="active"><a href="{$PAGE}.php">{$PAGENAMES[$PAGE]}</a></li>
		{else}
			<li><a href="{$PAGE}.php">{$PAGENAMES[$PAGE]}</a></li>
		{/if}
	{/foreach}
     </ul>
	</td>
	
	<td id="contents">