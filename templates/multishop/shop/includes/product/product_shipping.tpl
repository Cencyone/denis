<div class="product-shipping">

  <!-- Delivery Methods -->
  {if count($delivery_methods) > 0}
    <div class="product-shipping__row">
      <div class="product-shipping__header">
        <span class="product-shipping__icon" aria-hidden="true"><svg class="svg-icon">
            <use xlink:href="#svg-icon__truck"></use>
          </svg></span>
        <div class="product-shipping__title">{tlang('Shipping methods')}</div>
      </div>
      <ul class="product-shipping__list">
        {foreach $delivery_methods as $item}
          {$loc_desc = trim(strip_tags($item->getDescription())) != "" || $item->getPrice() > 0 ? "product-shipping__tooltip-link" : ""}
          <li class="product-shipping__item">
            <div class="product-shipping__tooltip">
              <span class="{$loc_desc}">{echo $item->getName()}</span>
              {if $loc_desc}
                <div class="product-shipping__tooltip-wrapper">
                  {if trim(strip_tags($item->getDescription())) != ""}
                    <div class="product-shipping__tooltip-item">
                      {echo $item->getDescription()}
                    </div>
                  {/if}
                  <!-- Delivery Price is undefined -->
                  {if $item->getDeliverySumSpecified()}
                    <div class="product-shipping__tooltip-item">
                      {echo $item->getDeliverySumSpecifiedMessage()}
                    </div>
                    <!-- Delivery Price is defined -->
                  {/if}
                  {if $item->getPrice() > 0}
                    <div class="product-shipping__tooltip-item">
                      {tlang('Price')}: {echo str_replace(" ","", emmet_money($item->getPrice()))}<br>
                      {if $item->getFreeFrom() > 0}
                        {tlang('Free from')}: {echo str_replace(" ","", emmet_money($item->getFreeFrom()))}
                      {/if}
                    </div>
                  {/if}
                </div>
              {/if}
            </div>
          </li>
        {/foreach}
      </ul>
    </div>
  {/if}

  <!-- Payment Methods -->
  {if count($payments_methods) > 0}
    <div class="product-shipping__row">
      <div class="product-shipping__header">
        <span class="product-shipping__icon" aria-hidden="true"><svg class="svg-icon">
            <use xlink:href="#svg-icon__credit-card"></use>
          </svg></span>
        <div class="product-shipping__title">{tlang('Payment methods')}</div>
      </div>
      <ul class="product-shipping__list">
        {foreach $payments_methods as $item}
          {$loc_desc = trim(strip_tags($item->getDescription())) != "" ? "product-shipping__tooltip-link" : ""}
          <li class="product-shipping__item">
            <div class="product-shipping__tooltip">
              <span class="{$loc_desc}">{echo $item->getName()}</span>
              {if $loc_desc}
                <div class="product-shipping__tooltip-wrapper">
                  <div class="product-shipping__tooltip-item">
                    {echo $item->getDescription()}
                  </div>
                </div>
              {/if}
            </div>
          </li>
        {/foreach}
      </ul>
    </div>
  {/if}

  <!-- Phones -->
  {if siteinfo('mainphone')}
    <div class="product-shipping__row">
      <div class="product-shipping__header">
        <span class="product-shipping__icon" aria-hidden="true"><svg class="svg-icon">
            <use xlink:href="#svg-icon__phone"></use>
          </svg></span>
        <div class="product-shipping__title">{tlang('Questions? Ask our experts')}</div>
      </div>
      <p class="product-shipping__desc">
        {tlang('Call:')} <span class="product-shipping__phone">{nl2br(siteinfo('mainphone'))}</span><br/>
        {tlang('or')} <a class="site-info__link" href="{site_url('callbacks')}"
                         data-modal="callbacks_modal">{tlang('order a callback')}</a>
      </p>
    </div>
  {/if}

</div><!-- /.product-shipping -->