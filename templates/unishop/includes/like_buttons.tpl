<ul class="likes">
	{if module('share')->settings['facebook_like']}
		<li class="likes__item likes__item--fb">{module('share')->_make_like_buttons_fb()}</li>
	{/if}
	
	{if module('share')->settings['gg_like']}
		<li class="likes__item likes__item--gp">{module('share')->_make_like_buttons_google()}</li>
	{/if}

	{if module('share')->settings['twitter_like']}
		<li class="likes__item likes__item--tw">{module('share')->_make_like_buttons_twitter()}</li>
	{/if}

	{if module('share')->settings['vk_like'] && module('share')->settings['vk_apiid']}
		<li class="likes__item likes__item--fb">{module('share')->_make_like_buttons_vk()}</li>
	{/if}
</ul>