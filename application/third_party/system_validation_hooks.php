<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/*
 * TODO: This file is legacy from older versions of CMS and should be 
 * transformed into some bootstrap logic along with Lib_init library
 */

function checkPhpVersionOrDie() {
    $isMinVersion = version_compare(PHP_VERSION, '5.6.0') >= 0;
    if (!$isMinVersion) {
        show_error('PHP version must be 5.6.0 or higher', 500);
    }
}
