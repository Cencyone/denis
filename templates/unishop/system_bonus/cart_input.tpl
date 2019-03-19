{if $showCheckBox}
  <div class="form__field form__field--hor form__field--hor-lg">
    <div class="form__label"></div>
    <div class="form__inner">
      <label class="form__checkbox">
        <span class="form__checkbox-field">
          <input type="checkbox" name="use_bonus">
        </span>
        <span class="form__checkbox-inner">
          <span class="form__checkbox-title">{tlang('Use points')}: {$bonus}</span>
        </span>
        <div class="form__tooltip">
          <div class="tooltip">
            <div class="tooltip__ico">
              <svg class="svg-icon">
                <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__tooltip"></use>
              </svg>
              <div class="tooltip__drop">
                <div class="tooltip__desc">
                  {tlang('You can use your points to get discount for purchasing. Each 1 point equals')} {echo module('system_bonus')->getBonusRate().$CS}
                </div>
              </div>
            </div>
          </div>
        </div>
      </label>

    </div>

  </div>
{/if}