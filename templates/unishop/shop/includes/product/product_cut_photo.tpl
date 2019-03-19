<div class="product-photo">
    <button class="product-photo__item" type="button"
            data-product-photo-href="{site_url($model->getRouteUrl())}">
        <img class="product-photo__img" src="{echo $model->firstVariant->getMediumPhoto()}"
             alt="{echo $model->getName()}"
             title="{echo $model->getName()}"
             data-product-photo>
        <!-- Photo labels -->
        {view('shop/includes/product/product_labels.tpl', [
        'model' => $model
        ])}
    </button>
</div>