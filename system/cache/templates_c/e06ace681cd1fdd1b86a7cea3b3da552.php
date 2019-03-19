<?php if($totalProducts > 0): ?>

  <?php if($_COOKIE['catalog_view'] == 'snippet'): ?>

    <!-- List, snippet view -->
    <?php if(is_true_array($products)){ foreach ($products as $product){ ?>
      <div class="content__row content__row--top-md">
        <?php view('shop/includes/product/product_snippet.tpl', [
        'model' => $product
        ])?>
      </div>
    <?php }} ?>

  <?php else:?>

    <!-- Table, card view. Default view -->
    <div class="content__row content__row--top-md">
      <div class="row row--ib row--vindent-m">
        <?php if(is_true_array($products)){ foreach ($products as $product){ ?>
          <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
            <?php view('shop/includes/product/product_cut.tpl', [
            'model' => $product
            ])?>
          </div>
        <?php }} ?>
      </div>
    </div>
  <?php endif; ?>

<?php else:?>
  <p class="typo"><?php echo tlang ('There are no items to display. Please come back later!'); ?></p>
<?php endif; ?>

<!-- Category pagination -->
<?php if($pagination): ?>
  <div class="content__pagination">
    <?php if(isset($pagination)){ echo $pagination; } ?>
  </div>
<?php endif; ?><?php $mabilis_ttl=1552752263; $mabilis_last_modified=1491903783; ///home/user2263/public_html/templates/unishop/shop/includes/category/category_products.tpl ?>