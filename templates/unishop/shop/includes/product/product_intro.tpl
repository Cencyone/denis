<div class="product-intro">

  <!-- Product additional information like brand, number -->
  <div class="product-intro__addition">

    <!-- Product rating -->
    <div class="product-intro__addition-item">
      {view('shop/includes/product/product_rating.tpl', [
      'model' => $model
      ])}
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

  <!-- Related post -->
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

  <!-- If product is not archived -->
  {if !tpl_is_product_archived($model)}

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
    <div class="product-intro__purchase">

      <!-- Product price -->
      <div class="product-intro__price">
        {view('shop/includes/product/product_price.tpl', [
        'variant' => $model->firstVariant,
        'parent_modifier' => 'product-price--lg'
        ])}
      </div>

      <!-- Product add to cart buttons -->
      {if !ShopCore::app()->SSettings->useCatalogMode()}
      <div class="product-intro__buy">
        {view('shop/includes/product/product_buy.tpl', [
        'parent_modifier' => 'product-buy--product',
        'parent_quantity' => true
        ])}
      </div>
      {/if}


    </div>
    <!-- /.product-intro__purchase -->

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
      <div class="product-actions">

        {$in_stock = $model->firstVariant->getStock()}

        <div class="product-actions__item {echo $in_stock > 0 ? '' : 'hidden' }" data-product-available>
          <div class="product-actions__ico product-actions__ico--available">
            <svg class="svg-icon">
              <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__available"></use>
            </svg>
          </div>
          <div class="product-actions__text product-actions__text--available">
            {tlang('Available')}
          </div>
        </div>

        <div class="product-actions__item {echo $in_stock > 0 ? 'hidden' : '' }" data-product-unavailable>
          <div class="product-actions__ico product-actions__ico--unavailable">
            <svg class="svg-icon">
              <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__close-bold"></use>
            </svg>
          </div>
          <div class="product-actions__text product-actions__text--unavailable">
            {tlang('Unavailable')}
          </div>
        </div>

        <!-- Buy in one click button. Visible when module is installed -->
        {if array_key_exists('one_click_order', $modules)}
          {module('one_click_order')->showButton($model->firstVariant->getId())}
        {/if}

        <!-- Found less expensive module -->
        {if array_key_exists('found_less_expensive', $modules)}
          {module('found_less_expensive')->showButtonWithForm()}
        {/if}

        <!-- Price Spy module -->
        {if array_key_exists('pricespy', $modules)}
          {if !$CI->dx_auth->is_logged_in()}
            {view('pricespy/button.tpl', ['parent_login' => true])}
          {else:}
            {module('pricespy')->init($model)->renderButton($model->getId(), $model->firstVariant->getId())}
          {/if}
        {/if}

        <!-- Wishlist buttons. Dont show button on whishlist page -->
        <div class="product-actions__item" data-ajax-inject="wishlist-link-{echo $model->firstVariant->getId()}">
          {module('wishlist')->renderWLButton($model->firstVariant->getId(), ['type' => 'link']);}
        </div>
        <!-- "Add to" or "Open in" compare buttons -->
        <div class="product-actions__item">
          {view('shop/includes/compare/compare_button.tpl', [
          'model' => $model,
          'type' => 'link'
          ])}
        </div>
      </div>
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
      <div class="main-params">
        {foreach $loc_main_params as $item}
          <div class="main-params__item">
            <span class="main-params__key">{$item.Name}</span>
            <span class="main-params__val">{$item.Value}</span>
          </div>
        {/foreach}
      </div>
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