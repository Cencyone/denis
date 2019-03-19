<div class="content">
  <div class="content__container">

    <!-- Gallery H1 Title -->
    <div class="content__header">
      <h1 class="content__title">
        {$album.name}
      </h1>
    </div>


    <!-- Categories Navigation -->
    {if count($gallery_category) > 1}
      <div class="content__row content__row--sm">
        {view('gallery/includes/gallery_menu.tpl')}
      </div>
    {/if}

    <!-- Category Description -->
    {if trim($album.description) != ""}
      <div class="content__row">
        <div class="typo">{$album.description}</div>
      </div>
    {/if}


    <!-- Albums List -->
    <div class="content__row">
      {if count($album.images) > 0}
        <ul class="row row--ib row--vindent-m"
            data-gallery-image="default">

          {foreach $album.images as $item}
            {$item.loc_prev_url = $album_url .$item['file_name'] .'_prev' .$item['file_ext'];}
            {$item.loc_thumb_url = $album_url ."_thumbs/" .$item['full_name'];}
            {$item.loc_filter_desc = strip_tags(trim($item.description));}
            <li class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
              <div class="gallery-image">
                <a class="gallery-image__link"
                   href="{media_url($item.loc_prev_url)}"
                   data-gallery-image-item
                   data-gallery-image-title="{$item.loc_filter_desc}">

                  <img class="gallery-image__img"
                       src="{media_url($item.loc_thumb_url)}"
                       alt="{$item.loc_filter_desc}">
                  <div class="gallery-image__zoom">
                    <div class="gallery-image__zoom-outer">
                      <div class="gallery-image__zoom-inner">
                        <i class="gallery-image__zoom-ico">
                          <svg class="svg-icon">
                            <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__zoom"></use>
                          </svg>
                        </i>
                      </div>
                    </div>
                  </div>
                </a>
                {if strip_tags(trim($item.description)) != ""}
                  <div class="gallery-image__desc">
                    <div class="typo">{$item.description}</div>
                  </div>
                {/if}
              </div>
            </li>
          {/foreach}
        </ul>
      {else:}
        <p class="message message--info">
          {tlang('There are no items to display. Please come back later!')}
        </p>
      {/if}
    </div>

  </div><!-- /.content__container -->
</div><!-- /.content -->