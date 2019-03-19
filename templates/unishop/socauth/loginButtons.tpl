<div class="socauth">
  {if $useGoogle == 'on'}
    <a class="socauth__link"
       href="https://accounts.google.com/o/oauth2/auth?redirect_uri=http://{echo $_SERVER[HTTP_HOST]}/socauth/google&response_type=code&client_id={echo $googleClientID}&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile"
       title="Google"
       data-socauth-popup>
      <i class="socauth__icon socauth__icon--gp">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__google-plus-logo"></use>
        </svg>
      </i>
    </a>
  {/if}

  {if $useVk == 'on'}
    <a class="socauth__link"
       href="http://oauth.vk.com/authorize?client_id={$vkClientID}&redirect_uri=http://{echo $_SERVER[HTTP_HOST]}/socauth/vk&response_type=code&scope=email&display=popup"
       title="Vkontakte"
       data-socauth-popup>
      <i class="socauth__icon socauth__icon--vk">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__vk-logo"></use>
        </svg>
      </i>
    </a>
  {/if}

  {if $useFaceBook == 'on'}
    <a class="socauth__link"
       href="https://www.facebook.com/dialog/oauth?client_id={$facebookClientID}&redirect_uri=http://{echo $_SERVER[HTTP_HOST]}/socauth/facebook&response_type=code&scope=email,user_location"
       title="Facebook"
       data-socauth-popup>
      <i class="socauth__icon socauth__icon--fb">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__facebook-logo"></use>
        </svg>
      </i>
    </a>
  {/if}

  {if $useYandex == 'on'}
    <a class="socauth__link"
       href="https://oauth.yandex.ru/authorize?response_type=code&client_id={$yandexClientID}&display=popup"
       title="Yandex"
       data-socauth-popup>
      <i class="socauth__icon socauth__icon--yandex">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__yandex-logo"></use>
        </svg>
      </i>
    </a>
  {/if}
</div>