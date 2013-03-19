<h1>Список источников</h1>
	  <span class="button"><a href="sourceadd.php">Добавить новый источник</a></span>
	  <ul class="source">
		{section name=i loop=$arr}
	    <li class="source">
	      <span class="quotecount">{$arr[i]['c']}</span>
	      <span class="title"><a href="source.php?id={$arr[i]['id']}">
			{$arr[i]['title']|escape}</a></span>
	      <span class="author">{$arr[i]['author']|escape}</span>
		  <span class="dates">{$arr[i]['dates']|escape}</span>
	    </li>
		{/section}
	  </ul>