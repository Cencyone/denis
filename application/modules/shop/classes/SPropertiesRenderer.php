<?php

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;

/**
 * Class to generate properties forms.
 *
 * @package Shop
 * @version $id$
 * @author <dev@imagecms.net>
 */
class SPropertiesRenderer
{

    /**
     * @var string
     */
    public $inputsName = 'productProperties';

    /**
     * @var string
     */
    public $noValueText = '';

    /**
     * @var bool
     */
    public $useMultipleSelect = false;

    /**
     * @var int
     */
    public $productId;

    protected $properties = null;

    /**
     * @var null|SProducts
     */
    protected $productModel = null;

    /**
     * @var array
     */
    protected $propertiesData = [];

    /**
     * @var bool
     */
    protected $mainOnly = TRUE;

    /**
     * @var null|string
     */
    protected $noSelectValue = NULL;

    public function __construct() {

        ShopCore::$ci->load->helper('form');
        $this->noValueText = '- ' . lang('none', 'admin') . ' -';
        $this->noSelectValue = '- ' . lang('Unspecified') . ' -';
    }

    /**
     * Render properties form for admin panel. Used in create/edit products.
     *
     * @param mixed $categoryId Category Id
     * @param null|SProducts $productModel
     * @param string $locale
     * @return string|false
     * @access public
     */
    public function renderAdmin($categoryId, $productModel = null, $locale = 'ru') {

        $categoryModel = SCategoryQuery::create()->setComment(__METHOD__)->findPk((int) $categoryId);
        if ($categoryModel === null) {
            return false;
        }

        $properties = SPropertiesQuery::create()->setComment(__METHOD__)->joinWithI18n($locale)->filterByPropertyCategory($categoryModel)->orderByPosition()->find();

        if (count($properties) == 0) {
            return false;
        }

        if ($productModel instanceof SProducts) {
            $this->setProductModel($productModel);
            $this->loadAdminPropertiesData($locale);
        }

        $resultHtml = '';
        foreach ($properties as $key => $property) {
            $resultHtml .= '
                <div class="control-group" id="edit-properties" >
                    <label class="control-label" for="num_' . ($key) . '"><a href="/admin/components/run/shop/properties/edit/' . $property->getId() . '/' . $locale . '">' . ShopCore::encode($property->getName()) . '</a>:</label>
                    <div class="controls">' . $this->_renderInput($property, $key, $locale) . '
                        <button type="button" data-rel="tooltip" data-close-tooltip="' . lang('Cancel', 'admin') . '" data-add-tooltip="' . lang('Add new property value') . '" data-title="' . lang('Add new property value') . '"  onclick="PropertyFastCreator.showAddForm(this)" class="btn btn-small" style="margin-left: -3px;">
                            <i class="icon-plus"></i>
                        </button>
                    </div>
                    <br>
                     <div style ="margin-bottom: -5px;">
                        <div style="display:none; margin-left: 224px;margin-top: -21px;" class="addPropertyToProduct">
                            <input type="text" style="" onkeypress="PropertyFastCreator.addPropertyValue(event, this)">
                            <button type="button" data-rel="tooltip" data-title="' . lang('Add new property value') . '" onclick="PropertyFastCreator.addPropertyValue(event, this)" class="btn btn-small" style="margin-left: -3px;">
                                <i class="icon-ok" style="margin-right: 0!important;"></i>
                            </button>
                        </div>
                    </div>
                </div>
                ';
        }

        return $resultHtml;
    }

