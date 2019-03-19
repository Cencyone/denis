<article class="product-cut {if tpl_is_product_archived($model)}product-cut--no-overlay{/if}"
         data-product-scope>

  <!-- Block visible once page is loaded -->
  <div class="product-cut__main-info">

    <!-- Photo output BEGIN -->
    <div class="product-cut__photo">
      {view('shop/includes/product/product_cut_photo.tpl', [
      'model' => $model
      ])}
    </div>

    <!-- Sales product takes part in via mod_link module -->
    {if array_key_exists('mod_link', $modules)}
      {$sales = module('mod_link')->getLinksByProduct($model->getId())}
      {if $sales && $CI->core->core_data['data_type'] != 'page'}
        <div class="product-cut__sale">
          {foreach $sales as $sale}
            <div class="product-cut__sale-item">{echo $sale->getPageData()['title']}</div>
          {/foreach}
        </div>
      {/if}
    {/if}

    <!-- Rating and reviews BEGIN -->
    <div class="product-cut__rating">
      {view('shop/includes/product/product_rating.tpl', [
      'model' => $model
      ])}
    </div>


    <!-- Title BEGIN -->
    <div class="product-cut__title">
      <a class="product-cut__title-link"
         href="{site_url($model->getRouteUrl())}">{echo $model->getName()}</a>
    </div>

    <!-- If product is not archived -->
    {if !tpl_is_product_archived($model)}

      <!-- Product price -->
      <div class="product-cut__price">

        {view('shop/includes/product/product_price.tpl', [
        'variant' => $model->firstVariant,
        'parent_modifier' => 'product-price--bg'
        ])}

        <!-- System bonus module -->
        {if array_key_exists('system_bonus', $modules)}
          <div class="product-cut__bonus">
            {view('system_bonus/system_bonus_product.tpl', [
            'model' => $model,
            'variant' => $model->firstVariant
            ])}
          </div>
        {/if}

      </div>
    {else:}

      <!-- If archived product -->
      <div class="product-cut__archive">
        {tlang('Product has been discontinued')}
      </div>
    {/if}

    <!-- Delete item from wishlist -->
    {if $parent_wishlist}
      <div class="product-cut__delete">
        <a class="product-cut__delete-icon"
           href="{site_url('/wishlist/deleteItem/' . $parent_wishlist.variant_id .'/'. $parent_wishlist.wish_list_id)}"
           title="{tlang('Remove from list')}">
          <svg class="svg-icon">
            <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__delete"></use>
          </svg>
        </a>
      </div>
    {/if}

    <!-- Move item between wishlists -->
    {if $parent_wishlist}
      <div class="product-cut__move">
        <a class="product-cut__move-link"
           href="{site_url('/wishlist/renderPopup/'.  $parent_wishlist.variant_id .'/'. $parent_wishlist.wish_list_id  .'/'. $user.id)}"
           data-modal
        >{tlang('Change list')}</a>
      </div>
    {/if}

    <!-- If archived product -->
    {if !tpl_is_product_archived($model)}

      <!-- Block hidden at once, visible on hover -->
      <div class="product-cut__extra-info">

        <!-- Product variants -->
        {$variants = $model->getProductVariants()}
        {if count($variants) > 1}
          <div class="product-cut__variants">
            {view('shop/includes/product/variants/select.tpl', [
            'model' => $model,
            'variants' => $variants
            ])}
          </div>
        {/if}

        <div class="product-cut__actions">

          <!-- Add to cart button -->
          {if !ShopCore::app()->SSettings->useCatalogMode()}
            <div class="product-cut__action-item">
              {view('shop/includes/product/product_buy.tpl', [
              'model' => $model,
              'parent_quantity' => false
              ])}
            </div>
          {/if}

          <!-- Wishlist button -->
          {if !$parent_wishlist}
            <div class="product-cut__action-item"
                 data-ajax-inject="wishlist-btn-{echo $model->firstVariant->getId()}">
              <!-- Wishlist buttons. Dont show button on whishlist page -->
              {module('wishlist')->renderWLButton($model->firstVariant->getId(), ['type' => 'button'])}
            </div>
          {/if}

          <!-- "Compare button -->
          <div class="product-cut__action-item">
            {view('shop/includes/compare/compare_button.tpl', ['model' => $model, 'type' => 'button'])}
          </div>

        </div><!-- /.product-cut__actions -->

      </div>
      <!-- /.product-cut__extra-info -->
    {/if}

  </div><!-- /.product-cut__main-info -->
</article>