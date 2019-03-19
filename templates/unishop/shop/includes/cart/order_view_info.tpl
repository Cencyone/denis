<div class="content__row">
  <div class="order-details">
    <div class="order-details__group">
      <div class="typo">
        <p>{tlang('Thank you for shopping at')} {echo $_SERVER['HTTP_HOST']}</p><p>{tlang('Your order details are below')}</p>
      </div>
    </div>
    <div class="order-details__group">      
      <!-- Order id number -->
      <div class="order-details__list">
        <div class="order-details__item order-details__item--title">{tlang('Order ID')}</div>
        <div class="order-details__item">{echo $model->getId()}</div>
      </div>
      
      <!-- Order status -->
      <div class="order-details__list">
        <div class="order-details__item order-details__item--title">{tlang('Order status')}</div>
        <div class="order-details__item">{echo $model->getSOrderStatuses()->setLocale(MY_Controller::getCurrentLocale())->getName()}</div>
      </div>

      <!-- Order date -->
      <div class="order-details__list">
        <div class="order-details__item order-details__item--title">{tlang('Order date')}</div>
        <div class="order-details__item">{tpl_locale_date('d F Y', $model->getDateCreated())}</div>
      </div>
    </div><!-- /.order-details__group -->

    <div class="order-details__group">      
      <!-- Shipping method -->
      {if $model->getSDeliveryMethods()}
      <div class="order-details__list">
        <div class="order-details__item order-details__item--title">{tlang('Shipping method')}</div>
        <div class="order-details__item">{echo $model->getSDeliveryMethods()->getName()}</div>
      </div>
      {/if}

      {if $paymentMethod}
      <!-- Payment method -->
      <div class="order-details__list">
        <div class="order-details__item order-details__item--title">{tlang('Payment method')}</div>
        <div class="order-details__item">{echo $paymentMethod->getName()}</div>
      </div>
      {/if}

      <!-- Payment status -->
      <div class="order-details__list">
        <div class="order-details__item order-details__item--title">{tlang('Payment status')}</div>
        <div class="order-details__item">{echo $model->getPaid() ? tlang('Paid successfully') : tlang('Not paid')}</div>
      </div>
      
      {if $paymentMethod and !$model->getPaid() and $paymentMethod->getPaymentSystemName()}
      <div class="btn-order">
        {echo $paymentMethod->getPaymentForm($model)}
      </div>
      {/if}
    </div><!-- /.order-details__group -->

    <div class="order-details__group">      
      <!-- User name  -->
      {if $model->getUserFullName()}
      <div class="order-details__list">
        <div class="order-details__item order-details__item--title">{tlang('Name')}</div>
        <div class="order-details__item">{echo $model->getUserFullName()}</div>
      </div>
      {/if}

      <!-- User email -->
      {if $model->getUserEmail()}
      <div class="order-details__list">
        <div class="order-details__item order-details__item--title">{tlang('E-mail')}</div>
        <div class="order-details__item">{echo $model->getUserEmail()}</div>
      </div>
      {/if}

      <!-- User phone number -->
      {if $model->getUserPhone()}
      <div class="order-details__list">
        <div class="order-details__item order-details__item--title">{tlang('Phone number')}</div>
        <div class="order-details__item">{echo $model->getUserPhone()}</div>
      </div>
      {/if}

      <!-- User shipping address -->
      {if $model->getUserDeliverTo()}
      <div class="order-details__list">
        <div class="order-details__item order-details__item--title">{tlang('Shipping address')}</div>
        <div class="order-details__item">{echo $model->getUserDeliverTo()}</div>
      </div>
      {/if}

      <!-- User shipping address -->
      {if $model->getUserComment()}
      <div class="order-details__list">
        <div class="order-details__item order-details__item--title">{tlang('Comments about your order')}</div>
        <div class="order-details__item">{echo nl2br($model->getUserComment())}</div>
      </div>
      {/if}
      
      <!-- Custom fields -->
      {foreach ShopCore::app()->CustomFieldsHelper->getCustomFielsdAsArray('order', $model->getId()) as $item}
        {if $item.field_data}
        <div class="order-details__list">
          <div class="order-details__item order-details__item--title">{$item.field_label}</div>
          <div class="order-details__item">
          {if $item.field_type_id == 3}
            <a class="btn btn--va-m btn-default btn-xs" href="{media_url($item.field_data)}" target="_blank">
              <i class="btn-default__ico btn-default__ico--download"><svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__download"></use></svg></i>
              <span> {tlang('Download')}</span>
            </a>
          {else:}
            {nl2br($item.field_data)}
          {/if}
          </div>
        </div>
        {/if}
      {/foreach}
    </div><!-- /.order-details__group -->
  </div><!-- /.order-details -->
</div><!-- /.content__row -->