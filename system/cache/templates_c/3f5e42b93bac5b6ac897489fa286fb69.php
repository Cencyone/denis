<div class="product-price <?php if(isset($parent_modifier)){ echo $parent_modifier; } ?>">

  <!-- Discount -->
  <?php if($variant->getDiscountStatic() > 0): ?>
    <div class="product-price__item">
      <div class="product-price__old">
        <?php echo emmet_money($variant->getOriginPrice(), 'span.product-price__item-value[data-product-price--origin]', '', 'span.product-price__item-cur')?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Main Price -->
  <div class="product-price__item">
    <div class="product-price__main">
      <?php echo emmet_money($variant->getFinalPrice(), 'span.product-price__item-value[data-product-price--main]', 'span.product-price__item-coins[data-product-price--coins]', 'span.product-price__item-cur')?>
    </div>
  </div>

  <?php $loc_additional_prices = emmet_money_additional($variant->getFinalPrice(), 'span.product-price__addition-value[data-product-price--addition-value]', 'span.product-price__addition-coins[data-product-price--addition-coins]', 'span.product-price__addition-cur')?>
  <?php if(count($loc_additional_prices) > 0): ?>
    <div class="product-price__item">
      <div class="product-price__addition">
        <?php if(is_true_array($loc_additional_prices)){ foreach ($loc_additional_prices as $additional_price){ ?>
          <div class="product-price__addition-item" data-product-price--addition-list>
            <?php if(isset($additional_price)){ echo $additional_price; } ?>
          </div>
        <?php }} ?>
      </div>
    </div>
  <?php endif; ?>

</div><?php $mabilis_ttl=1552752263; $mabilis_last_modified=1491903784; ///home/user2263/public_html/templates/unishop/shop/includes/product/product_price.tpl ?>