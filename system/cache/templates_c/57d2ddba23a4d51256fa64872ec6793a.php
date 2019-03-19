<div class="content__sidebar-item">

    <div class="filter"
         data-filter
         data-filter-category="<?php echo site_url ($category->getRouteUrl()); ?>">

        <!-- Price -->
        <?php if($curMax > 0): ?>
            <div class="filter__item">
                <div class="filter__header">
                    <div class="filter__title"><?php echo tlang ('Price range'); ?></div>
                </div>
                <div class="filter__inner">
                    <div class="filter__range-field">
                        <div class="row">
                            <div class="col-xs-6">
                                <input class="filter__range-control" type="text" form="catalog-form" name="lp"
                                       value="<?php echo $curMin?>"
                                       data-filter-price-min="<?php echo $minPrice?>">
                            </div>
                            <div class="col-xs-6">
                                <input class="filter__range-control" type="text" form="catalog-form" name="rp"
                                       value="<?php echo $curMax?>"
                                       data-filter-price-max="<?php echo $maxPrice?>">
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
        <?php endif; ?>


    <!-- Brands -->
    <?php if(count($brands) > 0): ?>
      <div class="filter__item" data-filter-name="brand" data-filter-position="0">
        <div class="filter__header">
          <div class="filter__title"><?php echo tlang ('Brand'); ?></div>
        </div>
        <div class="filter__inner">
          <?php if(is_true_array($brands)){ foreach ($brands as $item){ ?>
            <?php $loc_checked = in_array($item->id, $CI->input->get('brand')) ? "checked" : ""?>
            <?php $loc_available = $item->countProducts > 0 || $loc_checked ? "" : "disabled"?>
            <div class="filter__checkgroup">
              <div class="filter__checkgroup-control">
                <input type="checkbox" name="brand[]" form="catalog-form" id="brand-<?php echo $item->url?>"
                       value="<?php echo $item->id?>" <?php if(isset($loc_checked)){ echo $loc_checked; } ?> <?php if(isset($loc_available)){ echo $loc_available; } ?>
                       data-filter-control="brand-<?php echo $item->url?>"
                       data-filter-alias="<?php echo $item->url?>">
              </div>
              <label for="brand-<?php echo $item->url?>" data-filter-label>
                <?php if(MY_Controller::isPremiumCMS() == 1): ?>
                  <a class="filter__checkgroup-title <?php if(isset($loc_available)){ echo $loc_available; } ?>"
                     href="<?php echo site_url ($category->getRouteUrl() . '/brand-' . $item->url); ?>"
                     data-filter-link><?php echo $item->name?></a>
                <?php else:?>
                  <span class="filter__checkgroup-title <?php if(isset($loc_available)){ echo $loc_available; } ?>"><?php echo $item->name?></span>
                <?php endif; ?>
              </label>
              <div class="filter__checkgroup-count">(<?php echo $item->countProducts?>)</div>
            </div>
          <?php }} ?>
        </div>
            </div>
        <?php endif; ?>


        <!-- Properties -->
        <?php if(count($propertiesInCat) > 0): ?>
            <?php if(is_true_array($propertiesInCat)){ foreach ($propertiesInCat as $prop){ ?>

                <!-- if filter has dropDown type -->
                <?php $loc_is_dropdown = in_array('dropDown', getPropertyTypes($prop->property_id)) ? true : false?>
                <!-- if false, properties will be visible by default -->
                <?php $loc_dropdown_hidden = $prop->selectedValues ? false : true?>
                <!-- if filter has scroll type -->
                <?php $loc_is_scroll = in_array('scroll', getPropertyTypes($prop->property_id)) ? true : false?>
                <div class="filter__item" data-filter-drop-scope data-filter-name="property-<?php echo $prop->csv_name?>"
                     data-filter-position="<?php echo $prop->property_id?>">

                    <div class="filter__header" <?php if($loc_is_dropdown): ?>data-filter-drop-handle<?php endif; ?>>
                        <div class="filter__title"><?php echo $prop->name?></div>
                        <!-- Show/hide properties buttons, visible when filter has dropDown type -->
                        <?php if($loc_is_dropdown): ?>
                            <div class="filter__handle">
                                <div class="filter__handle-ico filter__handle-ico--plus <?php if(!$loc_dropdown_hidden): ?>hidden<?php endif; ?>"
                                     data-filter-drop-ico>
                                    <svg class="svg-icon">
                                        <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__plus"></use>
                                    </svg>
                                </div>
                                <div class="filter__handle-ico filter__handle-ico--minus <?php if($loc_dropdown_hidden): ?>hidden<?php endif; ?>"
                                     data-filter-drop-ico>
                                    <svg class="svg-icon">
                    <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__minus"></use>
                  </svg>
                </div>
              </div>
            <?php endif; ?>
          </div><!-- /.filter__header -->

          <div class="filter__inner
               <?php if($loc_is_dropdown and $loc_dropdown_hidden): ?>filter__inner--js-hidden<?php endif; ?>
               <?php if($loc_is_scroll && count($prop->possibleValues) > 10): ?>filter__inner--scroll<?php endif; ?>
               "
               data-filter-drop-inner
               <?php if($loc_is_scroll): ?>data-filter-scroll<?php endif; ?>
          >

            <?php if(is_true_array($prop->possibleValues)){ foreach ($prop->possibleValues as $item){ ?>
              <?php $loc_checked = in_array( $item['id'] , $CI->input->get('pv')[$prop->property_id]) ? "checked" : "" ?>
              <?php $loc_available =  $item['count']  > 0 || $loc_checked ? "" : "disabled" ?>
              <div class="filter__checkgroup">
                <div class="filter__checkgroup-control">
                  <input type="checkbox" name="pv[<?php echo $prop->property_id?>][]" form="catalog-form"
                         id="property-<?php echo $prop->csv_name?>-<?php echo  $item['id']  ?>"
                         value="<?php echo  $item['id']  ?>" <?php if(isset($loc_checked)){ echo $loc_checked; } ?> <?php if(isset($loc_available)){ echo $loc_available; } ?>
                         data-filter-control="property-<?php echo $prop->csv_name?>-<?php echo  $item['id']  ?>"
                         data-filter-alias="<?php echo  $item['id']  ?>">
                </div>
                <label for="property-<?php echo $prop->csv_name?>-<?php echo  $item['id']  ?>" data-filter-label>
                <?php if(MY_Controller::isPremiumCMS() == 1): ?>
                  <a class="filter__checkgroup-title <?php if(isset($loc_available)){ echo $loc_available; } ?>"
					 href="<?php echo site_url ($category->getRouteUrl() . '/property-' . $prop->csv_name . '-' .  $item['id'] ); ?>"
					 data-filter-link><?php echo  $item['value']  ?></a>
                <?php else:?>
                  <span class="filter__checkgroup-title <?php if(isset($loc_available)){ echo $loc_available; } ?>"><?php echo  $item['value']  ?></span>
                <?php endif; ?>
                </label>
                <div class="filter__checkgroup-count">
                  (<?php echo  $item['count']  ?>)
                </div>
              </div>
            <?php }} ?>
          </div><!-- /.filter__inner -->

        </div>
        <!-- /.filter__item -->

      <?php }} ?>
    <?php endif; ?>
  </div>

</div><?php $mabilis_ttl=1552752262; $mabilis_last_modified=1491903757; //templates/unishop/smart_filter/main.tpl ?>