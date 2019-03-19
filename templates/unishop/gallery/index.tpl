<div class="content">
	<div class="content__container">

		{$loc_main_title = $current_category.id ? $current_category.name : tlang('Gallery')}

		<!-- Gallery H1 Title -->
		<div class="content__header">
			<h1 class="content__title">
				{$loc_main_title}
			</h1>
		</div>

		{if count($gallery_category) > 1}
			<!-- Categories Navigation -->
			<div class="content__row content__row--sm">
			{view('gallery/includes/gallery_menu.tpl')}
			</div>
		{/if}

		<!-- Category Description -->
		{if trim($current_category.description) != ""}
			<div class="content__row">
				<div class="typo">{$current_category.description}</div>
			</div>
		{/if}

		<!-- Categories List -->
		<div class="content__row">
			{if count($albums) > 0}
				<div class="row row--ib row--vindent-m">
					{foreach $albums as $item}
						{if $item.count > 0}
						{$loc_cover_url = "uploads/gallery/" .$item.id ."/" .$item.cover_name ."_prev".$item.cover_ext}
						<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
							<div class="gallery-album">
								{if $item['cover_name']}
								<a class="gallery-album__image" href="{site_url('gallery/album/'.$item['id'])}">
									<img class="gallery-album__image-img" src="{media_url($loc_cover_url)}" alt="{$item.name}">
								</a>
								{/if}
								<h2 class="gallery-album__title">
									<a class="gallery-album__title-link" href="{site_url('gallery/album/'.$item['id'])}">{$item.name}</a>
								</h2>
								{if strip_tags(trim($item.description)) != ""}
								<div class="gallery-album__desc">
									{$item.description}
								</div>
								{/if}
							</div>
						</div>
						{/if}
					{/foreach}
				</div>
			{else:}
				<p class="message message--info">
					{tlang('There are no items to display. Please come back later!')}
				</p>
			{/if}
		</div>

	</div><!-- /.content__container -->
</div><!-- /.content -->