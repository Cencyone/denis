{$total = $CI->session->userdata('shopForCompare') ? count($CI->session->userdata('shopForCompare')) : 0;}

<a class="user-panel__link {if !$total}user-panel__link--empty{/if}"
   href="{shop_url('compare')}"
   rel="nofollow"
   data-compare-removeclass="user-panel__link--empty">
  <i class="user-panel__ico user-panel__ico--compare">
    <svg class="svg-icon">
      <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__compare"></use>
    </svg>
  </i>
  {tlang('Compare')} (<span data-compare-total>{$total}</span>)
</a>