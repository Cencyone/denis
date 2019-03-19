<?php namespace smart_filter\src\Filter\Filters;

use Exception;
use Propel\Runtime\Exception\PropelException;

/**
 *
 * Class PropertiesFilter
 * @package smart_filter\classes
 */
class PropertiesFilter extends AbstractEntityFilter
{

    /**
     * @return array|null
     */
    public function getValues() {
        $products = $this->selectProductsWithPropertyValueByCategory();

        if (is_array($products)) {

            $properties = $this->selectPropertiesById(array_keys($products));
            $values = $this->selectPropertyValuesByPropertyId(array_keys($products));

            $data = $this->combineData($products, $properties, $values);
            return $data;
        }

    }

    /**
     * @param array $products
     * @param array $properties
     * @param array $values
     * @return array
     */
    private function combineData(array $products, array $properties, $values) {

        $data = [];
        foreach ($properties as $propertyId => $propertyData) {
            if (count($values[$propertyId])) {
                $data[$propertyId] = $propertyData;
                foreach ($values[$propertyId] as $value) {
                    $valueProducts = $products[$propertyId][$value['id']];
                    $countProducts = count($valueProducts);
                    if ($countProducts > 0) {
                        $data[$propertyId]->possibleValues[$value['id']] = $value;
                        $data[$propertyId]->possibleValues[$value['id']]['count'] = count($valueProducts);
                        $data[$propertyId]->possibleValues[$value['id']]['productIds'] = $valueProducts;
                    }

                }
            }

        }

        return $data;

    }

    /**
     * @return array
     * @throws Exception
     */
    private function selectProductsWithPropertyValueByCategory() {

        $products = $this->db
            ->select('shop_products.id, shop_product_properties_data.property_id,  shop_product_properties_data.value_id')
            ->join('shop_products_i18n', "shop_products.id = shop_products_i18n.id and shop_products_i18n.locale = '{$this->parameters->getLocale()}'", 'inner')
            ->join('shop_product_categories', 'shop_products.id = shop_product_categories.product_id', 'inner')
            ->join('shop_product_properties_data', 'shop_products.id = shop_product_properties_data.product_id', 'inner')
            ->join('shop_product_properties', 'shop_product_properties.id = shop_product_properties_data.property_id and shop_product_properties.show_in_filter = 1', 'inner')
            ->join('shop_product_properties_categories', "shop_product_properties_categories.property_id = shop_product_properties_data.property_id and shop_product_properties_categories.category_id = {$this->parameters->getCategoryId()}", 'inner')
            ->where('shop_product_categories.category_id', $this->parameters->getCategoryId())
            ->where('shop_products.active', 1)
            ->where('shop_products.archive', 0)
            ->where('shop_product_properties.active', 1)
            ->get('shop_products');

        if ($this->db->_error_message()) {
            throw new Exception($this->db->_error_message());
        }

        $products = $products->num_rows() ? $products->result_array() : null;

        if ($products) {
            $byProperty = [];

            foreach ($products as $product) {
                $byProperty[$product['property_id']][$product['value_id']][] = $product['id'];
            }
            return $byProperty;
        }

    }

    /**
     * @param array $ids
     * @return array
     */
    private function selectPropertyValuesByPropertyId(array $ids) {

        $values = $this->db
            ->select('shop_product_property_value_i18n.value, shop_product_property_value.property_id, shop_product_property_value.id')
            ->join('shop_product_property_value_i18n', "shop_product_property_value_i18n.id = shop_product_property_value.id and locale = '{$this->parameters->getLocale()}'")
            ->where_in('shop_product_property_value.property_id', $ids)
            ->order_by('position')//or if no position CAST(shop_product_property_value_i18n.value as SIGNED INTEGER)
            ->get('shop_product_property_value');
        $values = $values->num_rows() ? $values->result_array() : null;

        if ($values) {
            $byPropertyId = [];
            foreach ($values as $value) {
                $byPropertyId[$value['property_id']][] = $value;
            }
            return $byPropertyId;
        }
    }

