<?php

use Base\SProductVariantPriceType as BaseSProductVariantPriceType;
use Currency\Currency;
use Propel\Runtime\Exception\PropelException;

/**
 * Skeleton subclass for representing a row from the 'shop_product_variants_price_types' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class SProductVariantPriceType extends BaseSProductVariantPriceType
{

    /**
     * @param array $data
     * @throws PropelException
     */
    public function addToModel(array $data)
    {

        $this->setNameType($data['name']);

        if ($data['price_type']) {
            $this->setPriceType($data['price_type']);
        }

        /** если цена не процент */
        if ($data['price_type'] == 1) {

            $this->setCurrencyId($data['currency']);
        }

        $this->setStatus($data['status'] ? 1 : 0);
        $this->setConsiderDiscount($data['consider_discount'] ? true : false);

        if ($this->isNew()) {
            $systemBonusList = SProductVariantPriceTypeQuery::create()
                ->find();
            /** @var SProductVariantPriceType $item */
            foreach ($systemBonusList as $item) {
                $item->setPosition($item->getPosition() + 1);
                $item->save();
            }
        }

        $this->save();

        $this->deleteOldValues();

        if (is_array($data['role_id'])) {

            foreach ($data['role_id'] as $value) {

                $price_type_value = new SProductVariantPriceTypeValue();
                $price_type_value->setPriceTypeId($this->getId());
                $price_type_value->setValue($value);
                $price_type_value->save();

            }
        }

    }

    /**
     * @return void
     */
    private function deleteOldValues()
    {

        $priceTypeValues = SProductVariantPriceTypeValueQuery::create()
            ->findByPriceTypeId($this->getId());

        $priceTypeValues->delete();

    }

    /**
     * @param null $group
     * @return array
     */
    public function rules($group = null)
    {
        $post = \CI::$APP->input->post();

        $fields = [[
            'field' => 'name',
            'label' => lang('Title', 'admin'),
            'rules' => 'required|trim|min_length[3]',
        ]];
        if ($group != 'edit') {
            $fields[] = [
                'field' => 'price_type',
                'label' => lang('Type', 'admin'),
                'rules' => 'required|trim|numeric',
            ];
        }
        if ($post['price_type'] != '2') {
            $fields[] = [
                'field' => 'currency',
                'label' => lang('Currency', 'admin'),
                'rules' => 'required|trim',
            ];
        }
        return $fields;
    }

}