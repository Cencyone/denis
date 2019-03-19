<!-- Styles and scripts init -->
{tpl_register_asset("related_products/css/related_products.css", "before")}

<div class="product-intro__colors">
  <div class="related-products">
    <div class="related-products__label">
      {tlang('Other colors')}:
    </div>
    <div class="related-products__list">
      {foreach $related_products as $item}
        {$loc_color = $item->customFields["color"]["field_data"];}
        <a class="related-products__item" href="{shop_url('product/'.$item->getUrl())}" title="{echo $item->getName()}">
          {if $loc_color}
            {$loc_colors = explode('|', $loc_color)}
            <div class="related-products__ico">
              {foreach $loc_colors as $color}
                {$width = (100 / count($loc_colors)) . "%"}
                <i class="related-products__color"
                   style="background-color: {$color}; width: {str_replace(',','.',$width)};">{echo $item->getName()}</i>
              {/foreach}
            </div>
          {else:}
            <img class="related-products__ico" src="{echo $item->firstVariant->getSmallPhoto()}"
                 alt="{echo $item->getName()}">
          {/if}
        </a>
      {/foreach}
    </div>
  </div>
</div>