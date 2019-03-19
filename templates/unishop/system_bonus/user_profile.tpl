<div class="content__row">
  <div class="frame-content">
    <div class="frame-content__header frame-content__header--sm">
      <div class="frame-content__title frame-content__title--sm">{tlang('Earned points')}</div>
    </div>
    <div class="frame-content__inner">
      <div class="frame-content__row">

        <div class="discount-info">
          <div class="discount-info__list">
            <div class="discount-info__row">
              <div class="discount-info__col discount-info__col--title">
                <div class="tooltip">
                  <span class="tooltip__label">{tlang('Number of points')}</span>
                  <div class="tooltip__position">
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
              </div>
              <div class="discount-info__col discount-info__col--value">
                {echo module('system_bonus')->getBonusForUser()}
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>