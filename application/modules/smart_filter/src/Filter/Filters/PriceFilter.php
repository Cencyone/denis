<?php

namespace smart_filter\src\Filter\Filters;

class PriceFilter extends AbstractEntityFilter
{

    /**
     * @var int
     */
    private $priceType;

    /**
     * @return null|array
     */
    public function getValues() {

        $priceType = $this->getCurrentPriceTypeId();

        $query = $this->db
            ->from('shop_product_variants')
            ->join('shop_products', 'shop_product_variants.product_id=shop_products.id')
            ->join('shop_product_categories', 'shop_product_categories.product_id = shop_products.id');

        if ($priceType > 0) {
            $query
                ->join(
                    'shop_product_variants_prices',
                    "shop_product_variants.id = shop_product_variants_prices.var_id  and  shop_product_variants_prices.type_id = {$priceType}",
                    'left'
                )
                ->select(
                    'FLOOR(MIN(IF(shop_product_variants_prices.final_price is not null ,shop_product_variants_prices.final_price,shop_product_variants.price ))) AS minCost
                ,FLOOR(MAX(IF(shop_product_variants_prices.final_price is not null ,shop_product_variants_prices.final_price,shop_product_variants.price ))) AS maxCost'
                );

        } else {
            $query
                ->select('FLOOR(MIN(shop_product_variants.price)) AS minCost, FLOOR(MAX(shop_product_variants.price)) AS maxCost');
        }

        $query = $query
            ->where('shop_product_categories.category_id', $this->parameters->getCategoryId())
            ->where('shop_products.active', 1)
            ->where('shop_products.archive', 0)
            ->get();

        $cost = ($query->num_rows() > 0) ? $query->row_array() : null;

        return $cost;
    }

    /**
     * @return array|null
     */
    public function getSelectedInFilterVariants() {
        if ($this->parameters->hasPrice()) {
            $priceType = $this->getCurrentPriceTypeId();
            $query = $this->db->distinct()->select('shop_products.id')
                ->from('shop_product_variants')
                ->join('shop_products', 'shop_product_variants.product_id=shop_products.id')
                ->join('shop_product_categories', 'shop_product_categories.product_id = shop_products.id');

            if ($priceType > 0) {
                $query->join('shop_product_variants_prices', "shop_product_variants_prices.var_id = shop_product_variants.id and shop_product_variants_prices.type_id = {$priceType}", 'left');
            }

            if ($this->parameters->hasLowPrice()) {
                if ($priceType > 0) {
                    $query->where('if(shop_product_variants_prices.final_price is not null, shop_product_variants_prices.final_price, shop_product_variants.price) >= ', $this->parameters->getLowPrice());
                } else {
                    $query->where('shop_product_variants.price >= ', $this->parameters->getLowPrice());
                }
            }

            if ($this->parameters->hasHighPrice()) {
                if ($priceType > 0) {
                    $query->where('if(shop_product_variants_prices.final_price is not null, shop_product_variants_prices.final_price, shop_product_variants.price) <= ', $this->parameters->getHighPrice());
                } else {
                    $query->where('shop_product_variants.price <= ', $this->parameters->getHighPrice());
                }
            }

            $query = $query->where('shop_product_categories.category_id', $this->parameters->getCategoryId())
                ->where('shop_products.active', 1)
                ->get();

            $products = $query->num_rows() ? array_column($query->result_array(), 'id') : [];

            return $products;
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentPriceTypeId() {

        if (!\MY_Controller::isPremiumCMS()) {
            return false;
        }

        if ($this->priceType === null) {

            /** @var \DX_Auth $dx */
            $dx = \CI::$APP->dx_auth;
            $role_id = $dx->get_role_id();

            $role_id === false && $role_id = -1;

            $query = $this->db->select('price_type_id')
                ->from('shop_product_variants_price_type_values')
                ->join(
                    'shop_product_variants_price_types',
                    'shop_product_variants_price_types.id = shop_product_variants_price_type_values.price_type_id'
                )
                ->where('shop_product_variants_price_types.status', 1)
                ->where('shop_product_variants_price_type_values.value', $role_id)
                ->limit(1)
                ->get();

            if ($query->num_rows() > 0) {
                $this->priceType = $query->row_array()['price_type_id'];
            } else {
                $this->priceType = false;
            }

        }
        return $this->priceType;
    }

}