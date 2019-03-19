{$total = module('wishlist')->getUserWishListItemsCount($CI->dx_auth->get_user_id())}

<a class="user-panel__link {if !$total}user-panel__link--empty{/if}" href="{site_url('wishlist')}" rel="nofollow">{tlang('Wishlist')} ({$total})</a>