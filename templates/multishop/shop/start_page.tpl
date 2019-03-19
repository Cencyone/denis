<div class="content">
	
	<!-- Homepage Main banner. Hidden on mobile  -->
	<div class="content__main-banner">
		<div class="content__container">
			{if $loc_main_banner = getBanner('main_banner', 'object')}
				<div class="content__row content__row--sm">
				{view('xbanners/banners/banner_simple.tpl', [
					'parent_banner' => $loc_main_banner
				])}
				</div>
			{/if}
		</div>
	</div>

	<!-- Homepage Popular products widget -->
	{widget('products_hits')}

	<!-- Homepage Catalog dashboard -->
	<div class="content__row">
		<div class="content__container">
			{load_catalog_menu('navs/catalog_board')}
		</div>
	</div>

	<!-- Homepage Latest news widget -->
	{widget('latest_news')}

	<!-- Homepage Brands widget -->
	{widget('brands')}

	<!-- Homepage SEO text -->
	{if widget('start_page_seo_text')}
	<div class="content__row">
		<div class="content__container">
			<div class="typo">{widget('start_page_seo_text')}</div>
		</div>
	</div>
	{/if}

</div>