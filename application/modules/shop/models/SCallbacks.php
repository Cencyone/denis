<?php

use Base\SCallbacks as BaseSCallbacks;

/**
 * Skeleton subclass for representing a row from the 'shop_callbacks' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.Shop
 */
class SCallbacks extends BaseSCallbacks
{

    public function attributeLabels() {
        return [
                'Name'    => lang('Your name', 'callbacks'),
                'Phone'   => lang('Your phone', 'callbacks'),
                'ThemeId' => lang('Subject'),
                'Comment' => lang('Comment'),
            //            'Name' => ShopCore::t('Имя'),
            //            'Phone' => ShopCore::t('Телефон'),
            //            'ThemeId' => ShopCore::t('Тема вопроса'),
            //            'Comment' => ShopCore::t('Дополнительная информация'),
               ];
    }

    public function validationMessage($type) {

        $messages = [
                     'required' => lang('The %s is required'),
                    ];
        return $messages[$type];
    }

    public function rules() {
        return [
                [
                 'field' => 'Name',
                 'label' => $this->getLabel('Name'),
                 'rules' => 'required',
                ],
                [
                 'field' => 'Phone',
                 'label' => $this->getLabel('Phone'),
                 'rules' => 'required|trim|xss_clean|max_length[50]|phone',
                ],
                [
                 'field' => 'ThemeId',
                 'label' => $this->getLabel('ThemeId'),
                 'rules' => 'trim',
                ],
                [
                 'field' => 'Comment',
                 'label' => $this->getLabel('Comment'),
                 'rules' => 'trim',
                ],
               ];
    }

}

// SCallbacks