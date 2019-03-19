<div class="cart-summary__subtotal">

  <!-- Total origin price -->
  {if $model->getOriginPrice() != $model->getFinalPrice()}
    <div class="cart-summary__subtotal-item">
      <div class="cart-summary__subtotal-title">
        {tlang('Subtotal')}
      </div>
      <div class="cart-summary__subtotal-value">
        <div class="cart-price">
          <div class="cart-price__main cart-price__main--small">
            {echo emmet_money($model->getOriginPrice(), 'span.cart-price__main-value', '', 'span.cart-price__main-cur')}
          </div>
        </div>
      </div>
    </div>
  {/if}


  <!-- Total discount -->
  {if $model->getDiscountValue() > 0}
    <div class="cart-summary__subtotal-item">
      <div class="cart-summary__subtotal-title">
        {tlang('Your discount')}
      </div>
      <div class="cart-summary__subtotal-value">
        <div class="cart-price">
          <div class="cart-price__main cart-price__main--small">
            {echo emmet_money($model->getDiscountValue(), 'span.cart-price__main-value', '', 'span.cart-price__main-cur')}
          </div>
        </div>
      </div>
    </div>
  {/if}

  <!-- Total points from system bonus module -->
  {if array_key_exists('system_bonus', $modules) && module('system_bonus')->getCartTotalBonus()}
    <div class="cart-summary__subtotal-item">
      <div class="cart-summary__subtotal-title">
        {tlang('Earned points')}
      </div>
      <div class="cart-summary__subtotal-value">
        <div class="cart-price">
          <div class="cart-price__main cart-price__main--small">
            {module('system_bonus')->getCartTotalBonus()}
            {echo SStringHelper::Pluralize($loc_points, array(tlang('system_bonus_points_pluralize_1'), tlang('system_bonus_points_pluralize_2'), tlang('system_bonus_points_pluralize_3')));}
          </div>
        </div>
      </div>
    </div>
  {/if}


  <!-- Delivery price -->
  {if $model->getDeliveryPrice() > 0}
    <div class="cart-summary__subtotal-item">
      <div class="cart-summary__subtotal-title">
        {tlang('Shipping')}
      </div>
      <div class="cart-summary__subtotal-value">
        <div class="cart-price">
          <div class="cart-price__main cart-price__main--small">
            {echo emmet_money($model->getDeliveryPrice(), 'span.cart-price__main-value', '', 'span.cart-price__main-cur')}
          </div>
        </div>
      </div>
    </div>
  {/if}

  <!-- Gift card code -->
  {if $model->getGiftValue() > 0}
    <div class="cart-summary__subtotal-item">
      <div class="cart-summary__subtotal-title">
        {tlang('Gift card')}
      </div>
      <div class="cart-summary__subtotal-value">
        <div class="cart-price">
          <div class="cart-price__main cart-price__main--small">
            {echo emmet_money($model->getGiftValue(), 'span.cart-price__main-value', '', 'span.cart-price__main-cur')}
          </div>
        </div>
      </div>
    </div>
  {/if}

</div><!-- /.__subtotal -->


<div class="cart-summary__total">

  <!-- Gift coupon. Not visible in order view page -->
  {if $parent_coupon}
    <div class="cart-summary__total-coupon">
      <form class="form" action="{shop_url('cart')}" method="post" data-cart-summary--coupon>
        {if $model->getGiftValue() > 0}
          <div>{tlang('Your card was successfully applied')}</div>
          <div class="hidden" data-ajax-grab="cart-coupon">
            <input type="hidden" name="gift_ord" value="1">
            <input type="hidden" name="gift" value="{$gift_key}">
          </div>
        {else:}
          <div class="input-group">
            <input class="form-control" type="text" name="gift" value="" placeholder="{tlang('Enter gift card code')}">
            <div class="input-group-btn">
              <button class="btn btn-default" type="submit">{tlang('Apply')}</button>
            </div>
          </div>
        {/if}

        {if $gift_error}
          <div class="form__info form__info--error">{tlang('Code you entered was not valid')}</div>
        {/if}

        {if $_POST['deliveryMethodId']}
          <input type="hidden" name="deliveryMethodId" value="{$_POST['deliveryMethodId']}">
        {/if}
        {form_csrf()}
      </form>
    </div>
  {/if}


  <div class="cart-summary__total-price {if $parent_type == 'order'}cart-summary__total-price--order{/if}">
    <div class="cart-summary__total-label">
      {if $parent_type == 'order'}
        {tlang('Estimated Total')}
      {else:}
        {tlang('Cart modal total price')}
      {/if}
    </div>
    <div class="cart-summary__total-value">
      <div class="cart-price">
        <div class="cart-price__main cart-price__main--lg">
          {echo emmet_money($model->getFinalPrice(), 'span.cart-price__main-value', '', 'span.cart-price__main-cur')}
        </div>
        {$loc_additional_prices = emmet_money_additional($model->getFinalPrice(), 'span.cart-price__addition-value', '', 'span.cart-price__addition-cur')}
        {if count($loc_additional_prices) > 0}
          <div class="cart-price__addition">
            {foreach $loc_additional_prices as $additional_price}
              <div class="cart-price__addition-item">
                {$additional_price}
              </div>
            {/foreach}
          </div>
        {/if}
      </div>
    </div>
  </div>

</div>