{if !$CI->dx_auth->is_logged_in()}
	<li class="mobile-nav__item" data-mobile-nav-item>
		<a class="mobile-nav__link" href="{site_url('auth')}" rel="nofollow">{tlang('Sign in')}</a>
	</li>
	<li class="mobile-nav__item" data-mobile-nav-item>
		<a class="mobile-nav__link" href="{site_url('auth/register')}" rel="nofollow">{tlang('Create Account')}</a>
	</li>
{else:}
	<li class="mobile-nav__item" data-mobile-nav-item>
		<a class="mobile-nav__link" href="{shop_url('profile')}" rel="nofollow">{tlang('Your Account')}</a>
	</li>
	<li class="mobile-nav__item" data-mobile-nav-item>
		<a class="mobile-nav__link" href="{site_url('auth/change_password')}" rel="nofollow">{tlang('Change Password')}</a>
	</li>    
{/if}
	
<li class="mobile-nav__item">
	<a class="mobile-nav__link" href="{shop_url('cart')}" rel="nofollow">{tlang('Cart')}</a>
</li>

{if $CI->dx_auth->is_logged_in()}
<li class="mobile-nav__item">
	<a class="mobile-nav__link" href="{site_url('wishlist')}" rel="nofollow">{tlang('Wishlist')}</a>
</li>
{/if}

<li class="mobile-nav__item">
	<a class="mobile-nav__link" href="{shop_url('compare')}" rel="nofollow">{tlang('Compare')}</a>
</li>

{if $CI->dx_auth->is_logged_in() && array_key_exists('pricespy', $modules)}
	<li class="mobile-nav__item">
		<a class="mobile-nav__link" href="{site_url('pricespy')}" rel="nofollow">{tlang('Tracking price')}</a>
	</li>
{/if}

{if $CI->dx_auth->is_logged_in()}
<li class="mobile-nav__item">
	<a class="mobile-nav__link" href="{site_url('auth/logout')}" rel="nofollow">{tlang('Sign out')}</a>
</li>
{/if}