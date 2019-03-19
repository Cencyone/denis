<div class="product-intro">

  <!-- Product additional information like brand, number -->
  <div class="product-intro__addition">

    <!-- Product rating -->
    <div class="product-intro__addition-item">
      {view('shop/includes/product/product_rating.tpl', ['model' => $model])}
    </div>

    <!-- Brand -->
    {if $model->getBrand()}
      <div class="product-intro__addition-item">
        {tlang('Brand')}:
        <a class="product-intro__addition-link" href="{shop_url('brand/'.$model->getBrand()->getUrl())}">
          {echo $model->getBrand()->getName()}
        </a>
      </div>
    {/if}

    <!-- SCU Number -->
    {if $model->firstVariant->getNumber()}
      <div class="product-intro__addition-item">
        {tlang('Number')}: <span data-product-number>{echo $model->firstVariant->getNumber()}</span>
      </div>
    {/if}
  </div>


  <!-- If product is not archived -->
  {if !tpl_is_product_archived($model)}

    <!-- Sales module -->
    {if array_key_exists('mod_link', $modules)}
      {$related_posts = module('mod_link')->getLinksByProduct($model->getId())}
      {if $related_posts}
        <div class="product-intro__sales">
          {view('shop/includes/product/product_sales.tpl', [
          'posts' => $related_posts
          ])}
        </div>
      {/if}
    {/if}

    <!-- Related products module -->
    {if array_key_exists('related_products', $modules)}
      {module('related_products')->show($model->getId())}
    {/if}

    <!-- Product variants -->
    {$variants = $model->getProductVariants()}
    {if count($variants) > 1}
      <div class="product-intro__variants">
        {view('shop/includes/product/variants/select.tpl', [
        'variants' => $variants,
        'product_main' => 1
        ])}
      </div>
    {/if}
    <div class="product-intro__price">

      <!-- Product price -->
      <div class="product-intro__price-col">
        {view('shop/includes/product/product_price.tpl', [
        'variant' => $model->firstVariant,
        'parent_modifier' => 'product-price--lg'
        ])}
      </div>

      <!-- Found cheaper module -->
      {if array_key_exists('found_less_expensive', $modules)}
        <div class="product-intro__price-col">
          {module('found_less_expensive')->showButtonWithForm()}
        </div>
      {/if}

      <!-- Price Spy module -->
      {if array_key_exists('pricespy', $modules)}
        <div class="product-intro__price-col">
          {if !$CI->dx_auth->is_logged_in()}
            {view('pricespy/button.tpl', ['parent_login' => true])}
          {else:}
            {module('pricespy')->init($model)->renderButton($model->getId(), $model->firstVariant->getId())}
          {/if}
        </div>
      {/if}

    </div>
    <!-- /.product-intro__price -->

    <!-- Product purchase buttons -->
    <div class="product-intro__purchase-row">

      <!-- Product add to cart button -->
      {if !ShopCore::app()->SSettings->useCatalogMode()}
      <div class="product-intro__purchase-col">
        <div class="product-intro__purchase">
          {view('shop/includes/product/product_purchase.tpl')}
        </div>
      </div>
      {/if}

      <!-- Product one click order module button -->
      {if array_key_exists('one_click_order', $modules)}
        <div class="product-intro__purchase-col" data-one-click-scope>
          <div class="product-intro__one-click">
            {module('one_click_order')->showButton($model->firstVariant->getId())}
          </div>
        </div>
      {/if}

    </div>

    <!-- Sstem bonus module -->
    {if array_key_exists('system_bonus', $modules)}
      <div class="product-intro__bonus">
        {view('system_bonus/system_bonus_product.tpl', [
        'model' => $model,
        'variant' => $model->firstVariant,
        'modifier' => 'theme-frame'
        ])}
      </div>
    {/if}

    <!-- Product actions like wishlist and compare -->
    <div class="product-intro__actions">
      {view('shop/includes/product/product_actions.tpl', [
      'parent_modifier' => 'product-actions--inline'
      ])}
    </div>
  {else:}

    <!-- If archived product -->
    <div class="product-intro__archive">
      {tlang('Product has been discontinued')}
    </div>
  {/if}


  <!-- Product prev text description -->
  {if $model->getShortDescription()}
    <div class="product-intro__short-desc">
      <div class="typo">
        {echo $model->getShortDescription()}
      </div>
    </div>
  {/if}


  <!-- Product main properties list -->
  {$loc_main_params = ShopCore::app()->SPropertiesRenderer->renderPropertiesArray($model, true)}
  {if count($loc_main_params) > 0}
    <div class="product-intro__main-params">
      {foreach $loc_main_params as $item}
        <div class="product-intro__main-params-item">
          <div class="product-intro__main-params-key">{$item.Name}</div>
          <div class="product-intro__main-params-val">{$item.Value}</div>
        </div>
      {/foreach}
    </div>
  {/if}


  <!-- Product like and share buttons -->
  {$active_likes = array_intersect(['facebook_like', 'gg_like', 'twitter_like', 'vk_like'], array_keys(module('share')->settings))}
  {$active_shares = array_intersect(['yaru', 'vkcom', 'facebook', 'twitter', 'odnoclass', 'myworld', 'lj', 'ff', 'mc', 'gg'], array_keys(module('share')->settings))}
  {if $active_likes || $active_shares}
    <div class="product-intro__social">
      {if $active_likes}
        <div class="product-intro__social-row">
          <div class="product-intro__social-title">{tlang('Like')}</div>
          <div class="product-intro__social-inner">
            {view('includes/like_buttons.tpl')}
          </div>
        </div>
      {/if}
      {if $active_shares}
        <div class="product-intro__social-row">
          <div class="product-intro__social-title">{tlang('Share')}</div>
          <div class="product-intro__social-inner">
            {module('share')->_make_share_form()}
          </div>
        </div>
      {/if}
    </div>
  {/if}


</div><!-- /.product-intro -->