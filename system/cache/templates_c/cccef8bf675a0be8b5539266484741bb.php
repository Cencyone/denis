<form class="variants-select" action="<?php echo tpl_self_url (); ?>" method="post">

  <select class="variants-select__field" name="variant" data-product-variant="select">
    <?php if(is_true_array($variants)){ foreach ($variants as $variant){ ?>

      <?php $loc_disabled = $variant->getStock() > 0 ? "" : "disabled"?>
      <?php $loc_selected = $variant->getId() != $model->firstVariant->getId() ? "" : 'selected="selected"'?>
      <?php $loc_formatter = emmet_money($variant->getFinalPrice())?>
      <?php if(array_key_exists('system_bonus', $modules)): ?>
        <?php $system_bonus_points = module('system_bonus')->getBonusForProductFront($model, $variant);?>
        <?php $system_bonus_label = SStringHelper::Pluralize($system_bonus_points, array(tlang('system_bonus_points_pluralize_1'), tlang('system_bonus_points_pluralize_2'), tlang('system_bonus_points_pluralize_3')))?>
      <?php endif; ?>
      <option value="<?php echo $variant->getId()?>" <?php if(isset($loc_selected)){ echo $loc_selected; } ?>
              data-product-variant--id="<?php echo $variant->getId()?>"
              data-product-variant--in-cart="<?php echo getAmountInCart('SProducts', $variant->getId()) > 0 ? 1 : 0 ?>"
              data-product-variant--number="<?php echo $variant->getNumber()?>"
              data-product-variant--stock="<?php echo $variant->getStock()?>"
              data-product-variant--price="<?php echo $loc_formatter->getPrice()?>"
              data-product-variant--coins="<?php echo $loc_formatter->getCoins()?>"
              data-product-variant--photo="<?php echo $product_main ? $variant->getMainPhoto() : $variant->getMediumPhoto()?>"
              data-product-variant--thumb="<?php echo $variant->getSmallPhoto()?>"
              data-product-variant--photo-link="<?php echo $variant->getLargePhoto()?>"
              <?php if(array_key_exists('system_bonus', $modules)): ?>
                data-product-variant-bonus-points="<?php if(isset($system_bonus_points)){ echo $system_bonus_points; } ?>"
                data-product-variant-bonus-label="<?php if(isset($system_bonus_label)){ echo $system_bonus_label; } ?>"
              <?php endif; ?>
              <?php if($variant->getDiscountStatic() > 0): ?>
                data-product-variant--origin-price="<?php echo $variant->getOriginPrice()?>"
              <?php endif; ?>
              <?php if(emmet_money_additional($variant->getFinalPrice())): ?>
                data-additional-prices="<?php echo tpl_money_to_str (emmet_money_additional($variant->getFinalPrice())); ?>"
              <?php endif; ?>
      >
        <?php echo tpl_variant_or_product_name($variant)?> <?php echo $variant->getStock() > 0 ? "" : "&nbsp;&nbsp;(".tlang('Not available').")"?>
      </option>
    <?php }} ?>
  </select>

</form>
<?php $mabilis_ttl=1552752263; $mabilis_last_modified=1491903794; ///home/user2263/public_html/templates/unishop/shop/includes/product/variants/select.tpl ?>