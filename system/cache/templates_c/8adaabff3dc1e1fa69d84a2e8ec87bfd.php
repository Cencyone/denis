<?php if(count($products) > 0): ?>
  <div class="start-page__row">
    <div class="start-page__container">
      <?php view('widgets/includes/widget_primary.tpl', [
      'parent_products' => $products,
      'parent_title' => getWidgetTitle('special_products')
      ])?>
    </div>
  </div>
<?php endif; ?><?php $mabilis_ttl=1552752298; $mabilis_last_modified=1491903758; ///home/user2263/public_html/templates/unishop/widgets/special_products.tpl ?>