    /**
     * @param SProperties $property
     * @param string $i
     * @param string $locale
     * @return string
     */
    protected function _renderInput(SProperties $property, $i, $locale) {

        $data = $property->asArray($locale);
        $name = $this->inputsName . '[' . $property->getId() . ']';
        $flag = false;
        if (!$data) {
            $flag = true;
        }

        // Render select
        if (count($data) > 0) {
            $data = array_combine($data, $data);
            natsort($data);

            if ($property->getMultiple() === true) {
                $multiple = 'multiple';
                $name .= '[]';
            } else {
                $multiple = null;
            }

            if ($flag) {
                return form_property_select($name, [], $this->_getProductPropertyValue($property->getId()), 'multiple');
            }

            return form_property_select($name, $data, $this->_getProductPropertyValue($property->getId()), $multiple);
        } else {

            if ($property->getMultiple() === true) {
                $multiple = 'multiple';
                $name .= '[]';
                $data = [];
            }

            if ($flag) {
                return form_property_select($name, $data, [], $multiple);
            }

            $i--;
            $inputData = [
                          'name'  => $name,
                          'value' => $this->_getProductPropertyValue($property->getId()),
                          'id'    => 'num_' . $i,
                         ];
            return form_input($inputData);
        }
    }

    /**
     * Сombines array of the properties with identifier as the key and the properties object as a value.<br/>
     * Recommend to use only for the administrative part of the site.
     *
     * @access protected
     * @param string $locale
     * @return array
     * @author DevImageCMS <dev@imagecms.net>
     * @copyright Copyright (c) 2012, DevImageCMS
     */
    protected function loadAdminPropertiesData($locale = 'ru') {

        $this->propertiesData = [];

        $propertiesDatas = SProductPropertiesDataQuery::create()->setComment(__METHOD__)->filterByLocale($locale)->filterByProductId($this->getProductModel()->getId())->find();
        if (count($propertiesDatas)) {
            foreach ($propertiesDatas as $p) {
                $this->propertiesData[$p->getPropertyId()][] = $p;
            }
        }
    }

    /**
     * @return false|null
     */
    protected function _loadProductPropertiesData() {

        $this->propertiesData = null;

        if ($this->getProductModel() === null) {
            return false;
        }

        $propertiesDatas = SPropertiesQuery::create()
            ->filterByShowInCompare(true)
            ->orderByPosition(Criteria::ASC)
            ->useSProductPropertiesDataQuery()
            ->filterByLocale(MY_Controller::getCurrentLocale())
            ->filterByProductId($this->getProductModel()->getId())
            ->endUse()
            ->joinWithI18n()
            ->distinct()
            ->find();

        if (count($propertiesDatas) > 0) {
            foreach ($propertiesDatas as $p) {
                $propertyData = $p->getSProductPropertiesDatas();
                foreach ($propertyData as $k => $v) {
                    if ($v->getProductId() == $this->getProductModel()->getId()) {
                        $this->propertiesData[$propertyData[$k]->getPropertyId()][] = $v;
                    }
                }
            }
        } else {
            $this->propertiesData = [];
        }
    }

    /**
     * @return null|SProducts
     */
    public function getProductModel() {

        return $this->productModel;
    }

    /**
     * @param null|SProducts $productModel
     */
    public function setProductModel($productModel) {

        $this->productModel = $productModel;
    }

