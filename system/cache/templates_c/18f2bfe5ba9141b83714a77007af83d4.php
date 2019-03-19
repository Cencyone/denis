<?php if(tpl_is_filtered($minPrice,  $maxPrice)): ?>
<div class="content__row content__row--sm">
	<div class="active-filters">
		<ul class="active-filters__wrapper">
			
			<!-- Prices -->
			<?php if($curMin != $minPrice || $curMax != $maxPrice): ?>
			<li class="active-filters__item">
				<button class="active-filters__btn"
					 data-filter-result="price">
					<span class="active-filters__btn-link">
						<?php echo tlang ('min'); ?>&nbsp;<?php echo str_replace(" ", "", emmet_money($curMin))?>&nbsp;&nbsp;<?php echo tlang ('max'); ?>&nbsp;<?php echo str_replace(" ", "", emmet_money($curMax))?>
					</span>
					<i class="active-filters__btn-ico"></i>
				</button>
			</li>
			<?php endif; ?>
			
			<!-- Brands -->
			<?php if(is_true_array($selectedBrands)){ foreach ($selectedBrands as $brand){ ?>
			<li class="active-filters__item">
				<button class="active-filters__btn"
					 data-filter-result="checkbox"
					 data-filter-result-value="<?php echo $brand->url?>">
					<span class="active-filters__btn-link">
						<?php echo $brand->name?>
					</span>
					<i class="active-filters__btn-ico"></i>
				</button>
			</li>
			<?php }} ?>

			<!-- Properties -->
			<?php if(is_true_array($selectedProperties)){ foreach ($selectedProperties as $prop){ ?>
				<?php if(is_true_array($prop->selectedValues)){ foreach ($prop->selectedValues as $value){ ?>
				<li class="active-filters__item">
					<button class="active-filters__btn"
						 data-filter-result="checkbox"
						 data-filter-result-value="<?php echo $prop->csv_name?>-<?php echo  $value['id']  ?>"
					>
						<span class="active-filters__btn-link">
							<?php echo $value['value']?>
						</span>
						<i class="active-filters__btn-ico"></i>
					</button>
				</li>
				<?php }} ?>
			<?php }} ?>

		</ul>
	</div>
</div>
<?php endif; ?><?php $mabilis_ttl=1552752262; $mabilis_last_modified=1491903769; ///home/user2263/public_html/templates/unishop/smart_filter/includes/filter_results.tpl ?>