    /**
     * @param array $ids
     * @return array
     * @throws PropelException
     */
    private function selectPropertiesById(array $ids) {

        $properties = $this->db->select('shop_product_properties.id as property_id, name, csv_name, description')
            ->join('shop_product_properties_i18n', "shop_product_properties.id = shop_product_properties_i18n.id and shop_product_properties_i18n.locale = '{$this->parameters->getLocale()}'")
            ->where_in('shop_product_properties.id', $ids)
            ->order_by('shop_product_properties.position')
            ->order_by('shop_product_properties.id')
            ->get('shop_product_properties');

        $properties = $properties->num_rows() ? $properties->result() : [];

        $byKey = [];
        foreach ($properties as $property) {
            $byKey[$property->property_id] = $property;
        }

        return $byKey;

    }

    /**
     * @param array $allProperties
     * @return array|null
     */
    public function getSelectedInFilterVariants(array $allProperties) {

        $useIds = false;

        // GET[p] old variant values
        if ($this->parameters->hasProperties()) {
            $getProperties = $this->parameters->getProperties();
        }

        // GET[pv] new variant value ids
        if ($this->parameters->hasPropertyValueIds()) {
            $getProperties = $this->parameters->getPropertyValueIds();
            $useIds = true;
        }

        if (isset($getProperties)) {

            $selectedProperties = array_intersect_key($allProperties, $getProperties);

            foreach ($selectedProperties as $property) {

                $property->possibleProducts = [];
                $property->selectedValues = [];
                foreach ($property->possibleValues as $value) {

                    $selected = false;
                    // GET[p] old variant values
                    if (!$useIds && in_array($value['value'], $getProperties[$property->property_id], true)) {
                        $value['value'] = htmlspecialchars_decode($value['value']);
                        $selected = true;
                    }
                    // GET[pv] new variant value ids
                    if ($useIds && in_array($value['id'], $getProperties[$property->property_id])) {
                        $selected = true;
                    }
                    if ($selected) {
                        $property->selectedValues[] = $value;
                        $valueProducts = $value['productIds'];
                        if (is_array($valueProducts)) {
                            $property->possibleProducts = array_merge($property->possibleProducts, $valueProducts);

                        }

                    }

                }
            }

            return $selectedProperties;
        }
    }

    /**
     * @param array $properties
     * @return array
     */
    public function fetchProductIds(array $properties) {
        if (count($properties)) {

            $ids = array_shift($properties)->possibleProducts;

            foreach ($properties as $property) {
                $ids = array_intersect($ids, $property->possibleProducts);
            }
            return $ids;
        }
    }

    /**
     * @param array $properties
     * @param array $selectedInFilter
     * @param array $intersectProducts
     * @return array
     */
    public function recount(array $properties, $selectedInFilter, $intersectProducts) {
        foreach ($properties as $propertyId => $property) {

            //intersect with other properties
            if (is_array($selectedInFilter)) {
                foreach ($selectedInFilter as $selectedPropertyId => $selectedProperty) {
                    if ($selectedPropertyId !== $propertyId) {
                        $properties[$propertyId]->possibleValues = $this->combinePropertyValues($property->possibleValues, $selectedProperty->possibleProducts);
                    }
                }
            }
            //intersect with products by brand and price
            if (is_array($intersectProducts) && is_array($property->possibleValues)) {
                $properties[$propertyId]->possibleValues = $this->combinePropertyValues($property->possibleValues, $intersectProducts);
            }
        }

        return $properties;
    }

    /**
     * @param array $possibleValues
     * @param array $intersectIds
     * @return array
     */
    private function combinePropertyValues(array $possibleValues, array $intersectIds) {

        foreach (array_keys($possibleValues) as $key) {
            $productIds = array_intersect($possibleValues[$key]['productIds'], $intersectIds);
            $possibleValues[$key]['productIds'] = $productIds;
            $possibleValues[$key]['count'] = count($productIds);

        }

        return $possibleValues;
    }

}