    /**
     *
     * @param string $locale
     * @return array
     */
    protected function _loadProductPropertiesDataNew($locale = null) {

        ShopCore::$ci->db->cache_on();
        if ($locale == null) {
            $locale = MY_Controller::getCurrentLocale();
        }
        $this->propertiesData = null;

        $mainCondition = $this->mainOnly == TRUE ? 'shop_product_properties.main_property' : 'shop_product_properties.show_on_site';

        $where = [
                  'shop_product_property_value_i18n.locale'  => $locale,
                  'shop_product_properties_i18n.locale'      => $locale,
                  'shop_product_properties.active'           => 1,
                  $mainCondition                             => 1,
                  'shop_product_properties_data.product_id'  => $this->productId,
                  'shop_product_property_value_i18n.value >' => '',
                 ];

        /** @var CI_DB_result $result */
        $result = ShopCore::$ci->db->select('*')
            ->from('shop_product_properties_data')
            ->join('shop_product_properties', 'shop_product_properties_data.property_id = shop_product_properties.id')
            ->join('shop_product_properties_i18n', 'shop_product_properties_data.property_id = shop_product_properties_i18n.id')
            ->join('shop_product_property_value', 'shop_product_properties_data.value_id = shop_product_property_value.id')
            ->join('shop_product_property_value_i18n', 'shop_product_property_value.id = shop_product_property_value_i18n.id')
            ->where($where)
            ->group_by('shop_product_properties_data.property_id')
            ->order_by('shop_product_properties.position')
            ->get();

        $result = $result->num_rows() < 0 ? $result->result() : [];

        foreach ($result as $key => $value) {

            $query_where = [
                            'shop_product_properties_data.product_id'  => $value->product_id,
                            'shop_product_properties_data.property_id' => $value->property_id,
                           ];

            /** @var CI_DB_result $query */
            $query = ShopCore::$ci->db->select('shop_product_property_value_i18n.value')
                ->from('shop_product_properties_data')
                ->join('shop_product_property_value', 'shop_product_properties_data.value_id = shop_product_property_value.id')
                ->join('shop_product_property_value_i18n', 'shop_product_property_value.id = shop_product_property_value_i18n.id')
                ->where($query_where)
                ->order_by('CAST(shop_product_property_value_i18n.value AS UNSIGNED) , value')
                ->get();

            $result[$key]->values = $query->num_rows() > 0 ? $query->result() : [];
        }
        ShopCore::$ci->db->cache_off();
        return $result;
    }

    /**
     *
     * @param integer $propertyId
     * @return array|null
     */
    protected function _getProductPropertyValue($propertyId) {

        if ($this->propertiesData[$propertyId]) {
            $property = $this->propertiesData[$propertyId];

            if ($this->propertiesData[$propertyId][0]->SProperties->getMultiple()) {
                $data = [];
                foreach ($property as $val) {
                    $data[] = $val->getValue();
                }
                return $data;
            } else {
                return $property[0]->getValue();
            }
        } else {
            $productsProperties = CI::$APP->input->post('productProperties');
            return $productsProperties[$propertyId];
        }

        return null;
    }

    /**
     * Render table containing product properties data.
     *
     * @param int $productId
     * @param bool $mainOnly
     * @return mixed string or null.
     * @access public
     */
    public function renderPropertiesTableNew($productId, $mainOnly = false) {

        $this->mainOnly = $mainOnly;
        $this->productId = $productId;
        $properties = $this->_loadProductPropertiesDataNew();

        if (count($properties) > 0) {
            $table = ShopCore::$ci->load->library('table', TRUE);
            $table->set_template(
                ['table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="characteristic">']
            );
            $returnString = '';
            foreach ($properties as $v) {
                if ($v->value == null) {
                    continue;
                }
                $returnString .= ShopCore::encode($v->name);
                if (empty($v->values)) {
                    $returnString .= ShopCore::encode($v->value);
                } else {
                    $ppSt = '';
                    foreach ($v->values as $key => $value) {
                        $ppSt .= htmlspecialchars_decode($value->value);
                        if (count($v->values) - 1 > $key) {
                            $ppSt .= ', ';
                        }
                    }
                    if ($v->active) {
                        $table->add_row($v->name, $ppSt);
                    }
                    $returnString .= $ppSt;
                }
            }
            return $table->generate();
        }
        return FALSE;
    }

