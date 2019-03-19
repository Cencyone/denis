<?php $loc_items = $category->getChildsByParentIdI18n($category->getId())?>
<?php if(count($loc_items)): ?>
    <div class="content__sidebar-item">
        <ul class="sidebar-nav">
            <?php if(is_true_array($loc_items)){ foreach ($loc_items as $item){ ?>
                <li class="sidebar-nav__item">
                    <a class="sidebar-nav__link" href="<?php echo site_url ($item->getRouteUrl()); ?>"><?php echo $item->getName()?></a>
                </li>
            <?php }} ?>
        </ul>
    </div>
<?php endif; ?><?php $mabilis_ttl=1552752262; $mabilis_last_modified=1491903783; ///home/user2263/public_html/templates/unishop/shop/includes/category/category_subnav.tpl ?>