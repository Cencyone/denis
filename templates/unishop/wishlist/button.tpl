{$login = $CI->dx_auth->is_logged_in() ? '' : '?wishlist='.$varId}

{if $type == 'button'}

  <!-- Button link, used in catalog page -->
  {if $class != 'btn inWL'}
    <a class="btn-white" href="{$href}{$login}" data-modal rel="nofollow">
      <i class="btn-white__ico btn-white__ico--wishlist">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__heart"></use>
        </svg>
      </i>
    </a>
  {else:}
    <a class="btn-white btn-white--active" href="{site_url('wishlist')}" rel="nofollow">
      <i class="btn-white__ico btn-white__ico--wishlist">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__heart"></use>
        </svg>
      </i>
    </a>
  {/if}

{else:}
  <div class="product-actions__ico product-actions__ico--wishlist">
    <svg class="svg-icon">
      <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__heart"></use>
    </svg>
  </div>
  <!-- Text link, used in product page -->
  {if $class != 'btn inWL'}
    <a class="product-actions__link" href="{$href}{$login}" data-modal rel="nofollow">{tlang('Add to Wishlist')}</a>
  {else:}
    <a class="product-actions__link product-actions__link--open" href="{site_url('wishlist')}"
       rel="nofollow">{tlang('Open in Wishlist')}</a>
  {/if}

{/if}
