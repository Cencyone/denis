<?php $loc_last_item = array_pop($navi_cats)?>

<?php if($loc_last_item): ?>
  <div class="page__breadcrumbs">
    <div class="page__container">

      <ul class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
        <li class="breadcrumbs__item" typeof="v:Breadcrumb">
          <a class="breadcrumbs__link" href="<?php echo site_url (''); ?>" rel="v:url" property="v:title"><?php echo tlang ('Home'); ?></a>
        </li>
        <?php if(is_true_array($navi_cats)){ foreach ($navi_cats as $item){ ?>
          <li class="breadcrumbs__item" typeof="v:Breadcrumb">
            <a class="breadcrumbs__link" href="<?php echo site_url ( $item['path_url'] ); ?>" rel="v:url" property="v:title">
              <?php echo $item['name']; ?>
            </a>
          </li>
        <?php }} ?>
        <li class="breadcrumbs__item hidden-xs hidden-sm" typeof="v:Breadcrumb" rel="v:url nofollow" property="v:title">
          <?php echo $loc_last_item['name']; ?>
        </li>
      </ul>

    </div>
  </div>
<?php endif; ?><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903757; ///home/user2263/public_html/templates/unishop/widgets/breadcrumbs.tpl ?>