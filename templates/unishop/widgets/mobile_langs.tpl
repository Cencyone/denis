{if count($languages) > 1}
	<li class="mobile-nav__item mobile-nav__item--separator">{tlang('Language')}</li>
	{foreach $languages as $lang}
		<li class="mobile-nav__item">
				<a class="mobile-nav__link" href="/{echo $lang.identif . $current_address}">{$lang.lang_name}</a>
		</li>
	{/foreach}
{/if}