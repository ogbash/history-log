{include file='main_header.tpl'}
<h2>Источник</h2>

<div class="title">
	{$source['title']|escape}
</div>

<div class="author">
	{$source['author']|escape}
</div>

<div class="url">
	<a  target="_blank" href={$source['url']|escape}>{$source['url']|escape}</a>
</div>

<span class="button"><a href="quoteadd.php?id={$source_id}">Добавить цитату</a></span>

<div>
	    <ul class="quotes">
		
		{section name=i loop=$quote}
		
	      <li class="quote">
		<span class="description">
		  {$quote[i]['description']}
		</span>
		
		<span class="dates">
		 {$quote[i]['dates']}
		</span>
		
		<span class="tags">
		{if isset($quote[i]['tags'])}
			{$quote[i]['tags']}
		{/if}
		</span>
		
		<span class="content">
			{$quote[i]['content']}
		</span>
	      </li>
		{/section}
	    </ul>
	  </div>

{include file='main_footer.tpl'}