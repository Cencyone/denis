<div class="order-history">
	<ul class="order-history__row">
		<li class="order-history__col order-history__col--header">{tlang('Id')}</li>
		<li class="order-history__col order-history__col--header hidden-xs">{tlang('Date')}</li>
		<li class="order-history__col order-history__col--header">{tlang('Status')}</li>
		<li class="order-history__col order-history__col--header hidden-xs">{tlang('Total Price')}</li>
	</ul>
	{foreach $orders as $order}
	{$loc_paid = $order->getPaid() ? tlang('Paid successfully') : tlang('Not paid')}
	<ul class="order-history__row">
		<!-- Order Id -->
		<li class="order-history__col">
			<a class="order-history__link" href="{shop_url('order/view/' . $order->getKey())}">{tlang('Order')}&nbsp;#{echo $order->getId()}</a>
		</li>
		<!-- Order Date -->
		<li class="order-history__col hidden-xs">
			{echo tpl_locale_date('d F Y', $order->getDateCreated())}
		</li>
		<!-- Order stage and payment status -->
		<li class="order-history__col">
			{echo $order->getSOrderStatuses()->setLocale(MY_Controller::getCurrentLocale())->getName()} &mdash; {$loc_paid}
		</li>
		<!-- Order total cost -->
		<li class="order-history__col hidden-xs">
			<div class="cart-price">
				<div class="cart-price__main">
					{echo emmet_money($order->getFinalPrice(), 'span.cart-price__main-value', '', 'span.cart-price__main-cur')}
				</div>
			</div>
		</li>
	</ul>
	{/foreach}
</div>