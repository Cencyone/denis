{$loc_last_item = array_pop($navi_cats)}

{if $loc_last_item}
  <div class="page__breadcrumbs">
    <div class="page__container">

      <ul class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
        <li class="breadcrumbs__item" typeof="v:Breadcrumb">
          <a class="breadcrumbs__link" href="{site_url('')}" rel="v:url" property="v:title">{tlang('Home')}</a>
        </li>
        {foreach $navi_cats as $item}
          <li class="breadcrumbs__item" typeof="v:Breadcrumb">
            <a class="breadcrumbs__link" href="{site_url($item.path_url)}" rel="v:url" property="v:title">
              {$item.name}
            </a>
          </li>
        {/foreach}
        <li class="breadcrumbs__item hidden-xs hidden-sm" typeof="v:Breadcrumb" rel="v:url nofollow" property="v:title">
          {$loc_last_item.name}
        </li>
      </ul>

    </div>
  </div>
{/if}