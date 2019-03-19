{$loc_rating = $rating/20}
<div class="star-rating">
  <div class="star-rating__stars">
    {for $i = 1; $i <= 5; $i++}
      {if $i <= $loc_rating}
        <i class="star-rating__star"
           title="{$loc_rating} {tlang('out of 5 stars')}">
          <svg class="svg-icon">
            <use xlink:href="#svg-icon__star"></use>
          </svg>
        </i>
      {else:}
        <i class="star-rating__star star-rating__star--empty"
           title="{$loc_rating} {tlang('out of 5 stars')}">
          <svg class="svg-icon">
            <use xlink:href="#svg-icon__star"></use>
          </svg>
        </i>
      {/if}
    {/for}
  </div>
  <div class="star-rating__votes">
    {tlang('Reviews')}: {$votes}
  </div>
</div>