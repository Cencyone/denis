<!--
Requires "One click module" http://www.imagecms.net/addons/shop/product/337
url - /one_click_order/make_order/'.$variant_id
$data
$variant_id
-->
<a class="link link--main link--js" href="{site_url('one_click_order/make_order/'.$variant_id)}" rel="nofollow"
   data-one-click-btn="one_click_modal"
   data-one-click-variant="{$variant_id}"
   data-one-click-href="{site_url('one_click_order/make_order')}"
>
  {tlang('Buy in one click')}
</a>