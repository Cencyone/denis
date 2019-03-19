{$loc_items = $category->getChildsByParentIdI18n($category->getId())}
{if count($loc_items)}
    <div class="content__sidebar-item">
        <ul class="sidebar-nav">
            {foreach $loc_items as $item}
                <li class="sidebar-nav__item">
                    <a class="sidebar-nav__link" href="{site_url($item->getRouteUrl())}">{echo $item->getName()}</a>
                </li>
            {/foreach}
        </ul>
    </div>
{/if}