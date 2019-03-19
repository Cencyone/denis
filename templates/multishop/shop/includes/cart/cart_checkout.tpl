<form class="form" action="{site_url('shop/order/make_order')}" method="post" data-cart-checkout-form>

  {$loc_validation_errors = $CI->session->flashdata('validation_errors')}

  {if $loc_validation_errors}
    <div class="form__messages">
      <div class="message message--error">{$loc_validation_errors}</div>
    </div>
  {/if}

  <!-- Name field -->
  {view('includes/forms/input-base.tpl', [
    'label' => tlang('Name'),
    'type' => 'text',
    'name' => 'userInfo[fullName]',
    'value' => get_value('userInfo[fullName]') ? : $profile['name'],
    'required' => $isRequired['userInfo[fullName]']
  ])}

  <!-- Email field -->
  {view('includes/forms/input-base.tpl', [
    'label' => tlang('E-mail'),
    'type' => 'email',
    'name' => 'userInfo[email]',
    'value' => get_value('userInfo[email]') ? : $profile['email'],
    'required' => $isRequired['userInfo[email]']
  ])}

  <!-- Phone field -->
  {view('includes/forms/input-base.tpl', [
    'label' => tlang('Phone number'),
    'type' => 'text',
    'name' => 'userInfo[phone]',
    'value' => get_value('userInfo[phone]') ? : $profile['phone'],
    'required' => $isRequired['userInfo[phone]']
  ])}

  <!-- Shipping address field -->
  {view('includes/forms/input-base.tpl', [
    'label' => tlang('Shipping address'),
    'type' => 'text',
    'name' => 'userInfo[deliverTo]',
    'value' => get_value('userInfo[deliverTo]') ? : $profile['address'],
    'required' => $isRequired['userInfo[deliverTo]']
  ])}

  <!-- Delivery and payment methods -->
  {if count($deliveryMethods) > 0}
  <div class="form__field">
    <div class="row">
      <div class="col-md-4">
        <div class="form__label">
          {tlang('Shipping & payment')}
        </div>
      </div>
      <div class="col-md-8">
        {view('shop/includes/cart/cart_delivery_radio.tpl')}
      </div>
    </div>
  </div>
  {/if}


  <!-- Additional fields output. Could be in several types -->
  {foreach ShopCore::app()->CustomFieldsHelper->getCustomFielsdAsArray('order') as $field}

    <!-- Text field type. field_type_id == 0 -->
    {if $field['field_type_id'] == 0}
      {view('includes/forms/input-base.tpl', [
        'label' => $field['field_label'],
        'type' => 'text',
        'name' => 'custom_field['.$field['id'].']',
        'value' => get_value('custom_field['.$field["id"].']'),
        'required' => $field['is_required'],
        'desc' => $field['field_description']
      ])}
    <!-- File input field type. field_type_id == 3 -->
    {elseif $field['field_type_id'] == 3}
    <!-- Textarea field type. field_type_id == 1 -->
    {else:}
      {view('includes/forms/textarea-base.tpl', [
        'label' => $field['field_label'],
        'name' => 'custom_field['.$field['id'].']',
        'value' => get_value('custom_field['.$field["id"].']'),
        'required' => $field['is_required'],
        'desc' => $field['field_description'],
        'rows' => 5
      ])}
    {/if}

  {/foreach}

  
  <!-- Comments about order -->
  {view('includes/forms/textarea-base.tpl', [
    'label' => tlang('Comments about your order'),
    'name' => 'userInfo[commentText]',
    'value' => get_value('userInfo[commentText]'),
    'rows' => 5
  ])}

  <!-- System bonus module -->
  {if array_key_exists('system_bonus', $modules)}
    {module('system_bonus')->getCartInput()}
  {/if}

  <!-- Submit button -->
  <div class="form__field">
    <div class="form__label"></div>
    <div class="form__inner">
      <input class="btn btn-primary btn-lg" type="submit" value="{tlang('Confirm your order')}" data-cart-checkout-form-button>
    </div>
  </div>

  <div class="hidden" data-ajax-inject="cart-coupon">
  {if $gift_key}
    <input type="hidden" name="gift" value="{echo $gift_key}">
    <input type="hidden" name="gift_ord" value="1">
  {/if}
  </div>
  
{form_csrf()}
</form>