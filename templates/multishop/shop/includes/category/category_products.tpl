{if $totalProducts > 0}

  {if $_COOKIE['catalog_view'] == 'snippet'}

    <!-- List, snippet view -->
    {foreach $products as $product}
      <div class="content__row">
        {view('shop/includes/product/product_snippet.tpl', [
        'model' => $product
        ])}
      </div>
    {/foreach}

  {else:}

    <!-- Table, card view. Default view -->
    <div class="row row--ib row--vindent-m">
      {foreach $products as $product}
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
          {view('shop/includes/product/product_cut.tpl', [
          'model' => $product
          ])}
        </div>
      {/foreach}
    </div>

  {/if}

{else:}
  <p class="typo">{tlang('There are no items to display. Please come back later!')}</p>
{/if}

<!-- Category pagination -->
{if $pagination}
  <div class="content__pagination">
    {$pagination}
  </div>
{/if}