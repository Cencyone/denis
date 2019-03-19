<div class="star-rating">
  <div class="star-rating__stars">
    {for $i = 1; $i <= 5; $i++}
      {if $i <= $model->getRating()}
        <i class="star-rating__star"
           title="{$loc_rating} {tlang('out of 5 stars')}">
          <svg class="svg-icon svg-icon--star">
            <use xlink:href="#svg-icon__star"></use>
          </svg>
        </i>
      {else:}
        <i class="star-rating__star star-rating__star--empty"
           title="{$loc_rating} {tlang('out of 5 stars')}">
          <svg class="svg-icon svg-icon--star">
            <use xlink:href="#svg-icon__star"></use>
          </svg>
        </i>
      {/if}
    {/for}
  </div>
  <div class="star-rating__votes">
    <a class="star-rating__votes-link" href="{site_url($model->getRouteUrl())}#comments-list">{tlang('Reviews')}: {echo $model->getVotes()}</a>
  </div>
</div>