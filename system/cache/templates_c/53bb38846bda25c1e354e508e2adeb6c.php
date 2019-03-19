<li class="list-nav__item" <?php if($wrapper): ?>data-global-doubletap<?php endif; ?>>
  <a class="list-nav__link" href="<?php if(isset($link)){ echo $link; } ?>" <?php if(isset($target)){ echo $target; } ?>><?php if(isset($title)){ echo $title; } ?>
    <?php if($wrapper): ?>
      <i class="list-nav__arrow list-nav__arrow--down"><svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__arrow-down"></use></svg></i>
    <?php endif; ?>
  </a>
  <?php if(isset($wrapper)){ echo $wrapper; } ?>
</li><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903780; ///home/user2263/public_html/templates/unishop/navs/info_header/level_0/item_default.tpl ?>