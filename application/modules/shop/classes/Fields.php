<?php

/**
 * use in:
 *  - profileapi.php
 * @method static Name
 * @method static Email
 * @method static ShippingAddress
 * @method static Phone
 * @method static Password
 */
class Fields
{

    public static function getField($name) {
        $fields = [
                   'Name'            => lang('Your name'),
                   'Email'           => lang('E-Mail Address'),
                   'ShippingAddress' => lang('Shipping address'),
                   'Phone'           => lang('Your phone'),
                   'Password'        => lang('Password', 'auth'),
                  ];
        return array_key_exists($name, $fields) ? $fields[$name] : NULL;

    }

    public static function __callStatic($name, $arguments = FALSE) {

        return static::getField($name);
    }

}