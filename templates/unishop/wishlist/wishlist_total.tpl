{$total = module('wishlist')->getUserWishListItemsCount($CI->dx_auth->get_user_id())}

<a class="user-panel__link {if !$total}user-panel__link--empty{/if}" href="{site_url('wishlist')}" rel="nofollow">
  <i class="user-panel__ico user-panel__ico--wishlist">
    <svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__heart"></use></svg>
  </i>
  {tlang('Wishlist')} ({$total})
</a>