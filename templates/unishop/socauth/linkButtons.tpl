<div class="socauth">

  <!-- Google -->
  {if $useGoogle == 'on' && $google != 'linked'}
    {if $google != 'main'}
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
  {elseif $useGoogle == 'on'}
    <button class="socauth__link" type="button" data-socauth-unlink="{site_url('socauth/unlink/google')}">
      <i class="socauth__icon socauth__icon--active socauth__icon--gp" title="Vkontakte">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__google-plus-logo"></use>
        </svg>
      </i>
    </button>
  {/if}

  <!-- Vkontakte -->
  {if $useVk == 'on' && $vk != 'linked'}
    {if $vk != 'main'}
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
  {elseif $useVk == 'on'}
    <button class="socauth__link" type="button" data-socauth-unlink="{site_url('socauth/unlink/vk')}">
      <i class="socauth__icon socauth__icon--active socauth__icon--vk" title="Vkontakte">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__vk-logo"></use>
        </svg>
      </i>
    </button>
  {/if}

  <!-- Facebook -->
  {if $useFaceBook == 'on' && $fb != 'linked'}
    {if $fb != 'main'}
      <a class="socauth__link"
         href="https://www.facebook.com/dialog/oauth?client_id={$facebookClientID}&redirect_uri=http://{echo $_SERVER[HTTP_HOST]}/socauth/facebook&response_type=code&scope=email,user_hometown"
         title="Facebook"
         data-socauth-popup>
        <i class="socauth__icon socauth__icon--fb">
          <svg class="svg-icon">
            <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__facebook-logo"></use>
          </svg>
        </i>
      </a>
    {/if}
  {elseif $useFaceBook == 'on'}
    <button class="socauth__link" type="button" data-socauth-unlink="{site_url('socauth/unlink/fb')}">
      <i class="socauth__icon socauth__icon--active socauth__icon--fb" title="Facebook">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__facebook-logo"></use>
        </svg>
      </i>
    </button>
  {/if}


  <!-- Yandex -->
  {if $useYandex == 'on' && $ya != 'linked'}
    {if $ya != 'main'}
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
  {elseif $useYandex == 'on'}
    <button class="socauth__link" type="button" data-socauth-unlink="{site_url('socauth/unlink/ya')}">
      <i class="socauth__icon socauth__icon--active socauth__icon--yandex" title="Yandex">
        <svg class="svg-icon">
          <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__yandex-logo"></use>
        </svg>
      </i>
    </button>
  {/if}
</div>