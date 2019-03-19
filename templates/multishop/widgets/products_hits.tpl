{if count($products) > 0}
<div class="content__row">
	<div class="content__container">
		{view('widgets/includes/widget_primary.tpl', [
			'parent_products' => $products,
			'parent_title' => getWidgetTitle('products_hits')
		])}
	</div>
</div>
{/if}