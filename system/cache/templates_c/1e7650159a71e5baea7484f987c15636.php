<?php if(count($languages) > 1): ?>
	<li class="mobile-nav__item mobile-nav__item--separator"><?php echo tlang ('Language'); ?></li>
	<?php if(is_true_array($languages)){ foreach ($languages as $lang){ ?>
		<li class="mobile-nav__item">
				<a class="mobile-nav__link" href="/<?php echo  $lang['identif']  . $current_address ?>"><?php echo $lang['lang_name']; ?></a>
		</li>
	<?php }} ?>
<?php endif; ?><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903757; //templates/unishop/widgets/mobile_langs.tpl ?>