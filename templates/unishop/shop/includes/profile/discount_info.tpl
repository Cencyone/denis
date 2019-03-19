<!-- User discount in general -->
{$user_discount = $discount->get_user_discount_api()}
{$user_distount_counter = 0;}

<div class="discount-info">
  <div class="discount-info__list">

    <!-- Total paid orders -->
    <div class="discount-info__row">
      <div class="discount-info__col discount-info__col--title">{tlang('Paid purchases amount')}</div>
      <div class="discount-info__col discount-info__col--value">{echo str_replace(" ", "", emmet_money($profile->getamout()))}</div>
    </div>

    <!-- User personal discount value -->
    {if $discount->userDiscountExists()}
      {$user_distount_counter ++;}
      {$user_discount_val = $user_discount["user"][0]["type_value"] == 1 ? $user_discount['user'][0]['value']."%": str_replace(" ", "", emmet_money($user_discount['user'][0]['value']))}
      <div class="discount-info__row">
        <div class="discount-info__col discount-info__col--title">{tlang('Your personal discount')}</div>
        <div class="discount-info__col discount-info__col--value">{echo $user_discount_val}</div>
      </div>
    {/if}

    <!-- User group discount value -->
    {if $discount->groupDiscountExists()}
      {$user_distount_counter ++;}
      {$user_discount_val = $user_discount["group_user"][0]["type_value"] == 1 ? $user_discount['group_user'][0]['value']."%": str_replace(" ", "", emmet_money($user_discount['group_user'][0]['value']))}
      <div class="discount-info__row">
        <div class="discount-info__col discount-info__col--title">{tlang('User goup discount')}</div>
        <div class="discount-info__col discount-info__col--value">{echo $user_discount_val}</div>
      </div>
    {/if}

    <!-- Cumulative discount -->
    {if $user_discount['comulativ']}
      {$user_distount_counter ++;}
      <div class="discount-info__row">
        <div class="discount-info__col discount-info__col--title">
          <div class="tooltip">
            <span class="tooltip__label">{tlang('Cumulative discount')}</span>
            <div class="tooltip__position">
              <div class="tooltip__ico">
                <svg class="svg-icon">
                  <use xlink:href="{$THEME}_img/sprite.svg#svg-icon__tooltip"></use>
                </svg>
                <div class="tooltip__drop">
                  <div class="tooltip__desc">

                    <div class="discount-table">
                      <div class="discount-table__row discount-table__row--header">
                        <div class="discount-table__item">{tlang('Discount')}</div>
                        <div class="discount-table__item">{tlang('Purchases amount')}</div>
                      </div>
                      {foreach $user_discount['comulativ'] as $key => $disc}
                        {$dist_summary = $disc["type_value"] == 1? $disc["value"] . '%' : str_replace(" ", "", emmet_money($disc['value']))}
                        <div class="discount-table__row">
                          <div class="discount-table__item">{$dist_summary}</div>
                          <div class="discount-table__item">{str_replace(" ", "", emmet_money($disc['begin_value']))}</div>
                        </div>
                        {if $disc['begin_value'] < $profile->getamout() and ($profile->getamout() < $disc['end_value'] || $disc['end_value'] === null)}
                          {$comul_curr = $user_discount['comulativ'][$key];}
                          {$comul_next = $user_discount['comulativ'][$key + 1];}
                        {/if}
                      {/foreach}
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="discount-info__col discount-info__col--value">
          {if $comul_curr > 0}
            {echo $comul_curr["type_value"] == 1 ? $comul_curr["value"]  . '%' : str_replace(" ", "", emmet_money($comul_curr['value']))}
            {if $comul_next > 0}
              <span class="discount-info__note">
									(- {echo  str_replace(" ","", emmet_money($comul_next['begin_value'] - $profile->getamout()))}
                {tlang('for')}
                {echo $comul_next["type_value"] == 1 ? $comul_next["value"]  . '%' : str_replace(" ", "", emmet_money($comul_next['value']))}
                )
								</span>
            {/if}
          {/if}
        </div>
      </div>
    {/if}

  </div>
  {if $user_distount_counter > 1}
    <div class="discount-info__message">
      <div class="message message--info">{tlang("More than one discount type can be used for your account at the moment. You'll be offered the highest discount rate available for your order.")}</div>
    </div>
  {/if}
</div>
