<li class="mobile-nav__item" data-mobile-nav-item data-nav-setactive-item>
	<a class="mobile-nav__link" href="<?php if(isset($link)){ echo $link; } ?>" <?php if($wrapper): ?> data-mobile-nav-link<?php endif; ?> <?php if(isset($target)){ echo $target; } ?> data-nav-setactive-link>
    <?php if(isset($title)){ echo $title; } ?>
    <?php if($wrapper): ?><span class="mobile-nav__has-children"><i class="mobile-nav__ico"><svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__arrow-right"></use></svg></i></span><?php endif; ?>
  </a>
	<?php if(isset($wrapper)){ echo $wrapper; } ?>
</li><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903781; ///home/user2263/public_html/templates/unishop/navs/mobile_nav/level_0/item_default.tpl ?>