    /**
     * Render table containing product properties data.
     *
     * @param SProducts $product
     * @access public
     * @return mixed string or null.
     */
    public function renderPropertiesTable(SProducts $product) {

        $this->setProductModel($product);
        $this->_loadProductPropertiesData();

        if (count($this->propertiesData) > 0) {
            $table = ShopCore::$ci->load->library('table', TRUE);
            $table->set_template(
                ['table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="characteristic">']
            );
            foreach ($this->propertiesData as $property) {
                // && $property[0]->getSProperties()->getShowOnSite() === TRUE
                if ($property[0]->getSProperties()->getActive() === TRUE) {
                    if ($property[0]->SProperties->getMultiple()) {
                        $data = [];
                        foreach ($property as $val) {
                            $data[] = $val->getValue();
                        }

                        $data = array_reverse($data);
                        $value = implode(', ', $data);
                    } else {
                        $value = $property[0]->getValue();
                    }

                    $table->add_row($property[0]->getSProperties()->getName(), $value);
                }
            }

            return $table->generate();
        }

        return null;
    }

    /**
     *
     * @param SProducts $product
     * @param boolean $mainOnly
     * @param string $glue
     * @return array
     */
    public function renderPropertiesArray(SProducts $product, $mainOnly = false, $glue = ', ') {

        $property = SPropertiesQuery::create()
            ->joinSProductPropertiesData()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->where('SProductPropertiesData.ProductId = ?', $product->getId())
            ->select(['Id', 'Active', 'ShowInCompare', 'ShowInFilter', 'ShowOnSite', 'ShowInFilter', 'MainProperty', 'SPropertiesI18n.Name', 'SPropertiesI18n.Description'])
            ->groupBy('SProductPropertiesData.PropertyId');

        $mainOnly ? $property->where('SProperties.MainProperty = ?', $mainOnly) : $property->where('SProperties.ShowOnSite = ?', 1);

        $property = $property->where('SProperties.Active = ?', 1)
            ->orderByPosition()->find()->toArray();

        $propertiesData = SProductPropertiesDataQuery::create()
            ->useSPropertyValueQuery()
                ->joinWithI18n(MY_Controller::getCurrentLocale())
                ->orderByPosition()
            ->endUse()
            ->withColumn('SPropertyValueI18n.value', 'Value')
            ->filterByProductId($product->getId())
            ->select(['PropertyId', 'Value'])
            ->find()
            ->toArray();
        $propertiesDataIds = [];
        foreach ($propertiesData as $propertyD) {
            $propertiesDataIds[$propertyD['PropertyId']][] = $propertyD['Value'];
        }

        $arr_res = [];
        foreach ($property as $prop) {
            if ($prop['Active']) {
                $arr_aux = $prop;

                $arr_aux_value = $propertiesDataIds[$prop['Id']];

                $arr_aux['Value'] = implode($glue, $arr_aux_value);

                /** Если свойство не пустое, передается в шаблон */
                if ($arr_aux['Value'] != null) {
                    $arr_aux['Name'] = $prop['SPropertiesI18n.Name'];
                    $arr_aux['Desc'] = $prop['SPropertiesI18n.Description'];
                    unset($arr_aux['SPropertiesI18n.Name']);
                    unset($arr_aux['SPropertiesI18n.Description']);

                    $arr_res[] = $arr_aux;
                }

            }
        }
        return $arr_res;
    }

    /**
     *
     * @param integer $productId id of product
     * @param boolean $mainOnly (optional, default TRUE) if TRUE, then returned will be only properties with main_property = 1 in DB
     * @return string|false
     */
    public function renderPropertiesInlineNew($productId, $mainOnly = TRUE) {

        $this->productId = $productId;
        $this->mainOnly = $mainOnly;
        $properties = $this->_loadProductPropertiesDataNew();

        if (count($properties) > 0) {
            $returnString = '';
            foreach ($properties as $k => $v) {
                if ($v->value == null) {
                    continue;
                }
                $returnString .= '<b>' . ShopCore::encode($v->name) . '</b>: ';
                if (empty($v->values)) {
                    $returnString .= ShopCore::encode($v->value);
                } else {
                    $ppSt = '';
                    foreach ($v->values as $key => $value) {
                        $ppSt .= htmlspecialchars_decode($value->value);
                        if (count($v->values) - 1 > $key) {
                            $ppSt .= ', ';
                        } elseif (count($properties) - 1 > $k) {
                            $ppSt .= ' / ';
                        }
                    }
                    $returnString .= $ppSt;
                }
            }
            return $returnString;
        }
        return FALSE;
    }

