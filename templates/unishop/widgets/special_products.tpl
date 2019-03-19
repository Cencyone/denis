{if count($products) > 0}
  <div class="start-page__row">
    <div class="start-page__container">
      {view('widgets/includes/widget_primary.tpl', [
      'parent_products' => $products,
      'parent_title' => getWidgetTitle('special_products')
      ])}
    </div>
  </div>
{/if}