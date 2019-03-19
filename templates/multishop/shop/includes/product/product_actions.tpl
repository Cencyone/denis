<div class="product-actions {$parent_modifier}">
  <!-- Wishlist buttons. Dont show button on whishlist page -->
  {if !$parent_wishlist_item}
    <div class="product-actions__item"
         data-ajax-inject="wishlist-btn-{echo $model->firstVariant->getId()}"
    >
      {module('wishlist')->renderWLButton($model->firstVariant->getId());}
    </div>
  {/if}
  <!-- Edit and remove buttons. Display only on wishlist page -->
  {if $parent_wishlist_item}
    <div class="product-actions__item">
      <a class="product-actions__link"
         href="{site_url('/wishlist/deleteItem/' . $parent_wishlist_item.variant_id .'/'. $parent_wishlist_item.wish_list_id)}">{tlang('Remove from list')}</a>
      /
      <a class="product-actions__link"
         href="{site_url('/wishlist/renderPopup/'.  $parent_wishlist_item.variant_id .'/'. $parent_wishlist_item.wish_list_id  .'/'. $user.id)}"
         data-modal
      >{tlang('Change list')}</a>
    </div>
  {/if}
  <!-- "Add to" or "Open in" compare buttons -->
  <div class="product-actions__item">
    {view('shop/includes/compare/compare_button.tpl', [
      'model' => $model
    ])}
  </div>
</div>