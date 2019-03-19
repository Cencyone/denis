<li class="mobile-nav__item" data-mobile-nav-item>
	<a class="mobile-nav__link" href="{$link}" {if $wrapper} data-mobile-nav-link{/if} {$target}>
    {$title}
    {if $wrapper}<span class="mobile-nav__has-children"><svg class="svg-icon"><use xlink:href="#svg-icon__angle-right"></use></svg></span>{/if}
  </a>
	{$wrapper}
</li>