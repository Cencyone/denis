{$login = $CI->dx_auth->is_logged_in() ? '' : '?wishlist='.$varId}

{if $class != 'btn inWL'}
  <a class="product-actions__link" href="{$href}{$login}"
     data-modal
     rel="nofollow">{tlang('Add to Wishlist')}</a>
{else:}
  <a class="product-actions__link" href="{site_url('wishlist')}" rel="nofollow">{tlang('Open in Wishlist')}</a>
{/if}