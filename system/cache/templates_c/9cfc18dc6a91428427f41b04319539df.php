<?php if($totalProducts > 0 || tpl_is_filtered($minPrice,  $maxPrice) || count($category->getTree()) == 0): ?>
  <div class="content">
    <div class="content__container">
      <div class="row">

        <!-- Left BEGIN -->
        <div class="col-sm-4 col-md-3 col-lg-2">

          <!-- Sub categories -->
          <div class="hidden-xs">
            <?php echo view ('shop/includes/category/category_subnav.tpl'); ?>
          </div>

          <!-- Filter toggle button on mobile devices -->
          <div class="content__sidebar-item visible-xs">
            <button class="btn btn-default btn-block" data-filter-toggle--btn>
            <span data-filter-toggle--btn-text><?php echo tlang ('Show filter'); ?>
              <i class="btn-default__ico btn-default__ico--down">
                <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__arrow-down"></use></svg>
              </i>
            </span>
              <span class="hidden" data-filter-toggle--btn-text><?php echo tlang ('Hide filter'); ?>
                <i class="btn-default__ico btn-default__ico--top">
                <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__arrow-top"></use></svg>
              </i>
            </span>
            </button>
          </div>

          <!-- Filter -->
          <div class="hidden-xs" data-filter-toggle--filter>
            <?php module('smart_filter')->init();?>
          </div>
        </div>
        <!-- END Left -->

        <!-- Center BEGIN -->
        <div class="col-sm-8 col-md-9 col-lg-10">

          <!-- Category title -->
          <div class="content__header">
            <h1 class="content__title">
              <?php if(isset($title)){ echo $title; } ?>
            </h1>
            <span class="content__hinfo">
            <?php echo tlang ('Result'); ?>:
              <?php if($totalProducts > 0): ?>
                <i class="content__hinfo-number"><?php echo tpl_encode($CI->input->get('per_page')) ? tpl_encode($CI->input->get('per_page')) : 1?></i>
                <span> - </span>
                <i class="content__hinfo-number"><?php echo tpl_encode($CI->input->get('per_page')) + count($products)?></i>
                <?php echo tlang ('of'); ?>
              <?php endif; ?>
              <i class="content__hinfo-number"><?php if(isset($totalProducts)){ echo $totalProducts; } ?></i>
              <?php echo SStringHelper::Pluralize($totalProducts, array(tlang('pluralize item 1'), tlang('pluralize item 2'), tlang('pluralize item 3')))?>
          </span>
          </div>

          <!-- Horisontal banner -->
          <?php if($loc_banner = getBanner('catalog_horisontal_banner', 'object')): ?>
            <div class="content__row content__row--sm">
              <?php view('xbanners/banners/banner_simple.tpl', [
              'parent_banner' => $loc_banner
              ])?>
            </div>
          <?php endif; ?>

          <!-- Products order and view change -->
          <?php view('shop/includes/category/category_toolbar.tpl', [
          'parent_default_order' => $category->getOrderMethod()
          ])?>

          <!-- Filter selected results -->
          <?php echo view ('smart_filter/includes/filter_results.tpl'); ?>

          <!-- Product list -->
          <div class="content__row">
            <?php echo view ('shop/includes/category/category_products.tpl'); ?>
          </div>


          <!-- Category description -->
          <?php if(trim($category->getDescription()) != "" and $page_number < 2): ?>
            <div class="content__row">
              <div class="typo"><?php echo trim($category->getDescription())?></div>
            </div>
          <?php endif; ?>

        </div><!-- /.col -->
        <!-- END Center -->

      </div>
    </div><!-- /.content__container -->
    <?php echo view ('shop/includes/category/category_form.tpl'); ?>
  </div>
  <!-- /.content -->
<?php else:?>
  <?php echo view ('shop/section.tpl'); ?>
<?php endif; ?><?php $mabilis_ttl=1552752262; $mabilis_last_modified=1491903756; ///home/user2263/public_html/templates/unishop/shop/category.tpl ?>