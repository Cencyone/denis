<nav class="gallery-menu">
	<ul class="gallery-menu__list">
		<li class="gallery-menu__item">
			{if $current_category.id}
				<a class="gallery-menu__link" href="{site_url('gallery')}">{tlang('All categories')}</a>
			{else:}
				<span class="gallery-menu__active">{tlang('All categories')}</span>
			{/if}
		</li>
		
		{foreach $gallery_category as $item}
		<li class="gallery-menu__item">
			{if $current_category.id != $item.id}
				<a class="gallery-menu__link" href="{site_url('gallery/category/'.$item.id)}">{$item.name}</a>
			{else:}
				<span class="gallery-menu__active">{$item.name}</span>
			{/if}
		</li>
		{/foreach}
	</ul>
</nav>