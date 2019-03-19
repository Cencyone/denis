<?php if(count($languages) > 1): ?>
  <?php $loc_cur_lang = getLanguage(array('id'=>CI::$APP->config->config['cur_lang']))?>
  <?php $loc_cur_lang_url = "/" .  $loc_cur_lang['identif']  . $current_address ?>
  <div class="user-panel__item">
    <div class="user-panel__link">
      <span class="user-panel__ico"><i class="ico-flag ico-flag--<?php echo $loc_cur_lang['identif']; ?>"></i></span>
      <i class="user-panel__arrow user-panel__arrow--down">
        <svg class="svg-icon">
          <use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__arrow-down"></use>
        </svg>
      </i>
    </div>
    <div class="user-panel__drop user-panel__drop--rtl">
      <ul class="overlay">
        <?php if(is_true_array($languages)){ foreach ($languages as $lang){ ?>
          <?php $loc_page_url = "/" .  $lang['identif']  . $current_address ?>
          <li class="overlay__item">
            <a class="overlay__link" href="<?php if(isset($loc_page_url)){ echo $loc_page_url; } ?>">
              <i class="overlay__icon">
                <i class="ico-flag ico-flag--<?php echo $lang['identif']; ?>"></i>
              </i>
              <?php echo $lang['lang_name']; ?>
            </a>
          </li>
        <?php }} ?>
      </ul>
    </div>
  </div>
<?php endif; ?><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903757; //templates/unishop/widgets/languages.tpl ?>