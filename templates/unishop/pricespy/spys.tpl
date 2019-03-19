<!-- Styles init -->
{tpl_register_asset('pricespy/css/style.css', 'before')}

<div class="content">
  <div class="content__container">
    <div class="content__header">
      <h1 class="content__title">
        {tlang('Follow price')}
      </h1>
    </div>
    <div class="content__row">

      {if count($products) > 0}
        <div class="pricespy-table">
          <div class="pricespy-table__row hidden-xs">
            <div class="pricespy-table__col pricespy-table__col--header">{tlang('Product')}</div>
            <div class="pricespy-table__col pricespy-table__col--header">{tlang('New price')}</div>
            <div class="pricespy-table__col pricespy-table__col--header">{tlang('Old price')}</div>
            <div class="pricespy-table__col pricespy-table__col--header">{tlang('Price reduction')}</div>
            <div class="pricespy-table__col pricespy-table__col--header">{tlang('Unsubscribe')}</div>
          </div>
          {foreach $products as $key => $product}
            <div class="pricespy-table__row">
              <div class="pricespy-table__col">
                <div class="pricespy-table__product">
                  <div class="pricespy-table__product-image">
                    <img class="pricespy-table__product-img"
                         src="{media_url('uploads/shop/products/small/' . $product['mainImage'])}"
                         alt="{$product[name]}">
                  </div>
                  <div class="pricespy-table__product-title">
                    <a class="link link--main" href="{site_url($product['url'])}"
                       title="{$product[name]}">
                      {$product[name]}
                    </a>
                  </div>
                </div>
              </div>
              <div class="pricespy-table__col hidden-xs">
                {echo emmet_money($product[productPrice])}
              </div>
              <div class="pricespy-table__col hidden-xs">
                {echo emmet_money($product[oldProductPrice])}
              </div>
              <div class="pricespy-table__col {if $product[productPrice] < $product[oldProductPrice]}pricespy-table__col--decrease{/if}">
                {echo round(($product[productPrice]-$product[oldProductPrice])*100/$product[oldProductPrice], 2)}%
              </div>
              <div class="pricespy-table__col">
                <button type="button"
                        class="btn btn-default btn-small"
                        value="{tlang('Unsubscribe')}"
                        data-pricespy-unsubscribe="{site_url('pricespy/unspy/' . $product[hash])}"
                        data-pricespy-unsubscribe-redirect="{site_url('pricespy')}">x</button>
              </div>
            </div>
          {/foreach}
        </div>
      {else:}
        {tlang('Watch list is empty')}
      {/if}

    </div><!-- /.content__row -->
  </div><!-- /.content__container -->
</div><!-- /.content -->
