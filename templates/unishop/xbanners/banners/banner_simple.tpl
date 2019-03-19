{$loc_params = $parent_banner->getEffects()}
<div class="banner-simple"
     data-slider="banner-simple"
     data-autoplay="{echo $loc_params['autoplay'] == 1 ? 'true' : 'false'}"
     data-autoplayspeed="{echo $loc_params['autoplaySpeed']*1000}"
     data-arrows="{echo $loc_params['arrows'] == 1 ? 'true' : 'false'}"
     data-dots="{echo $loc_params['dots'] == 1 ? 'true' : 'false'}"
     data-fade="{echo $loc_params['fade'] == 1 ? 'true' : 'false'}"
     data-infinite="{echo $loc_params['infinite'] == 1 ? 'true' : 'false'}"
     data-speed="{echo $loc_params['scrollSpeed']*1000}"
>
  {if $loc_params['arrows'] == 1}
    <div class="banner-simple__arrow banner-simple__arrow--prev hidden"
         data-slider-arrow-left>
      <i class="banner-simple__ico banner-simple__ico--flip">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__angle-right"></use>
        </svg>
      </i>
    </div>
    <div class="banner-simple__arrow banner-simple__arrow--next hidden"
         data-slider-arrow-right>
      <i class="banner-simple__ico">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__angle-right"></use>
        </svg>
      </i>
    </div>
  {/if}
  <div data-slider-slides
       data-slider-nojs>
    {foreach $parent_banner->getBannerImages() as $item}
      {$loc_url_target = $item->getTarget() == 1 ? "target=\"_blank\"" : ""}
      <div data-slider-slide>
        <div class="banner-simple__item">

          <!-- Banner text information -->
          {if trim($item->getDescription())}
            <div class="banner-simple__content">
              <div class="typo">{echo $item->getDescription()}</div>
            </div>
          {/if}

          <!-- Banner URL -->
          {if $item->getStatisticUrl()}
            <a class="banner-simple__link" {$loc_url_target}
               href="{echo $item->getStatisticUrl()}"
               rel="nofollow"></a>
          {/if}

          <!-- Banner image -->
          {if $item->getImageOriginPath()}
            <img class="banner-simple__image"
                 src="{echo $item->getImageOriginPath()}"
                 alt="{echo $item->getName()}">
          {/if}

        </div>
      </div>
      <!-- /data-slider-slide -->
    {/foreach}
  </div>
</div>
