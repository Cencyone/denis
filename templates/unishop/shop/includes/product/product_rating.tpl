<div class="star-rating">
  <div class="star-rating__stars">
    {$rating = $model->getRating()}
    {for $i = 1; $i <= 5; $i++}
      <i class="star-rating__star {if $i <= $rating}star-rating__star--active{/if}"
         title="{echo $model->getRating()} {tlang('out of 5 stars')}">
        <svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__star"></use></svg>
      </i>
    {/for}
  </div>
  <div class="star-rating__votes">
    <a class="star-rating__votes-link"
       href="{site_url($model->getRouteUrl())}#comments-list">{tlang('Reviews')}: {tpl_product_comments_votes($model)}</a>
  </div>
</div>