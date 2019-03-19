<div class="row row--ib row--ib row--vindent-m">
  {foreach $category->getTree() as $section}
    {if $section->getActive()}
    {$loc_img = !trim($section->getImage()) ? 'uploads/shop/nophoto/nophoto.jpg' : $section->getImage()}
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">

      <a class="catalog-section" href="{site_url($section->getRouteUrl())}">
        <div class="catalog-section__image">
          <img class="catalog-section__img" src="{media_url($loc_img)}" alt="{echo $section->getName()}">
        </div>
        <div class="catalog-section__caption">
          {echo $section->getName()}
        </div>
      </a>

    </div>
    {/if}
  {/foreach}
</div>
