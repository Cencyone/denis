<!-- Found cheaper link -->
<div class="product-actions__item">
  <div class="product-actions__ico product-actions__ico--discount">
    <svg class="svg-icon">
      <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__percentage-discount"></use>
    </svg>
  </div>
  <div class="product-actions__link">
    <button class="link link--main" data-found-cheaper-link="{site_url('found_less_expensive/showButtonWithForm' . '?id=' . $model->getId())}" data-modal="modal_form">
      {tlang('Found cheaper?')}
    </button>
  </div>
</div>