{if tpl_is_filtered($minPrice,  $maxPrice)}
<div class="content__row content__row--sm">
	<div class="active-filters">
		<ul class="active-filters__wrapper">
			
			<!-- Prices -->
			{if $curMin != $minPrice || $curMax != $maxPrice}
			<li class="active-filters__item">
				<button class="active-filters__btn"
					 data-filter-result="price">
					<span class="active-filters__btn-link">
						{tlang('min')}&nbsp;{echo str_replace(" ", "", emmet_money($curMin))}&nbsp;&nbsp;{tlang('max')}&nbsp;{echo str_replace(" ", "", emmet_money($curMax))}
					</span>
					<i class="active-filters__btn-ico"></i>
				</button>
			</li>
			{/if}
			
			<!-- Brands -->
			{foreach $selectedBrands as $brand}
			<li class="active-filters__item">
				<button class="active-filters__btn"
					 data-filter-result="checkbox"
					 data-filter-result-value="{echo $brand->url}">
					<span class="active-filters__btn-link">
						{echo $brand->name}
					</span>
					<i class="active-filters__btn-ico"></i>
				</button>
			</li>
			{/foreach}

			<!-- Properties -->
			{foreach $selectedProperties as $prop}
				{foreach $prop->selectedValues as $value}
				<li class="active-filters__item">
					<button class="active-filters__btn"
						 data-filter-result="checkbox"
						 data-filter-result-value="{echo $prop->csv_name}-{echo $value.id}"
					>
						<span class="active-filters__btn-link">
							{echo $value['value']}
						</span>
						<i class="active-filters__btn-ico"></i>
					</button>
				</li>
				{/foreach}
			{/foreach}

		</ul>
	</div>
</div>
{/if}