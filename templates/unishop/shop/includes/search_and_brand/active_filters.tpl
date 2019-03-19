<div class="active-filters">
  <ul class="active-filters__wrapper">
    <li class="active-filters__item">
      <a class="active-filters__btn" href="{shop_url($parent_url)}">
        <span class="active-filters__btn-link">
          {tpl_category_active_filter($CI->input->get('category'), $categories)}
          </span>
          <i class="active-filters__btn-ico"></i>
      </a>
    </li>
  </ul>
</div>