<li class="overlay__item" <?php if($wrapper): ?>data-global-doubletap<?php endif; ?>>
  <a class="overlay__link" href="<?php if(isset($link)){ echo $link; } ?>" <?php if(isset($target)){ echo $target; } ?>><?php if(isset($title)){ echo $title; } ?>
    <?php if($wrapper): ?>
      <i class="overlay__arrow overlay__arrow--right"><svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__arrow-right"></use></svg></i>
    <?php endif; ?>
  </a>
  <?php if(isset($wrapper)){ echo $wrapper; } ?>
</li><?php $mabilis_ttl=1552752264; $mabilis_last_modified=1491903780; ///home/user2263/public_html/templates/unishop/navs/info_header/level_1/item_default.tpl ?>