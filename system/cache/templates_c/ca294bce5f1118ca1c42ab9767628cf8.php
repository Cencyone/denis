<?php if(count($products) > 0): ?>
<div class="page__viewed">
  <div class="page__container">
    <?php view('widgets/includes/widget_primary.tpl', [
      'parent_products' => $products,
      'parent_title' => getWidgetTitle('products_viewed')
    ])?>
  </div>
</div>
<?php endif; ?><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903757; ///home/user2263/public_html/templates/unishop/widgets/products_viewed.tpl ?>