    /**
     * @param int $productId
     * @param bool|TRUE $mainOnly
     * @return array|false
     */
    public function renderPropertiesNewArray($productId, $mainOnly = TRUE) {

        $this->productId = $productId;
        $this->mainOnly = $mainOnly;
        $properties = $this->_loadProductPropertiesDataNew();
        $array = [];
        if (count($properties) > 0) {
            $returnString = '';
            foreach ($properties as $v) {
                if (!$v->value) {
                    continue;
                }

                if (empty($v->values)) {
                    $returnString .= ShopCore::encode($v->value);
                } else {
                    foreach ($v->values as $value) {
                        $name = trim($v->name);
                        if (count($v->values) > 1) {
                            $array[$name] = '';
                            foreach ($v->values as $value) {
                                $array[$name] .= ' ' . $value->value;
                            }
                        } else {
                            $array[$name] = $value->value;
                        }
                    }
                }
            }
            return $array;
        }
        return FALSE;
    }

    /**
     * @param int $categoryId
     * @return array|bool
     */
    public function renderCategoryPropertiesArrayNew($categoryId) {

        $categoryModel = SCategoryQuery::create()
            ->findPk((int) $categoryId);

        if ($categoryModel === null) {
            return false;
        }
        $properties = SPropertiesQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale(), Criteria::LEFT_JOIN)
            ->filterByActive(true)
            ->filterByShowInCompare(true)
            ->filterByPropertyCategory($categoryModel)
            ->orderByPosition()
            ->find()
            ->toArray();

        $props = [];
        foreach ($properties as $property) {
            $props[$property['Id']] = $property['SPropertiesI18ns']['SPropertiesI18n_0']['Name'];
        }

        if (count($properties) == 0) {
            return false;
        }

