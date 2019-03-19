<div class="content__row content__row--sm">
  <div class="catalog-toolbar">
    <div class="row">

      <!-- Order BEGIN -->
      <div class="col-xs-12 col-sm-5">
        <?php $loc_sorting_list = ShopCore::app()->SSettings->getSortingFront()?>
        <?php if($loc_sorting_list): ?>
          <div class="catalog-toolbar__item">
            <label class="catalog-toolbar__label hidden-xs hidden-sm" for="catalog-sort-by"><?php echo tlang ('Sort by'); ?></label>
            <div class="catalog-toolbar__field">
              <?php $loc_current_sort = tpl_get_default_sorting($parent_default_order)?>
              <?php $loc_default_sort = tpl_get_default_sorting($parent_default_order, false)?>
              <select class="form-control input-sm"
                      id="catalog-sort-by"
                      form="catalog-form"
                      name="order"
                      data-catalog-order-select>
                <?php if(is_true_array($loc_sorting_list)){ foreach ($loc_sorting_list as $key => $order){ ?>
                  <option value="<?php echo $order['get']; ?>"
                          <?php if($loc_current_sort ==  $order['get']): ?>selected<?php endif; ?>
                          <?php if($loc_default_sort ==  $order['get']): ?>data-catalog-default<?php endif; ?>
                  ><?php echo $order['name_front']; ?></option>
                <?php }} ?>
              </select>
            </div>
          </div>
        <?php endif; ?>
      </div>
      <!-- END Order -->

      <!-- Per page items BEGIN -->
      <div class="hidden-xs col-sm-3 col-md-4">
        <?php $loc_per_page_items = tpl_per_page_array()?>
        <?php if(count($loc_per_page_items) > 1): ?>
          <div class="catalog-toolbar__item">
            <label class="catalog-toolbar__label hidden-xs hidden-sm" for="catalog-per-page"><?php echo tlang ('Per page'); ?></label>
            <div class="catalog-toolbar__field">
              <select class="form-control input-sm"
                      id="catalog-per-page"
                      form="catalog-form"
                      name="user_per_page"
                      data-catalog-perpage-select>
                <?php if(is_true_array($loc_per_page_items)){ foreach ($loc_per_page_items as $per_page_item){ ?>
                  <option value="<?php if(isset($per_page_item)){ echo $per_page_item; } ?>"
                          <?php if(tpl_per_page_selected($per_page_item)): ?>selected<?php endif; ?>
                          <?php if(tpl_per_page_selected($per_page_item, false)): ?>data-catalog-default<?php endif; ?>>
                    <?php if(isset($per_page_item)){ echo $per_page_item; } ?>
                  </option>
                <?php }} ?>
              </select>
            </div>
          </div>
        <?php endif; ?>
      </div>
      <!-- END Per page items -->


      <!-- Change View BEGIN -->
      <div class="hidden-xs col-sm-4 col-md-3">
        <div class="pull-right">
          <div class="catalog-toolbar__item">
            <div class="catalog-toolbar__label hidden-xs hidden-sm"><?php echo tlang ('View'); ?></div>
            <div class="catalog-toolbar__field">

              <div class="change-view">
                <button class="change-view__button"
                        data-catalog-view-item="card"
                        <?php if(!$_COOKIE['catalog_view'] || $_COOKIE['catalog_view'] == 'card'): ?>disabled<?php endif; ?>>
                  <i class="change-view__icon"
                     title=<?php echo tlang ("Card"); ?>><svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__table"></use></svg></i>
                </button>
                <button class="change-view__button"
                        data-catalog-view-item="snippet"
                        <?php if($_COOKIE['catalog_view'] == 'snippet'): ?>disabled<?php endif; ?>>
                  <i class="change-view__icon"
                     title=<?php echo tlang ("List"); ?>><svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__list"></use></svg></i>
                </button>
              </div>

            </div>
          </div>
        </div>
      </div>
      <!-- END Change View -->

    </div>
  </div>
</div><?php $mabilis_ttl=1552752262; $mabilis_last_modified=1491903783; ///home/user2263/public_html/templates/unishop/shop/includes/category/category_toolbar.tpl ?>