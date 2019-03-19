<div class="product-photo">
    <button class="product-photo__item" type="button"
            data-product-photo-href="<?php echo site_url ($model->getRouteUrl()); ?>">
        <img class="product-photo__img" src="<?php echo $model->firstVariant->getMediumPhoto()?>"
             alt="<?php echo $model->getName()?>"
             title="<?php echo $model->getName()?>"
             data-product-photo>
        <!-- Photo labels -->
        <?php view('shop/includes/product/product_labels.tpl', [
        'model' => $model
        ])?>
    </button>
</div><?php $mabilis_ttl=1552752263; $mabilis_last_modified=1491903783; ///home/user2263/public_html/templates/unishop/shop/includes/product/product_cut_photo.tpl ?>