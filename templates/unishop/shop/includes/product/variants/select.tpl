<form class="variants-select" action="{tpl_self_url()}" method="post">

  <select class="variants-select__field" name="variant" data-product-variant="select">
    {foreach $variants as $variant}

      {$loc_disabled = $variant->getStock() > 0 ? "" : "disabled"}
      {$loc_selected = $variant->getId() != $model->firstVariant->getId() ? "" : 'selected="selected"'}
      {$loc_formatter = emmet_money($variant->getFinalPrice())}
      {if array_key_exists('system_bonus', $modules)}
        {$system_bonus_points = module('system_bonus')->getBonusForProductFront($model, $variant);}
        {$system_bonus_label = SStringHelper::Pluralize($system_bonus_points, array(tlang('system_bonus_points_pluralize_1'), tlang('system_bonus_points_pluralize_2'), tlang('system_bonus_points_pluralize_3')))}
      {/if}
      <option value="{echo $variant->getId()}" {$loc_selected}
              data-product-variant--id="{echo $variant->getId()}"
              data-product-variant--in-cart="{echo getAmountInCart('SProducts', $variant->getId()) > 0 ? 1 : 0 }"
              data-product-variant--number="{echo $variant->getNumber()}"
              data-product-variant--stock="{echo $variant->getStock()}"
              data-product-variant--price="{echo $loc_formatter->getPrice()}"
              data-product-variant--coins="{echo $loc_formatter->getCoins()}"
              data-product-variant--photo="{echo $product_main ? $variant->getMainPhoto() : $variant->getMediumPhoto()}"
              data-product-variant--thumb="{echo $variant->getSmallPhoto()}"
              data-product-variant--photo-link="{echo $variant->getLargePhoto()}"
              {if array_key_exists('system_bonus', $modules)}
                data-product-variant-bonus-points="{$system_bonus_points}"
                data-product-variant-bonus-label="{$system_bonus_label}"
              {/if}
              {if $variant->getDiscountStatic() > 0}
                data-product-variant--origin-price="{echo $variant->getOriginPrice()}"
              {/if}
              {if emmet_money_additional($variant->getFinalPrice())}
                data-additional-prices="{tpl_money_to_str(emmet_money_additional($variant->getFinalPrice()))}"
              {/if}
      >
        {echo tpl_variant_or_product_name($variant)} {echo $variant->getStock() > 0 ? "" : "&nbsp;&nbsp;(".tlang('Not available').")"}
      </option>
    {/foreach}
  </select>

</form>
