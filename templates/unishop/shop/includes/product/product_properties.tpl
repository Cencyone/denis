<div class="properties">
  {foreach $items as $item}
    <div class="properties__item">
      <div class="properties__header">
        <div class="properties__wrapper">
          <div class="properties__title">

            <div class="tooltip">
              <span class="tooltip__label">{$item.Name}</span>
              {if trim($item.Desc)}
                <div class="tooltip__position">
                  <div class="tooltip__ico">
                    <svg class="svg-icon"><use xlink:href="{$THEME}_img/sprite.svg#svg-icon__tooltip"></use></svg>
                    <div class="tooltip__drop">
                      <div class="tooltip__desc">
                        {$item.Desc}
                      </div>
                    </div>
                  </div>
                </div>
              {/if}
            </div>

          </div><!-- /.properties__title -->
        </div>
      </div>
      <div class="properties__value">{$item.Value}</div>
    </div>
  {/foreach}
</div>