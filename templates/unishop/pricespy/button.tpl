{$loc_is_following = strpos($class, "inSpy") ? 1 : 0}
<div class="product-actions__item">
  <div class="product-actions__ico product-actions__ico--increase">
    <svg class="svg-icon">
      <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__profit-chart"></use>
    </svg>
  </div>
  <div class="product-actions__link">
    <a class="link link--main" rel="nofollow"
            {if $parent_login}
      href="{site_url('auth')}" data-modal
            {else:}
      href="#data-pricespy-modal" data-pricespy-link
            {/if}>
      {tlang('Follow price')}
    </a>
  </div>
</div>

<!-- Modal window with messages before adding to spy -->
<div class="modal modal--sm mfp-hide" id="data-pricespy-modal" data-pricespy-modal>

  <!-- Modal Header -->
  {view('includes/modal/modal_header.tpl', [
  'title' => tlang('Follow price')
  ])}

  <!-- Modal Content -->
  <div class="modal__content">

    <!-- Visible when product has not been followed -->
    <div class="modal__content-cell {if $loc_is_following}hidden{/if}" data-pricespy-is-following-hide>
      <div class="typo">
        {tlang('Follow the price and we will send you an email once the product price changes')}
      </div>
    </div>


    <!-- -->
    <div class="modal__content-cell {if !$loc_is_following}hidden{/if}" data-pricespy-is-following-show>
      <div class="typo">
        {tlang("You are following the price changes of this item. You will receive a notification to your contact email when the product price will be changed. Check your personal account to cancel the subscription and view other items that you're following.")}
      </div>
    </div>

  </div><!-- /.modal__content -->

  <!-- Modal Footer -->
  <div class="modal__footer">
    <div class="modal__footer-row">
      <div class="modal__footer-btn hidden-xs">
        <button class="btn btn-default" type="reset" data-modal-close>{tlang('Close')}</button>
      </div>
      <div class="modal__footer-btn {if $loc_is_following}hidden{/if}" data-pricespy-is-following-hide>
        <button class="btn btn-primary" type="button"
                data-pricespy-href="{site_url('/pricespy/spy/' . $data[Id])}"
                data-pricespy-variant="{$data[varId]}"
                data-pricespy-is-following="{$loc_is_following}">{tlang('Follow price')}</button>
      </div>
      <div class="modal__footer-btn {if !$loc_is_following}hidden{/if}" data-pricespy-is-following-show>
        <a class="btn btn-primary" type="button" href="{site_url('pricespy')}">{tlang('View items')}</a>
      </div>
    </div>
  </div>

  <input type="hidden" name="variant_id" value="{echo $variant_id}">
  {form_csrf()}

</div><!-- /.modal -->