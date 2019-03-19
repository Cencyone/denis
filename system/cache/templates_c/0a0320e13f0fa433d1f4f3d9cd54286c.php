<?php if(count($brands) > 0): ?>
  <section class="widget-secondary">
    <div class="widget-secondary__header">
      <div class="widget-secondary__title"><?php echo getWidgetTitle ('brands'); ?></div>
      <div class="widget-secondary__viewall">
        <a class="widget-secondary__hlink" href="<?php echo shop_url ('brand'); ?>"><?php echo tlang ('View all'); ?></a>
      </div>
    </div>
    <div class="widget-secondary__inner">

      <div class="brands-widget">
        <div class="row row--ib row--vindent-s">
          <?php if(is_true_array($brands)){ foreach ($brands as $brand){ ?>
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
              <div class="brands-widget__item">
                <a class="brands-widget__link" href="<?php echo shop_url ( $brand['full_url'] ); ?>">
                  <?php if($brand['img_fullpath']): ?>
                    <img class="brands-widget__img" src="<?php echo media_url ( $brand['img_fullpath'] ); ?>" title="<?php echo $brand['name']; ?>"
                         alt="<?php echo $brand['name']; ?>">
                  <?php else:?>
                    <span class="brands-widget__img"><?php echo $brand['name']; ?></span>
                  <?php endif; ?>
                </a>
              </div>
            </div>
          <?php }} ?>
        </div>
      </div>

    </div>
  </section>
<?php endif; ?><?php $mabilis_ttl=1552752299; $mabilis_last_modified=1491903757; ///home/user2263/public_html/templates/unishop/widgets/brands.tpl ?>