{if count($languages) > 1}
  {$loc_cur_lang = getLanguage(array('id'=>CI::$APP->config->config['cur_lang']))}
  {$loc_cur_lang_url = "/" . $loc_cur_lang.identif . $current_address}
  <div class="user-panel__item">
    <div class="user-panel__link">
      <span class="user-panel__ico"><i class="ico-flag ico-flag--{$loc_cur_lang.identif}"></i></span>
      <i class="user-panel__arrow user-panel__arrow--down">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__arrow-down"></use>
        </svg>
      </i>
    </div>
    <div class="user-panel__drop user-panel__drop--rtl">
      <ul class="overlay">
        {foreach $languages as $lang}
          {$loc_page_url = "/" . $lang.identif . $current_address}
          <li class="overlay__item">
            <a class="overlay__link" href="{$loc_page_url}">
              <i class="overlay__icon">
                <i class="ico-flag ico-flag--{$lang.identif}"></i>
              </i>
              {$lang.lang_name}
            </a>
          </li>
        {/foreach}
      </ul>
    </div>
  </div>
{/if}