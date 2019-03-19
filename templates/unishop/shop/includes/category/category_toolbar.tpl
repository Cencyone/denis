<div class="content__row content__row--sm">
  <div class="catalog-toolbar">
    <div class="row">

      <!-- Order BEGIN -->
      <div class="col-xs-12 col-sm-5">
        {$loc_sorting_list = ShopCore::app()->SSettings->getSortingFront()}
        {if $loc_sorting_list}
          <div class="catalog-toolbar__item">
            <label class="catalog-toolbar__label hidden-xs hidden-sm" for="catalog-sort-by">{tlang('Sort by')}</label>
            <div class="catalog-toolbar__field">
              {$loc_current_sort = tpl_get_default_sorting($parent_default_order)}
              {$loc_default_sort = tpl_get_default_sorting($parent_default_order, false)}
              <select class="form-control input-sm"
                      id="catalog-sort-by"
                      form="catalog-form"
                      name="order"
                      data-catalog-order-select>
                {foreach $loc_sorting_list as $key => $order}
                  <option value="{$order.get}"
                          {if $loc_current_sort == $order.get}selected{/if}
                          {if $loc_default_sort == $order.get}data-catalog-default{/if}
                  >{$order.name_front}</option>
                {/foreach}
              </select>
            </div>
          </div>
        {/if}
      </div>
      <!-- END Order -->

      <!-- Per page items BEGIN -->
      <div class="hidden-xs col-sm-3 col-md-4">
        {$loc_per_page_items = tpl_per_page_array()}
        {if count($loc_per_page_items) > 1}
          <div class="catalog-toolbar__item">
            <label class="catalog-toolbar__label hidden-xs hidden-sm" for="catalog-per-page">{tlang('Per page')}</label>
            <div class="catalog-toolbar__field">
              <select class="form-control input-sm"
                      id="catalog-per-page"
                      form="catalog-form"
                      name="user_per_page"
                      data-catalog-perpage-select>
                {foreach $loc_per_page_items as $per_page_item}
                  <option value="{$per_page_item}"
                          {if tpl_per_page_selected($per_page_item)}selected{/if}
                          {if tpl_per_page_selected($per_page_item, false)}data-catalog-default{/if}>
                    {$per_page_item}
                  </option>
                {/foreach}
              </select>
            </div>
          </div>
        {/if}
      </div>
      <!-- END Per page items -->


      <!-- Change View BEGIN -->
      <div class="hidden-xs col-sm-4 col-md-3">
        <div class="pull-right">
          <div class="catalog-toolbar__item">
            <div class="catalog-toolbar__label hidden-xs hidden-sm">{tlang('View')}</div>
            <div class="catalog-toolbar__field">

              <div class="change-view">
                <button class="change-view__button"
                        data-catalog-view-item="card"
                        {if !$_COOKIE['catalog_view'] || $_COOKIE['catalog_view'] == 'card'}disabled{/if}>
                  <i class="change-view__icon"
                     title={tlang("Card")}><svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__table"></use></svg></i>
                </button>
                <button class="change-view__button"
                        data-catalog-view-item="snippet"
                        {if $_COOKIE['catalog_view'] == 'snippet'}disabled{/if}>
                  <i class="change-view__icon"
                     title={tlang("List")}><svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__list"></use></svg></i>
                </button>
              </div>

            </div>
          </div>
        </div>
      </div>
      <!-- END Change View -->

    </div>
  </div>
</div>