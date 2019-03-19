<form class="form form--bg" action="{site_url('shop/profile')}" method="POST">

  {if flashdata('success')}
  <div class="form__messages">
    <div class="message message--success">
      {flashdata('success')}
    </div>
  </div>
  {/if}

  <!-- Unlink from Social Networks -->
  {if array_key_exists('socauth', $modules)}
    <div class="form__field">
      <div class="form__label">{tlang('Linked accounts')}</div>
      <div class="form__inner">
        {module('socauth')->renderLink()}
      </div>
    </div>
  {/if}

  <!-- Name field -->
  {view('includes/forms/input-base.tpl', [
    'label' => tlang('Name'),
    'type' => 'text',
    'name' => 'name',
    'value' => get_value('name') ? : $profile->getName(),
    'required' => true
  ])}

  
  <!-- Email field -->
  <div class="form__field">
    <div class="form__label">{tlang('E-mail')}: {echo $profile->getUserEmail()}</div>
    <input type="hidden" name="email" value="{echo $profile->getUserEmail()}">
  </div>

  
  <!-- Phone field -->
  {view('includes/forms/input-base.tpl', [
    'label' => tlang('Phone number'),
    'type' => 'text',
    'name' => 'phone',
    'value' => get_value('phone') ? : $profile->getPhone(),
    'required' => true
  ])}

  
  <!-- Shipping address field -->
  {view('includes/forms/input-base.tpl', [
    'label' => tlang('Shipping address'),
    'type' => 'text',
    'name' => 'address',
    'value' => get_value('address') ? : $profile->getAddress(),
    'required' => false
  ])}

  <!-- Additional fields output. Could be in several types -->
  {foreach ShopCore::app()->CustomFieldsHelper->getCustomFielsdAsArray('user', $profile->getId()) as $field}

    <!-- Text input field type. field_type_id == 0 -->
    {if $field['field_type_id'] == 0}
      {view('includes/forms/input-base.tpl', [
        'label' => $field['field_label'],
        'type' => 'text',
        'name' => 'custom_field['.$field['id'].']',
        'value' => get_value('custom_field['.$field["id"].']')?:$field['field_data'],
        'required' => $field['is_required'],
        'desc' => $field['field_description']
      ])}
    <!-- File input field type. field_type_id == 3 -->
    {elseif $field['field_type_id'] == 3}
    <!-- Textarea field type. field_type_id == 1 Do not output in cart page -->
    {else:}
      {view('includes/forms/textarea-base.tpl', [
        'label' => $field['field_label'],
        'name' => 'custom_field['.$field['id'].']',
        'value' => get_value('custom_field['.$field["id"].']')?:$field['field_data'],
        'required' => $field['is_required'],
        'desc' => $field['field_description'],
        'rows' => 5
      ])}      
    {/if}

  {/foreach}

  <!-- Submit button -->
  <div class="form__field">
    <div class="form__label"></div>
    <div class="form__inner">
      <input class="btn btn-primary" type="submit" value="{tlang('Save changes')}">
    </div>
  </div>

{form_csrf()}
</form>