<?php
use Currency\Currency;
use Map\SProductVariantPriceTableMap;
use Map\SProductVariantPriceTypeTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use VariantPriceType\BaseVariantPriceType;

/**
 * @property Lib_admin lib_admin
 */
class ShopAdminVariantprice extends ShopAdminController
{

    /**
     * @var array
     */
    private $all_rbac;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function index() {

        $this->setAllRbac(true);

        $variantPriceTypeData = SProductVariantPriceTypeQuery::create()
            ->setComment(__METHOD__)
            ->joinWithCurrency(Criteria::LEFT_JOIN)
            ->joinWithSProductVariantPriceTypeValue(Criteria::LEFT_JOIN)
            ->orderByPosition()
            ->find();

        /** @var SProductVariantPriceType $item */
        foreach ($variantPriceTypeData as $item) {

            $item->setVirtualColumn(
                'usedRbac',
                $this->getSelectedCatsString($item->getSProductVariantPriceTypeValues()->toArray())
            );
        }

        $this->render(
            'list',
            [
             'content'         => $variantPriceTypeData,
             'defaultCurrency' => Currency::create()->getMainCurrency()->getCode(),
            ]
        );
    }

    /**
     * @param array $priceTypeValue
     * @return string
     */
    private function getSelectedCatsString(array $priceTypeValue) {

        $all_rbac = $this->getAllRbac();
        $rbac_str = [];

        foreach ($priceTypeValue as $item) {

            foreach ($all_rbac as $rbac) {

                if ($item['Value'] == (int) $rbac['id']) {

                    $rbac_str[] = $rbac['name'];

                }
            }
        }

        return implode(', ', $rbac_str);
    }

