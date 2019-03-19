{$state = in_array($model->getId(), $CI->session->userdata('shopForCompare')) }

<div data-compare-scope="add_to">

  <!-- Item isn't in compare list -->
  <button class="product-actions__link {echo $state?'hidden':''}" type="button"
          data-compare-add="{shop_url('compare_api/add/'.$model->getId())}"
          data-loader="{tlang('Loading...')}"
          rel="nofollow"
  >{tlang('Add to compare')}
  </button>

  <!-- Item already is in compare list -->
  <a class="product-actions__link {echo $state?'':'hidden'}" href="{shop_url('compare')}"
     data-compare-open
     rel="nofollow"
  >{tlang('Open in compare list')}
  </a>

</div>