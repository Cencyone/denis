<div class="content">
  <div class="content__container">

    <!-- Category title -->
    <div class="content__header">
      <h1 class="content__title">
        {$category.name}
      </h1>
    </div>

    <!-- Category description -->
    {if trim($category.short_desc)}
      <div class="content__row">
        <div class="typo">{$category.short_desc}</div>
      </div>
    {/if}

    <!-- Category post list BEGIN -->
    <div class="content__row">
      {if count($pages) > 0}
        <div class="row row--vindent-m">
          {foreach $pages as $item}
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

              <!-- Post -->
              <article class="post">
                {if trim($item.field_list_image) != ""}
                  <a class="post__image" href="{site_url($item.full_url)}">
                    <img src="{$item.field_list_image}" alt="{$item.title}">
                  </a>
                {/if}
                <div class="post__inner">
                  <time class="post__date"
                        datetime="{date('Y-m-d', $item.publish_date)}">{tpl_locale_date('d F Y', $item.publish_date)}</time>
                  <h2 class="post__title">
                    <a class="post__title-link" href="{site_url($item.full_url)}">{$item.title}</a>
                  </h2>

                  {if trim($item.prev_text) != ""}
                    <div class="post__desc">
                      <div class="typo">{$item.prev_text}</div>
                    </div>
                    <a class="post__readmore hidden-xs" href="{site_url($item.full_url)}">{tlang('Read more')}</a>
                  {/if}
                </div>
              </article>

            </div>
          {/foreach}
        </div>
      {else:}
        <p class="typo">{tlang('There are no items to display. Please come back later!')}</p>
      {/if}

      <!-- Category pagination BEGIN -->
      {if $pagination}
        <div class="content__pagination">
          {$pagination}
        </div>
      {/if}
    </div><!-- /.content__row -->

  </div><!-- /.content__container -->
</div><!-- /.content -->