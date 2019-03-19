<?php if(trim(siteinfo('schedule')) != ""): ?>
  <div class="site-info">
    <div class="site-info__aside hidden-xs">
      <div class="site-info__ico site-info__ico--clock-big">
        <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__clock-big"></use></svg>
      </div>
    </div>
    <div class="site-info__inner">
      <div class="site-info__desc">
        <?php echo siteinfo('schedule')?>
      </div>
    </div>
  </div>
<?php endif; ?><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903755; ///home/user2263/public_html/templates/unishop/includes/header_schedule.tpl ?>