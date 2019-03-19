<?php if(trim(siteinfo('mainphone')) != ""): ?>
  <div class="site-info">
    <div class="site-info__aside hidden-xs">
      <div class="site-info__ico site-info__ico--phone-big">
        <svg class="svg-icon">
          <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__phone-big"></use>
        </svg>
      </div>
    </div>
    <div class="site-info__inner">
      <div class="site-info__title"><?php echo nl2br (siteinfo('mainphone')); ?></div>
      <div class="site-info__desc">
        <a class="site-info__link" href="<?php echo site_url ('callbacks'); ?>"
           data-modal="callbacks_modal"
           rel="nofollow"
        >
          <?php echo tlang ('Callback'); ?>
        </a>
      </div>
    </div>
  </div>
<?php endif; ?><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903754; ///home/user2263/public_html/templates/unishop/includes/header_phone.tpl ?>