        return $props;
    }

    /**
     * @param SProducts $product
     * @return array|bool
     */
    public function renderPropertiesArrayProd(SProducts $product) {

        $result = [];
        $this->setProductModel($product);
        $this->_loadProductPropertiesDataCompare();

        if (count($this->propertiesData) > 0) {
            foreach ($this->propertiesData as $property) {
                $name = ShopCore::encode($property[0]->getSProperties()->getCsvName());
                $result[$name]['title'] = ShopCore::encode($property[0]->getSProperties()->getName());
                $result[$name]['value'] = ShopCore::encode($property[0]->getValue());
            }
            return $result;
        }
        return FALSE;
    }

    /**
     * @param SProducts $product
     * @return array
     */
    public function renderPropertiesArrayProd1(SProducts $product) {

        $this->setProductModel($product);
        $this->_loadProductPropertiesDataCompare();

        if (count($this->propertiesData) > 0) {

            return $this->propertiesData;
        }

        return [];
    }

    /**
     * @param int $categoryId
     * @return array|bool
     * @throws PropelException
     */
    public function renderCategoryPropertiesArray($categoryId) {

        $categoryModel = SCategoryQuery::create()
            ->findPk((int) $categoryId);

        if ($categoryModel === null) {
            return false;
        }
        $properties = SPropertiesQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale(), Criteria::LEFT_JOIN)
            ->filterByActive(true)
            ->filterByShowInCompare(true)
            ->filterByPropertyCategory($categoryModel)
            ->select('SPropertiesI18n.Name')
            ->orderByPosition()
            ->find()
            ->toArray();

        if (count($properties) == 0) {
            return false;
        }

        return $properties;
    }

    /**
     *
     * @param SProducts $product
     * @return array
     */
    public function renderPropertiesCompareArray(SProducts $product) {

        $result = [];
        $this->setProductModel($product);
        $this->_loadProductPropertiesDataCompare();

        if (count($this->propertiesData) > 0) {
            /** @var SProductPropertiesData[] $property */
            foreach ($this->propertiesData as $key => $property) {

                if (count($property) > 1) {
                    foreach ($property as $k => $p) {
                        $result[ShopCore::encode($p->getSProperties()->getName())][$k] = htmlspecialchars_decode(ShopCore::encode($p->getSPropertyValue()->getValue()));
                    }
                } else {
                    if ($property[0]->getSProperties()->getShowInCompare() === TRUE) {

                        $result[ShopCore::encode($property[0]->getSProperties()->getName())] = htmlspecialchars_decode(ShopCore::encode($property[0]->getSPropertyValue()->getValue()));
                    }
                }
            }

            return $result;
        }
        return [];
    }

    /**
     * @return bool
     */
    protected function _loadProductPropertiesDataCompare() {

        // Clear current properties
        $this->propertiesData = null;

        if ($this->getProductModel() === null) {
            return false;
        }

        /** @var ObjectCollection $properties */
        $properties = SPropertiesQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->filterByShowInCompare(true)
               ->useSProductPropertiesDataQuery()
                  ->filterByProductId($this->getProductModel()->getId())
               ->endUse()
            ->distinct()
            ->orderByPosition()
            ->find();

        if ($properties->count() > 0) {
            /** @var SProperties $p */
            foreach ($properties as $p) {
                $propertyData = $p->getSProductPropertiesDatas(
                    SProductPropertiesDataQuery::create()
                        ->joinWithSPropertyValue()
                        ->useSPropertyValueQuery()
                            ->joinWithI18n(MY_Controller::getCurrentLocale())
                            ->orderByPosition()
                        ->endUse()
                    ->filterByProductId($this->getProductModel()->getId())
                );

                /** @var SProductPropertiesData $v */
                foreach ($propertyData as $k => $v) {
                        $this->propertiesData[$propertyData[$k]->getPropertyId()][] = $v;
                }
            }
        } else {
            $this->propertiesData = [];
        }

    }

    /**
     * Returns array of properties of variant
     * @param integer $vId
     * @param array $params
     * @return array|bool
     */
    public function getVariantProperties($vId, array $params = []) {

        $ci = &get_instance();
        $result = $ci->db
            ->select('product_id')
            ->get_where(
                'shop_product_variants',
                ['id' => $vId]
            )
            ->row_array();
        return $this->getProductProperties($result['product_id'], $params);
    }

    /**
     * Returns array of properties of product
     * @param integer $pId id of product
     * @param array $params (optipnal) additional conditions
     * possible values:
     *  - active: boolean (default empty)
     *  - show_on_site: boolean (default empty)
     *  - main_property: boolean (default empty)
     *
     * @return boolean|array
     */
    public function getProductProperties($pId, array $params = []) {

        if (!is_numeric($pId)) {
            return false;
        }

        // reading params
        $possibleParams = [
                           'active',
                           'show_on_site',
                           'main_property',
                          ];
        $condition = '';
        foreach ($params as $key => $value) {
            if (in_array($key, $possibleParams)) {
                $value = $value == TRUE ? 1 : 0;
                $condition .= " AND `{$key}` = {$value} ";
            }
        }

        $locale = MY_Controller::getCurrentLocale();
        $query = "
            SELECT
                `p18`.`name`,
                GROUP_CONCAT(`pd`.`value` SEPARATOR ', ') as `values`
            FROM
                `shop_product_properties_data` `pd`
            LEFT JOIN `shop_product_properties` `p`
                ON `p`.`id` = `pd`.`property_id`
            LEFT JOIN `shop_product_properties_i18n` `p18`
                ON `p18`.`id` = `p`.`id` AND `p18`.`locale` = '{$locale}'
            WHERE 1 AND `pd`.`product_id` = {$pId} {$condition}
            GROUP BY `p18`.`name`
            ";
        $ci = &get_instance();
        return $ci->db->query($query)->result_array();
    }

}