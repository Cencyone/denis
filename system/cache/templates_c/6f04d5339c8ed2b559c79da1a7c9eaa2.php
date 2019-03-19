<?php $loc_params = $parent_banner->getEffects()?>
<div class="banner-simple"
     data-slider="banner-simple"
     data-autoplay="<?php echo $loc_params['autoplay'] == 1 ? 'true' : 'false'?>"
     data-autoplayspeed="<?php echo $loc_params['autoplaySpeed']*1000?>"
     data-arrows="<?php echo $loc_params['arrows'] == 1 ? 'true' : 'false'?>"
     data-dots="<?php echo $loc_params['dots'] == 1 ? 'true' : 'false'?>"
     data-fade="<?php echo $loc_params['fade'] == 1 ? 'true' : 'false'?>"
     data-infinite="<?php echo $loc_params['infinite'] == 1 ? 'true' : 'false'?>"
     data-speed="<?php echo $loc_params['scrollSpeed']*1000?>"
>
  <?php if($loc_params['arrows'] == 1): ?>
    <div class="banner-simple__arrow banner-simple__arrow--prev hidden"
         data-slider-arrow-left>
      <i class="banner-simple__ico banner-simple__ico--flip">
        <svg class="svg-icon">
          <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__angle-right"></use>
        </svg>
      </i>
    </div>
    <div class="banner-simple__arrow banner-simple__arrow--next hidden"
         data-slider-arrow-right>
      <i class="banner-simple__ico">
        <svg class="svg-icon">
          <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__angle-right"></use>
        </svg>
      </i>
    </div>
  <?php endif; ?>
  <div data-slider-slides
       data-slider-nojs>
    <?php if(is_true_array($parent_banner->getBannerImages())){ foreach ($parent_banner->getBannerImages() as $item){ ?>
      <?php $loc_url_target = $item->getTarget() == 1 ? "target=\"_blank\"" : ""?>
      <div data-slider-slide>
        <div class="banner-simple__item">

          <!-- Banner text information -->
          <?php if(trim($item->getDescription())): ?>
            <div class="banner-simple__content">
              <div class="typo"><?php echo $item->getDescription()?></div>
            </div>
          <?php endif; ?>

          <!-- Banner URL -->
          <?php if($item->getStatisticUrl()): ?>
            <a class="banner-simple__link" <?php if(isset($loc_url_target)){ echo $loc_url_target; } ?>
               href="<?php echo $item->getStatisticUrl()?>"
               rel="nofollow"></a>
          <?php endif; ?>

          <!-- Banner image -->
          <?php if($item->getImageOriginPath()): ?>
            <img class="banner-simple__image"
                 src="<?php echo $item->getImageOriginPath()?>"
                 alt="<?php echo $item->getName()?>">
          <?php endif; ?>

        </div>
      </div>
      <!-- /data-slider-slide -->
    <?php }} ?>
  </div>
</div>
<?php $mabilis_ttl=1552752299; $mabilis_last_modified=1491903770; ///home/user2263/public_html/templates/unishop/xbanners/banners/banner_simple.tpl ?>