    /**
     * @throws PropelException
     * @return void
     */
    public function create() {

        if ($this->input->post()) {
            $this->load->library('form_validation');

            $model = new SProductVariantPriceType();

            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run() == false) {

                showMessage(validation_errors(), '', 'r');
                return;

            } else {

                $model->addToModel($this->input->post());

                showMessage(lang('Price type was created', 'admin'));
                $this->lib_admin->log(lang('The price type was created', 'admin') . '. Id: ' . $model->getId());

                if ($this->input->post('action') == 'close') {
                    pjax('/admin/components/run/shop/variantprice');
                } else {
                    pjax('/admin/components/run/shop/variantprice/edit/' . $model->getId());
                }
            }
        } else {

            $this->setAllRbac(true);

            $currency = SCurrenciesQuery::create()
                ->find();

            $data = [
                     'currencies'   => $currency,
                     'rbac_roles'   => $this->getNotUsedRbac(),
                     'mainCurrency' => Currency::create()->getMainCurrency(),
                    ];
            $this->render('create', $data);
        }
    }

    /**
     * @param int $id
     * @return bool|void
     * @throws PropelException
     */
    public function edit($id) {
        $id = (int) $id;

        /** Преверка на инт  */
        if ($id == 0 || !$id) {
            $this->error404(lang('Page not found', 'admin'));
            return;
        }

        $model = SProductVariantPriceTypeQuery::create()
            ->joinWithCurrency(Criteria::LEFT_JOIN)
            ->findOneById($id);

        if (!$model) {

            $this->error404(lang('Page not found', 'admin'));
            return;
        }

        if ($this->input->post()) {

            $this->form_validation->set_rules($model->rules('edit'));

            if ($this->form_validation->run() == false) {

                showMessage(validation_errors(), '', 'r');
                return;

            } else {

                $model->addToModel($this->input->post());

                showMessage(lang('Changes were saved', 'admin'));
                $this->lib_admin->log(lang('The price type was update', 'admin') . '. Id: ' . $model->getId());

                if ($this->input->post('action') == 'edit') {
                    pjax('/admin/components/run/shop/variantprice/edit/' . $id);
                } else {
                    pjax('/admin/components/run/shop/variantprice');
                }
            }
        } else {

            $this->setAllRbac(true);

            $currency = SCurrenciesQuery::create()
                ->find();

            $data = [
                     'model'           => $model,
                     'currencies'      => $currency,
                     'selected_values' => $model->getSProductVariantPriceTypeValues(),
                     'rbac_roles'      => $this->getSelectedRbac($this->getNotUsedRbac($id), $model->getSProductVariantPriceTypeValues()),

                    ];
            $this->render('edit', $data);
        }
    }

    /**
     * Исключает выбраные роли пользователей
     * @param null|int $price_type
     * @return array
     */
    private function getNotUsedRbac($price_type = null) {

        /** @var CI_DB_result $query */
        $query = $this->db->select(['value', 'price_type_id'])
            ->from('shop_product_variants_price_type_values')
            ->get();

        $roles = $query->num_rows() > 0 ? $query->result_array() : [];

        $availableRole = $this->getAllRbac();

        foreach ($roles as $role) {

            foreach ($availableRole as $key => $item) {

                if ($role['value'] == $item['id'] && $price_type != $role['price_type_id']) {
                    unset($availableRole[$key]);
                }

            }

        }

        return $availableRole;
    }

    /**
     * @return boolean|void
     */
    public function delete() {
        $ids = $this->input->post('ids');

        if (!$ids) {
            return false;
        }

        SProductVariantPriceTypeQuery::create()
            ->findPks($ids)
            ->delete();

        $this->lib_admin->log(lang('The price type was delete', 'admin'));

        /** clear Doctrine cache */
        $this->getCache()->flushAll();
        $this->getCache()->deleteAll();

        showMessage(lang('Removing price types was successful', 'admin'));
    }

    /**
     * @param int $id
     * @return bool|void
     */
    public function changeStatus($id) {

        if (!$id) {
            return false;
        }

        $model = SProductVariantPriceTypeQuery::create()
            ->findOneById($id);

        $model->setStatus($model->getStatus() == 0 ? 1 : 0);
        $model->save();

        $this->lib_admin->log(lang('The price type was change status', 'admin') . '. Id: ' . $model->getId());

    }

    /**
     * @return void|boolean|Json
     */
    public function attribute() {

        $id = $this->input->post('id');

        if (!$id) {
            return false;
        }

        $data = [];

        $variants = SProductVariantPriceQuery::create()
            ->withColumn(SProductVariantPriceTableMap::COL_PRICE, 'price')
            ->withColumn(SProductVariantPriceTableMap::COL_TYPE_ID, 'type_id')
            ->withColumn(SProductVariantPriceTypeTableMap::COL_PRICE_TYPE, 'price_type')
            ->select(['price', 'type_id', 'price_type'])
                ->joinWithSProductVariantPriceType()
            ->findByVarId($id);

        /** @var SProductVariantPrice $variant */
        foreach ($variants as $variant) {

            $data[] = [
                       'type'  => $variant['type_id'],
                       'price' => $variant['price_type'] != 1 ? (int) $variant['price'] : $variant['price'],//Если тип цены процент, возвращаем int
                      ];
        }

        echo json_encode($data);
    }

    /**
     * @throws PropelException
     */
    public function saveVariantsPrice() {

        $post = $this->input->post();

        SProductVariantPriceQuery::create()
            ->findByVarId($post['id_variant'])
            ->delete();

        $variant = SProductVariantsQuery::create()
            ->findPk($post['id_variant']);

        $prices = array_diff($post['prices'], ['']);

        $price_types = $this->getPriceTypesData(array_keys($prices));

        foreach ($prices as $type_id => $price) {
            $model = new SProductVariantPrice();

            if ($price_types[$type_id]['price_type'] == BaseVariantPriceType::PRICE_TYPE_PERCENT) {

                $final = Currency::create()
                    ->toMain(BaseVariantPriceType::getPercent((string) $price, $variant->getPriceInMain()), $variant->getSCurrencies()->getId());
            }

            $model->addToModel(
                [
                 'var_id'  => $post['id_variant'],
                 'type_id' => $type_id,
                 'price'   => $price,
                 'prod_id' => $post['id_product'],
                  // конвертирует в стринг так как float не пишет в таблицу как double  а рубает по запятой
                 'final'   => (string) str_replace(',', '.', $final) ?: Currency::create()->toMain((string) $price, $price_types[$type_id]['currency']),
                ]
            );

        }

        echo json_encode(['success' => lang('Price variants saved', 'admin')]);
    }

    /**
     * @param array $arrayKeys
     * @return array
     * @throws PropelException
     */
    private function getPriceTypesData(array $arrayKeys) {

        $priceType  = SProductVariantPriceTypeQuery::create()
            ->withColumn(SProductVariantPriceTypeTableMap::COL_ID, 'id')
            ->withColumn(SProductVariantPriceTypeTableMap::COL_PRICE_TYPE, 'price_type')
            ->withColumn(SProductVariantPriceTypeTableMap::COL_CURRENCY_ID, 'currency')
            ->select(['id', 'price_type', 'currency'])
            ->findPks($arrayKeys)->toArray();

        $data = [];

        foreach ($priceType as $item) {
            $data[$item['id']] = [
                                  'price_type' => $item['price_type'],
                                  'currency'   => $item['currency'],
                                 ];
        }

        return $data;
    }

    /**
     * @param array $roles
     * @param ObjectCollection $collection_selected
     * @return array
     */
    private function getSelectedRbac(array $roles, ObjectCollection $collection_selected) {

        foreach ($roles as $key => $role) {

            /** @var SProductVariantPriceTypeValue $selected */
            foreach ($collection_selected as $selected) {

                if ($role['id'] == $selected->getValue()) {
                    $roles[$key]['selected'] = true;

                }
            }

        }
        return $roles;
    }

    /**
     * @return bool|void
     * @throws PropelException
     */
    public function save_positions() {

        $positions = $this->input->post('positions');

        if (count($positions) == 0) {
            return false;
        }

        foreach ($positions as $key => $val) {

            $variant_price = SProductVariantPriceTypeQuery::create()
                ->findPk($val);

            if ($variant_price) {
                $variant_price->setPosition($key);
                $variant_price->save();
            }

        }
        showMessage(lang('Positions saved', 'admin'));

    }

    /**
     * @return array
     */
    private function getAllRbac() {

        return $this->all_rbac;
    }

    /**
     * @param bool $default
     */
    private function setAllRbac($default = false) {

        $locale = MY_Controller::getCurrentLocale();

        /** @var CI_DB_result $user_rbac_role */
        $user_rbac_role = $this->db->select('shop_rbac_roles.id, shop_rbac_roles_i18n.alt_name AS name')
            ->from('shop_rbac_roles')
            ->join('shop_rbac_roles_i18n', 'shop_rbac_roles_i18n.id = shop_rbac_roles.id')
            ->where('shop_rbac_roles_i18n.locale', $locale)->get();

        $user_rbac_role = $user_rbac_role->num_rows() > 0 ? $user_rbac_role->result_array() : [];

        if ($default == true) {
            array_unshift($user_rbac_role, ['id' => '0', 'name' => lang('Without role', 'admin')]);
            array_unshift($user_rbac_role, ['id' => '-1', 'name' => lang('Not authorized', 'admin')]);
        }

        $this->all_rbac = $user_rbac_role;
    }

}