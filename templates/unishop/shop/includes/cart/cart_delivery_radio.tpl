<div class="delivery-radio"
     data-cart-delivery
>
  {foreach $deliveryMethods as $delivery}
    <div class="delivery-radio__field" data-cart-delivery--item>

      <div class="delivery-radio__control">
        <input type="radio" name="deliveryMethodId" value="{echo $delivery->getId()}"
               id="deliveryMethod_{echo $delivery->getId()}"
               data-cart-delivery--input
               data-cart-delivery--href="{shop_url('cart')}"
               {if $delivery->getId() == get_value('deliveryMethodId')}checked{/if}>
      </div>
      <div class="delivery-radio__content">

        <label class="delivery-radio__title" for="deliveryMethod_{echo $delivery->getId()}">
          {echo $delivery->getName()}
        </label>

        {if trim(strip_tags($delivery->getDescription())) != ""}
          <div class="delivery-radio__info">
            {echo html_entity_decode($delivery->getDescription())}
          </div>
        {/if}

        <!-- Delivery Price is undefined -->
        {if $delivery->getDeliverySumSpecified()}
          <div class="delivery-radio__info">
            {echo $delivery->getDeliverySumSpecifiedMessage()}
          </div>
          <!-- Delivery Price is defined -->
        {/if}

        {if $delivery->getPrice() > 0}
          <div class="delivery-radio__info">
            {tlang('Price')}: {echo str_replace(" ","", emmet_money($delivery->getPrice()))}
          </div>
          {if $delivery->getFreeFrom() > 0}
            <div class="delivery-radio__info">
              {tlang('Free from')}: {echo str_replace(" ","", emmet_money($delivery->getFreeFrom()))}
            </div>
          {/if}
        {/if}

        <div class="delivery-radio__spoiler {if $delivery->getId()!= get_value('deliveryMethodId')}hidden{/if}" data-cart-delivery--spoiler>

          <!-- Nova Poshta module -->
          {if array_key_exists('nova_poshta', $modules)}
            {if module('nova_poshta')->getSelectDeliveryId() == $delivery->getId()}
              {view('nova_poshta/nova_poshta.tpl')}
            {/if}
          {/if}

          <!-- Payment methods selection -->
          {if count($delivery->getPaymentMethodss()) > 0}
            <div class="delivery-radio__spoiler-row">
              <div class="delivery-radio__spoiler-col">
                {tlang('Payment method')}:
              </div>
              <div class="delivery-radio__spoiler-col">
                {view('shop/includes/cart/cart_payment_select.tpl', [
                'delivery' => $delivery
                ])}
              </div>
            </div>
          {/if}

        </div>

      </div><!-- /.content -->

    </div>
    <!-- /.field -->
  {/foreach}
</div><!-- /.delivery-radio -->