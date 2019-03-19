<?php

namespace smart_filter\src\Filter\Filters;

class BrandsFilter extends AbstractEntityFilter
{

    /**
     * @var array|null
     */
    protected $filterVariants = null;

    /**
     * Get all brands with count products by category
     * @return array|null
     */
    public function getValues() {

        $productsKeyByBrands = $this->selectProductsWithBrandByCategory();

        if ($productsKeyByBrands) {

            $brands = $this->selectBrandsByIds(array_keys($productsKeyByBrands));

            foreach (array_keys($brands) as $id) {
                $brands[$id]->countProducts = count($productsKeyByBrands[$id]);
                $brands[$id]->productIds = $productsKeyByBrands[$id];
            }

            return $brands;
        }

    }

    /**
     * @return array
     * Propel
     * $products = SProductsQuery::create()
     * ->select(['id', 'brand_id'])
     * ->joinShopProductCategories()
     * ->useShopProductCategoriesQuery()
     * ->filterByCategoryId($this->parameters->getCategoryId())
     * ->endUse()
     * ->filterByActive(true)
     * ->find()
     * ->toArray();
     */
    private function selectProductsWithBrandByCategory() {
        $products = $this->db->select('shop_products.id, brand_id')
            ->join('shop_product_categories', 'shop_product_categories.product_id = shop_products.id')
            ->join('shop_products_i18n', "shop_products.id = shop_products_i18n.id and locale = '".$this->parameters->getLocale()."'")
            ->where('shop_product_categories.category_id', $this->parameters->getCategoryId())
            ->where('shop_products.active', true)
            ->where('shop_products.archive', false)
            ->get('shop_products');
        $products = $products->num_rows() ? $products->result_array() : [];

        if ($products) {

            $byBrand = [];
            foreach ($products as $item) {
                $byBrand[$item['brand_id']][] = $item['id'];
            }
            return $byBrand;
        }

    }

    /**
     * @param array $ids
     * @return array
     * PROPEL
     * $brands = \SBrandsQuery::create()
     * ->select(['name', 'url', 'id'])
     * ->joinI18n($this->parameters->getLocale())
     * ->withColumn('SBrands.Url', 'url')
     * ->withColumn('SBrands.Id', 'id')
     * ->withColumn('SBrandsI18n.Name', 'name')
     * ->orderBy('SBrandsI18n.Name')
     * ->filterById($ids, Criteria::IN)
     * ->find();
     */
    private function selectBrandsByIds(array $ids) {

        if (count($ids)) {

            $brands = $this->db->select(['shop_brands_i18n.name', 'shop_brands.url', 'shop_brands.id'])
                ->join('shop_brands_i18n', "shop_brands_i18n.id = shop_brands.id and shop_brands_i18n.locale = '{$this->parameters->getLocale()}'")
                ->where_in('shop_brands.id', $ids)
                ->order_by('shop_brands_i18n.name')
                ->get('shop_brands');

            $brands = $brands->num_rows() ? $brands->result() : [];

            $byId = [];
            foreach ($brands as $brand) {
                $byId[$brand->id] = $brand;
            }
            return $byId;
        }

    }

    /**
     *
     * @param array $allBrands
     * @return array
     */
    public function getSelectedInFilterVariants(array $allBrands) {
        if ($this->parameters->hasBrands()) {
            $brandsKeyById = [];
            foreach ($allBrands as $brand) {
                $brandsKeyById[$brand->id] = $brand;
            }
            $getBrands = $this->parameters->getBrands();
            return array_intersect_key($brandsKeyById, array_flip($getBrands));
        }
    }

    /**
     * Fetch products by brands or null if brands is empty
     * @param array $brands
     * @return array|null
     */
    public function fetchProductIds(array $brands) {

        if (count($brands)) {
            $ids = array_shift($brands)->productIds;
            foreach ($brands as $brand) {
                $ids = array_merge($ids, $brand->productIds);
            }
            return $ids;
        }
    }

    /**
     * Recount products in brand
     * @param array $brands
     * @param array $intersectIds product ids for brands intersect
     * @return array
     */
    public function recount(array $brands, array $intersectIds) {

        foreach ($brands as $key => $brand) {
            $brands[$key]->productIds = array_intersect($brand->productIds, $intersectIds);
            $brands[$key]->countProducts = count($brand->productIds);
        }
        return $brands;
    }

}