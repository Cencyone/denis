<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * List of valid filter params
 */
$config['filter'] = [
                     'p',
                     'brand',
                     'lp',
                     'rp',
                     'order',
                     'per_page',
                     'user_per_page',
                     'category',
                     'utm_medium',
                     'utm_campaign',
                     'utm_source',
                     'utm_term',
                     'utm_content',
                     'gclid',
                     'filtermobile',
                    ];

$config['url-brand-pattern'] = 'brand-{value}';
$config['url-property-pattern'] = 'property-{value}';
$config['url-static-separator'] = ';';
$config['url-use-multiple-values'] = true;