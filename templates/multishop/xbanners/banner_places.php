<?php

// Examples:
//  page_type:
//    - main,
//    - category,
//    - page,
//    - shop_category,
//    - product,
//    - brand,
//    - search

return [
        'main_banner'               => [
                                        'name'      => 'Home big slider',
                                        'width'     => '1280',
                                        'height'    => '400',
                                        'page_type' => 'main',
                                       ],
        'catalog_horisontal_banner' => [
                                        'name'      => 'Catalog horisontal banner',
                                        'width'     => '960',
                                        'height'    => '160',
                                        'page_type' => 'shop_category',
                                       ],
        'sidebar_banner'            => [
                                        'name'      => 'Sidebar banner',
                                        'width'     => '300',
                                        'height'    => '400',
                                        'page_type' => 'product',
                                       ],
       ];