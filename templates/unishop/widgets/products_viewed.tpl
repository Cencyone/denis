{if count($products) > 0}
<div class="page__viewed">
  <div class="page__container">
    {view('widgets/includes/widget_primary.tpl', [
      'parent_products' => $products,
      'parent_title' => getWidgetTitle('products_viewed')
    ])}
  </div>
</div>
{/if}