<article class="product-cut"
         data-product-scope>

  <!-- Photo output BEGIN -->
  <div class="product-cut__photo">
    {view('shop/includes/product/product_cut_photo.tpl', [
    'model' => $model
    ])}
  </div>
  <!-- END photo output -->


  <!-- Rating and reviews BEGIN -->
  <div class="product-cut__rating">
    {view('shop/includes/product/product_rating.tpl', ['model' => $model])}
  </div>
  <!-- END Rating and reviews -->


  <!-- Title BEGIN -->
  <div class="product-cut__title">
    <a class="product-cut__title-link" href="{site_url($model->getRouteUrl())}">
      {echo $model->getName()}
    </a>
  </div>
  <!-- END Title -->

  <!-- Sales module -->
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

  <!-- Additional info BEGIN -->
  <div class="product-cut__addition">
    <!-- Brand -->
    {if $model->getBrand()}
      <div class="product-cut__addition-item">
        {tlang('Brand')}:
        <a class="product-cut__addition-link" href="{shop_url('brand/'.$model->getBrand()->getUrl())}">
          {echo $model->getBrand()->getName()}
        </a>
      </div>
    {/if}
    <!-- SCU Number -->
    {if $model->firstVariant->getNumber()}
      <div class="product-cut__addition-item">
        {tlang('Number')}: <span data-product-number>{echo $model->firstVariant->getNumber()}</span>
      </div>
    {/if}
  </div><!-- /.product-cut__addition -->
  <!-- END Additional info -->


  <!-- If product is not archived -->
  {if !tpl_is_product_archived($model)}

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


    <!-- Product price -->
    <div class="product-cut__price">
      {view('shop/includes/product/product_price.tpl', [
      'variant' => $model->firstVariant
      ])}
    </div>

    <!-- System bonus module -->
    {if array_key_exists('system_bonus', $modules)}
      <div class="product-cut__bonus">
        {view('system_bonus/system_bonus_product.tpl', [
        'model' => $model,
        'variant' => $model->firstVariant
        ])}
      </div>
    {/if}

    <!-- Product "add to cart" and "already in cart" buttons -->
    {if !ShopCore::app()->SSettings->useCatalogMode()}
      <div class="product-cut__purchase">
        {view('shop/includes/product/product_cut_button.tpl', [
        'model' => $model
        ])}
      </div>
    {/if}
    <!-- Wishlist and Compare BEGIN -->
    <div class="product-cut__actions hidden-xs">
      {view('shop/includes/product/product_actions.tpl', [
      'model' => $model,
      'parent_wishlist_item' => $wishlist_item
      ])}
    </div>
    <!-- END Wishlist and Compare -->
  {else:}
    <!-- If archived product -->
    <div class="product-cut__archive">
      {tlang('Product has been discontinued')}
    </div>
  {/if}

  <!-- Short description BEGIN -->
  {if $model->getShortDescription()}
    <p class="product-cut__desc hidden-xs">
      {echo strip_tags($model->getShortDescription())}
    </p>
  {/if}
  <!-- END Short description -->


  <!-- Main properties BEGIN -->
  {$loc_main_params = ShopCore::app()->SPropertiesRenderer->renderPropertiesArray($model, true)}
  {if count($loc_main_params) > 0}
    <div class="product-cut__params hidden-xs">
      {foreach $loc_main_params as $item}
        <div class="product-cut__params-item">
          <div class="product-cut__params-key">{$item.Name}</div>
          <div class="product-cut__params-val">{$item.Value}</div>
        </div>
      {/foreach}
    </div>
  {/if}
  <!-- END Main properties -->

</article>