<div class="form-input {$parent_mod_class}" data-form-quantity {echo $parent_cart ? 'data-form-quantity-submit' : ''}>
  <div class="form-input__group">
    <div class="form-input__group-item">
      <button class="form-input__group-btn" type="button" data-form-quantity-control="minus">-</button>
    </div>
    <input class="form-input__control form-input__control--quantity" type="text" name="{$parent_name}" autocomplete="off"
           value="{$parent_value}"
           data-cart-summary--quantity-field
           data-form-quantity-field
           data-form-quantity-step="1">
    <div class="form-input__group-item">
      <button class="form-input__group-btn" type="button" data-form-quantity-control="plus">+</button>
    </div>
  </div>
</div>