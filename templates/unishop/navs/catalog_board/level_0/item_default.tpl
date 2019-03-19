<li class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
	<div class="board-nav">
		<a class="board-nav__header" href="{$link}">
			{if trim($image)}
			<div class="board-nav__imagebox">
				<img class="board-nav__img" src="{$image}" alt="{$title}">
			</div>
			{/if}
			<span class="board-nav__title">{$title}</span>
		</a>	
		{$wrapper}
	</div>
</li>