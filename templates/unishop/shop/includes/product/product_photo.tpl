<div class="product-photo"
     data-magnific-galley
     data-product-photo-scope
>
  
  <!-- Main photo -->
  <a class="product-photo__item product-photo__item--lg {if !$model->firstVariant->hasMainImage()}product-photo__item--no-photo{/if}" href="{media_url($model->firstVariant->getLargePhoto())}" target="_blank"
     data-product-photo-link
     {if $model->firstVariant->hasMainImage()}
     data-magnific-galley-main
     data-magnific-galley-title="{echo $model->getName()}"
     data-magnific-galley-close-text="{tlang('Close')}"
     {/if}
  >
    <img class="product-photo__img" src="{media_url($model->firstVariant->getMainPhoto())}" alt="{echo $model->getName()}" title="{echo $model->getName()}"
         data-product-photo
         data-zoom-image-small
         data-zoom-image="{media_url($model->firstVariant->getLargePhoto())}"
    >
    <!-- Photo labels -->
    {view('shop/includes/product/product_labels.tpl')}
    <span class="product-photo__zoom hidden hidden-sm hidden-xs" data-zoom-wrapper></span>
  </a>
  
  <!-- Additional images -->
  {if count($model->getSProductAdditionalImages()) > 0}
  <ul class="product-photo__thumbs">
    
    <!-- First output main image -->
    <li class="product-photo__thumb">
      <a class="product-photo__thumb-item" href="{media_url($model->firstVariant->getLargePhoto())}" target="_blank"
         data-product-photo-thumb
         data-product-photo-thumb-active
         data-magnific-galley-thumb
         data-magnific-galley-title="{echo $model->getName()}"
      >
        <img class="product-photo__thumb-img" src="{media_url($model->firstVariant->getSmallPhoto())}" alt="{echo $model->getName()}" title="{echo $model->getName()}"
             data-product-photo-main-thumb>
      </a>
    </li>
    
    <!-- Additional images list -->
    {foreach $model->getSProductAdditionalImages() as $thumb}
    <li class="product-photo__thumb">
      <a class="product-photo__thumb-item" href="{productImageUrl('products/additional/'.$thumb->getImageName())}" target="_blank"
         data-product-photo-thumb
         data-magnific-galley-thumb
         data-magnific-galley-title="{echo $model->getName()}"
      >
        <img class="product-photo__thumb-img"
             src="{productImageUrl('products/additional/thumb_'.$thumb->getImageName())}"
             alt="{echo $model->getName()} {echo $thumb->getPosition()}"
             title="{echo $model->getName()} {echo $thumb->getPosition()}"
             data-zoom-image-small>
      </a>
    </li>
    {/foreach}
    
  </ul>
  {/if}

</div>