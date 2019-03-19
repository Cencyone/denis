<div class="content__sidebar-item">

    <div class="filter"
         data-filter
         data-filter-category="{site_url($category->getRouteUrl())}">

        <!-- Price -->
        {if $curMax > 0}
            <div class="filter__item">
                <div class="filter__header">
                    <div class="filter__title">{tlang('Price range')}</div>
                </div>
                <div class="filter__inner">
                    <div class="filter__range-field">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="filter__range-control" type="text" form="catalog-form" name="lp"
                                       value="{echo $curMin}"
                                       data-filter-price-min="{echo $minPrice}">
                            </div>
                            <div class="col-xs-6">
                                <input class="filter__range-control" type="text" form="catalog-form" name="rp"
                                       value="{echo $curMax}"
                                       data-filter-price-max="{echo $maxPrice}">
                            </div>
                        </div>
                    </div>
                    <div class="filter__range-slider">
                        <div class="range-slider">
                            <div class="range-slider__wrapper">
                                <div class="range-slider__control" data-range-slider></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/if}


    <!-- Brands -->
    {if count($brands) > 0}
      <div class="filter__item" data-filter-name="brand" data-filter-position="0">
        <div class="filter__header">
          <div class="filter__title">{tlang('Brand')}</div>
        </div>
        <div class="filter__inner">
          {foreach $brands as $item}
            {$loc_checked = in_array($item->id, $CI->input->get('brand')) ? "checked" : ""}
            {$loc_available = $item->countProducts > 0 || $loc_checked ? "" : "disabled"}
            <div class="filter__checkgroup">
              <div class="filter__checkgroup-control">
                <input type="checkbox" name="brand[]" form="catalog-form" id="brand-{echo $item->url}"
                       value="{echo $item->id}" {$loc_checked} {$loc_available}
                       data-filter-control="brand-{echo $item->url}"
                       data-filter-alias="{echo $item->url}">
              </div>
              <label for="brand-{echo $item->url}" data-filter-label>
                {if MY_Controller::isPremiumCMS() == 1}
                  <a class="filter__checkgroup-title {$loc_available}"
                     href="{site_url($category->getRouteUrl() . '/brand-' . $item->url)}"
                     data-filter-link>{echo $item->name}</a>
                {else:}
                  <span class="filter__checkgroup-title {$loc_available}">{echo $item->name}</span>
                {/if}
              </label>
              <div class="filter__checkgroup-count">({echo $item->countProducts})</div>
            </div>
          {/foreach}
        </div>
            </div>
        {/if}


        <!-- Properties -->
        {if count($propertiesInCat) > 0}
            {foreach $propertiesInCat as $prop}

                <!-- if filter has dropDown type -->
                {$loc_is_dropdown = in_array('dropDown', getPropertyTypes($prop->property_id)) ? true : false}
                <!-- if false, properties will be visible by default -->
                {$loc_dropdown_hidden = $prop->selectedValues ? false : true}
                <!-- if filter has scroll type -->
                {$loc_is_scroll = in_array('scroll', getPropertyTypes($prop->property_id)) ? true : false}
                <div class="filter__item" data-filter-drop-scope data-filter-name="property-{echo $prop->csv_name}"
                     data-filter-position="{echo $prop->property_id}">

                    <div class="filter__header" {if $loc_is_dropdown}data-filter-drop-handle{/if}>
                        <div class="filter__title">{echo $prop->name}</div>
                        <!-- Show/hide properties buttons, visible when filter has dropDown type -->
                        {if $loc_is_dropdown}
                            <div class="filter__handle">
                                <div class="filter__handle-ico filter__handle-ico--plus {if !$loc_dropdown_hidden}hidden{/if}"
                                     data-filter-drop-ico>
                                    <svg class="svg-icon">
                                        <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__plus"></use>
                                    </svg>
                                </div>
                                <div class="filter__handle-ico filter__handle-ico--minus {if $loc_dropdown_hidden}hidden{/if}"
                                     data-filter-drop-ico>
                                    <svg class="svg-icon">
                    <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__minus"></use>
                  </svg>
                </div>
              </div>
            {/if}
          </div><!-- /.filter__header -->

          <div class="filter__inner
               {if $loc_is_dropdown and $loc_dropdown_hidden}filter__inner--js-hidden{/if}
               {if $loc_is_scroll && count($prop->possibleValues) > 10}filter__inner--scroll{/if}
               "
               data-filter-drop-inner
               {if $loc_is_scroll}data-filter-scroll{/if}
          >

            {foreach $prop->possibleValues as $item}
              {$loc_checked = in_array($item.id, $CI->input->get('pv')[$prop->property_id]) ? "checked" : ""}
              {$loc_available = $item.count > 0 || $loc_checked ? "" : "disabled"}
              <div class="filter__checkgroup">
                <div class="filter__checkgroup-control">
                  <input type="checkbox" name="pv[{echo $prop->property_id}][]" form="catalog-form"
                         id="property-{echo $prop->csv_name}-{echo $item.id}"
                         value="{echo $item.id}" {$loc_checked} {$loc_available}
                         data-filter-control="property-{echo $prop->csv_name}-{echo $item.id}"
                         data-filter-alias="{echo $item.id}">
                </div>
                <label for="property-{echo $prop->csv_name}-{echo $item.id}" data-filter-label>
                {if MY_Controller::isPremiumCMS() == 1}
                  <a class="filter__checkgroup-title {$loc_available}"
					 href="{site_url($category->getRouteUrl() . '/property-' . $prop->csv_name . '-' . $item.id)}"
					 data-filter-link>{echo $item.value}</a>
                {else:}
                  <span class="filter__checkgroup-title {$loc_available}">{echo $item.value}</span>
                {/if}
                </label>
                <div class="filter__checkgroup-count">
                  ({echo $item.count})
                </div>
              </div>
            {/foreach}
          </div><!-- /.filter__inner -->

        </div>
        <!-- /.filter__item -->

      {/foreach}
    {/if}
  </div>

</div>