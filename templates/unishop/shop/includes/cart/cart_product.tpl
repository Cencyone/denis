<div class="cart-product">

  <div class="cart-product__photo">

    <div class="product-photo">
      <a class="product-photo__item product-photo__item--xs" href="{echo site_url($product_url)}">
        <img class="product-photo__img" src="{echo $product_image}" alt="{echo $product_title}"
             title="{echo $product_title}">
      </a>
    </div>

  </div><!-- /.__photo -->

  <div class="cart-product__info">

    <!-- Product brand -->
    {if $product_brand}
      <div class="cart-product__brand">
        {echo $product_brand->getName()}
      </div>
    {/if}

    <!-- Product title -->
    <div class="cart-product__title">
      <a class="cart-product__link" href="{echo site_url($product_url)}">{echo $product_title}</a>
      <!-- System bonus module -->
      {if array_key_exists('system_bonus', $modules) && $product_model}
        {view('system_bonus/system_bonus_product.tpl', [
        'model' => $product_model,
        'variant' => $product_variant,
        'quantity' => $product_quantity
        ])}
      {/if}
    </div>
    <!-- Product option (variant) -->
    {if $variant_title and trim($variant_title) != trim($product_title)}
      <p class="cart-product__option">{echo $variant_title}</p>
    {/if}
  </div><!-- /.__info -->

</div><!-- /.__product -->