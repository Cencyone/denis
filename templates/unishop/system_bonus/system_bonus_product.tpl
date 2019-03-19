{tpl_register_asset('system_bonus/css/system_bonus.css', 'before')}

{$loc_points = module('system_bonus')->getBonusForProductFront($model, $variant)*($quantity ? : 1);}
{$loc_points_label = SStringHelper::Pluralize($loc_points, array(tlang('system_bonus_points_pluralize_1'), tlang('system_bonus_points_pluralize_2'), tlang('system_bonus_points_pluralize_3')));}


<div class="bonus {if $modifier}bonus--{$modifier}{/if} {if $loc_points <= 0} hidden {/if}" data-bonus>
  <span class="bonus__title">{tlang('You will earn')}</span> <span class="bonus__points">+<span data-bonus-points>{$loc_points}</span> <span data-bonus-label>{$loc_points_label}</span> </span>
</div>