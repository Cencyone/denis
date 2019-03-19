<?php if(widget('start_page_seo_text') && $CI->core->core_data['data_type'] == 'main'): ?>
  <div class="page__container">
    <div class="page__seo-text">
      <div class="typo typo--seo"><?php echo widget ('start_page_seo_text'); ?></div>
    </div>
  </div>
<?php endif; ?>

<div class="page__footer">
  <div class="page__container">
    <div class="footer">
      <div class="row">
        <div class="col-xs-6 col-sm-3">
          <div class="footer__title"><?php echo tlang ('Catalog'); ?></div>
          <div class="footer__inner">
            <?php echo load_catalog_menu ('navs/catalog_footer'); ?>
          </div>
        </div>
        <div class="col-xs-6 col-sm-3">
          <div class="footer__title"><?php echo tlang ('Store'); ?></div>
          <div class="footer__inner">
            <?php echo load_menu ('info_footer'); ?>
          </div>
        </div>
        <div class="clearfix visible-xs"></div>
        <div class="col-xs-6 col-sm-3 col--spacer-xs">
          <div class="footer__title"><?php echo tlang ('User menu'); ?></div>
          <div class="footer__inner">
            <?php echo view ('includes/footer_profile.tpl'); ?>
          </div>
        </div>
        <div class="col-xs-6 col-sm-3 col--spacer-xs">
          <div class="footer__title"><?php echo tlang ('Contacts'); ?></div>
          <div class="footer__inner">
            <ul class="footer__items">
              <?php if(trim(siteinfo('address')) != ""): ?>
                <li class="footer__item"><?php echo siteinfo ('address'); ?></li>
              <?php endif; ?>
              <?php if(trim(siteinfo('mainphone')) != ""): ?>
                <li class="footer__item"><?php echo nl2br (siteinfo('mainphone')); ?></li>
              <?php endif; ?>
              <?php if(trim(siteinfo('adminemail')) != ""): ?>
                <li class="footer__item"><?php echo siteinfo ('adminemail'); ?></li>
              <?php endif; ?>
              <li class="footer__item">
                <a class="footer__link" href="<?php echo site_url ('feedback'); ?>"
                   data-modal="feedback_modal"
                   rel="nofollow"
                ><?php echo tlang ('Feedback'); ?></a>
              </li>
            </ul>
          </div>
          <div class="footer__inner">
            <div class="soc-groups">

              <?php $soc_groups = [
              "vk-link"            => "vkontakte",
              "fb-link"            => "facebook",
              "google-link"        => "google-plus",
              "twitter-link"       => "twitter",
              "instagram-link"     => "instagram",
              "odnoklassniki-link" => "odnoklassniki"
              ]?>

              <?php if(is_true_array($soc_groups)){ foreach ($soc_groups as $name => $icon){ ?>
                <?php if(trim(siteinfo($name)) != ""): ?>
                  <a class="soc-groups__ico soc-groups__ico--<?php if(isset($icon)){ echo $icon; } ?>" href="<?php echo siteinfo ($name); ?>" target="_blank">
                    <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__<?php if(isset($icon)){ echo $icon; } ?>"></use></svg>
                  </a>
                <?php endif; ?>
              <?php }} ?>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="page__basement">
  <div class="page__container">
    <div class="basement">
      <div class="row row--ib row--ib-mid">
        <?php if(trim(siteinfo('companytype')) != ""): ?>
          <div class="col-xs-12 col-sm-6 col--align-left-sm col--spacer-xs"><?php echo siteinfo ('companytype'); ?></div>
        <?php endif; ?>
        <div class="col-xs-12 col-sm-6 col--align-right-sm col--spacer-xs"><?php echo tlang ('Powered by '); ?> <a
                  class="basement__link" href="http://imagecms.net" target="_blank"><?php echo tlang ('ImageCMS'); ?></a></div>
      </div>
    </div>
  </div>
</div><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903755; ///home/user2263/public_html/templates/unishop/includes/footer.tpl ?>