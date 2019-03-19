<div class="autocomplete"
     data-autocomplete="header-search"
     data-autocomplete-url="<?php echo shop_url ('search/ac'); ?>"
>

  <!-- Autocomplet Input Element BEGIN -->
  <div class="autocomplete__element">
    <form action="<?php echo shop_url ('search'); ?>" method="GET">
      <div class="input-group">
        <input class="form-control autocomplete__input"
               data-autocomplete-input
               type="text"
               name="text"
               autocomplete="off"
               placeholder="<?php echo tlang ('Search by item name or number'); ?>"
               value="<?php echo tpl_encode($CI->input->get('text'))?>"
               required
        >
        <span class="input-group-btn">
          <button class="btn btn-default" type="submit">
            <i class="btn-default__ico btn-default__ico--search">
              <svg class="svg-icon"><use xlink:href="<?php if(isset($THEME)){ echo $THEME; } ?>_img/sprite.svg#svg-icon__search"></use></svg>
            </i>
          </button>
        </span>
      </div>
    </form>
  </div>
  <!-- END Autocomplet Input Element -->


  <!-- Autocomplet Overlay Frame BEGIN -->
  <div class="autocomplete__frame hidden" data-autocomplete-frame>
    <?php for($i = 0; $i < 5; $i++){?>
      <a class="autocomplete__item hidden" href="#" data-autocomplete-product="<?php if(isset($i)){ echo $i; } ?>">
        <div class="autocomplete__product">
          <!-- Photo  -->
          <div class="autocomplete__product-photo">
            <div class="product-photo">
              <span class="product-photo__item product-photo__item--xs">
                <img class="product-photo__img" src="<?php echo media_url ('uploads/shop/nophoto/nophoto.jpg'); ?>" alt="No photo"
                     data-autocomplete-product-img>
              </span>
            </div>
          </div>

          <div class="autocomplete__product-info">
            <!-- Title -->
            <div class="autocomplete__product-title" data-autocomplete-product-name></div>
            <!-- Price -->
            <div class="autocomplete__product-price">
              <div class="product-price product-price--sm">
                <div class="product-price__item">
                  <div class="product-price__old" data-autocomplete-product-old-price></div>
                </div>
                <div class="product-price__item">
                  <div class="product-price__main" data-autocomplete-product-price></div>
                </div>
                <div class="product-price__item">
                  <div class="product-price__addition">
                    <div class="product-price__addition-item" data-autocomplete-product-addition-price></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </a>
    <?php } ?>

    <!-- Message if no items found after search request -->
    <div class="autocomplete__message autocomplete__message--noitems hidden" data-autocomplete-noitems>
      <?php echo tlang ('Your search did not match any products. Make sure all words are spelled correctly.'); ?>
    </div>

    <!-- Message if no items found after search request -->
    <div class="autocomplete__message autocomplete__message--noitems hidden" data-autocomplete-tooshort>
      <?php echo tlang ('Please enter two or more characters.'); ?>
    </div>

    <!-- Link to search page if number of results are more than 5 -->
    <div class="autocomplete__readmore hidden">
      <a href="<?php echo shop_url ('search?text='.$GET['text']); ?>"><?php echo tlang ('view all'); ?></a>
    </div>

  </div>
  <!-- END Autocomplet Overlay Frame -->

</div><!-- /.autocomplete --><?php $mabilis_ttl=1552752265; $mabilis_last_modified=1491903784; ///home/user2263/public_html/templates/unishop/shop/includes/search_and_brand/autocomplete.tpl ?>