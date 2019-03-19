<li class="overlay__item overlay__item--active" {if $wrapper}data-global-doubletap{/if}>
	<a class="overlay__link" href="{$link}" {$target}>
		<span>{$title}</span>
		{if $wrapper}<i class="overlay__icon" aria-hidden="true"><svg class="svg-icon svg-icon--caret"><use xlink:href="#svg-icon__caret-right"></use></svg></i>{/if}
	</a>
	{$wrapper}
</li>