<?php
use Propel\Runtime\Exception\PropelException;

/**
 * @property Lib_admin lib_admin
 */
class ShopAdminCustomfields extends ShopAdminController
{

    /**
     * @var array|null
     */
    public $defaultLanguage = null;

    /**
     * @var array
     */
    public $entities;

    /**
     * ShopAdminCustomfields constructor.
     */
    public function __construct() {

        parent::__construct();

        ShopController::checkVar();
        ShopAdminController::checkVarAdmin();

        $this->defaultLanguage = MY_Controller::getDefaultLanguage();
    }

    /**
     * @return array
     */
    public function getEntities() {

        return [
                'category' => '<i class="fa fa-cart-plus fa-lg"></i> ' . lang('Product category', 'admin'),
                'brand'    => '<i class="fa fa-barcode fa-lg"></i> ' . lang('Product brand', 'admin'),
                'user'     => '<i class="icon-user"></i> ' . lang('One user', 'admin'),
                'order'    => '<i class="icon-shopping-cart"></i> ' . lang('Order', 'admin'),
                'product'  => '<i class="icon-shopping-cart"></i> ' . lang('Product', 'admin'),
               ];
    }

    /**
     * Show list of avaliable custom fields
     * @param int $offset
     * @param string $orderField
     * @param string $orderCriteria
     */
    public function index($offset = 0, $orderField = '', $orderCriteria = '') {

        $customFields = CustomFieldsQuery::create()
            ->setComment(__METHOD__)
            ->joinWithI18n(MY_Controller::defaultLocale())
            ->orderByposition()
            ->find();

        $customFieldsCount = $customFields->count();

        $this->render(
            'list',
            [
             'entities'          => $this->getEntities(),
             'customFields'      => $customFields,
             'customFieldsCount' => $customFieldsCount,
             'orderField'        => $orderField,
             'locale'            => $this->defaultLanguage['identif'],
            ]
        );
    }

    /**
     * Create custom field
     * @throws PropelException
     */
    public function create() {

        if ($this->input->post()) {
            $model = new CustomFields();
            $model->setLocale(MY_Controller::defaultLocale());
            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run() == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                if ($this->input->post('Entity') == 'user_order') {
                    $_POST['Entity'] = 'user';
                    $this->saveModels($model);
                    $model = new CustomFields();
                    $_POST['Entity'] = 'order';
                    $this->saveModels($model);
                } else {
                    $this->saveModels($model);
                }
                $this->lib_admin->log(lang('An additional field is created', 'admin') . '. ID: ' . $model->getId());
                showMessage(lang('An additional field is created', 'admin'));

                if ($this->input->post('action') == 'exit') {
                    pjax('/admin/components/run/shop/customfields');
                } else {
                    pjax('/admin/components/run/shop/customfields/edit/' . $model->getId());
                }

            }
        } else {
            $this->render('create');
        }
    }

    /**
     * @param CustomFields $model
     * @param bool $create
     * @throws PropelException
     */
    public function saveModels(CustomFields $model, $create = true) {

        $model->fromArray($this->input->post());

        $model->setIsRequired($this->input->post('is_required'));
        $model->setIsPrivate($this->input->post('is_private'));
        $model->setIsActive($this->input->post('is_active'));

        if ($this->input->post('multiple_select') == 'on') {
            $model->setOptions('multiple');
        }

        if ($this->input->post('validators') !== false) {
            $model->setValidators($this->input->post('validators'));
        }

        if ($create) {
            $model->setFieldLabel($this->input->post('fLabel'));
            $model->setFieldDescription($this->input->post('description'));
            if ($this->input->post('possible_values')) {
                $values = explode(',', $this->input->post('possible_values'));
                $count = count($values);
                for ($i = 0; $i < $count; $i++) {
                    $values[$i] = trim($values[$i]);
                }
                $model->setPossibleValues(serialize($values));
            }
        }

        $model->save();
    }

    /**
     * Edit custom field
     * @param integer $customFieldId Id of custom field to edit
     * @param bool|string $locale
     */
    public function edit($customFieldId = null, $locale = FALSE) {

        $locale = $locale ?: MY_Controller::defaultLocale();

        $model = CustomFieldsQuery::create()->setComment(__METHOD__)->findPk((int) $customFieldId);

        if ($model === null) {
            $this->error404(lang('Field not found', 'admin'));
        }

        $name = $model->getname();
        if ($this->input->post()) {
            if ($model->getname() != $this->input->post('name')) {
                $this->form_validation->set_rules($model->rules());
            } else {
                $this->form_validation->set_rules('fLabel', 'Label', 'required');
            }

            if ($this->form_validation->run() == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {

                $this->saveModels($model, false);

                $model_locale = CustomFieldsI18nQuery::create()->setComment(__METHOD__)->filterByLocale($locale)->filterById($model->getId())->findOne();

                $this->update_i18n($model_locale, $model, $locale);

                if ($model->getEntity() == 'user' or $model->getEntity() == 'order') {
                    $model_connect = CustomFieldsQuery::create()->setComment(__METHOD__)->filterByname($name);
                    if ($model->getEntity() == 'user') {
                        $model_connect = $model_connect->filterByEntity('order');
                    } else {
                        $model_connect = $model_connect->filterByEntity('user');
                    }

                    $model_connect = $model_connect->findOne();
                    if ($model_connect) {
                        $model_connect->setname($model->getname());
                        $model_connect->save();

                        $model_locale_connect = CustomFieldsI18nQuery::create()
                            ->setComment(__METHOD__)
                            ->filterByLocale($locale)
                            ->filterById($model_connect->getId())
                            ->findOne();

                        $this->update_i18n($model_locale_connect, $model_connect, $locale);
                    }
                }

                $this->lib_admin->log(lang('Custom field edited', 'admin') . '. ID: ' . $customFieldId);
                showMessage(lang('Changes have been saved', 'admin'));

                $action = $this->input->post('action');

                if ($action == 'edit') {
                    pjax("/admin/components/run/shop/customfields/edit/$customFieldId/$locale");
                } else {
                    pjax('/admin/components/run/shop/customfields');
                }
            }
        } else {
            $this->render(
                'edit',
                [
                 'locale'    => $locale,
                 'entities'  => $this->getEntities(),
                 'languages' => $this->db->get('languages')->result_array(),
                 'model'     => $model,
                ]
            );
        }
    }

    /**
     * @param CustomFieldsI18n|null $model_locale
     * @param CustomFields $model_withid
     * @param string $locale
     */
    public function update_i18n($model_locale, CustomFields $model_withid, $locale) {

        if ($this->input->post('possible_values')) {
            $values = explode(',', $this->input->post('possible_values'));
            $count = count($values);
            for ($i = 0; $i < $count; $i++) {
                $values[$i] = trim($values[$i]);
            }
        }

        if ($model_locale) {
            $model_locale->setFieldDescription($this->input->post('description'));
            $model_locale->setFieldLabel($this->input->post('fLabel'));
            $model_locale->setPossibleValues(serialize($values));
            $model_locale->save();
        } else {
            $model_locale = new CustomFieldsI18n();
            $model_locale->setFieldLabel($this->input->post('fLabel'));
            $model_locale->setFieldDescription($this->input->post('description'));
            $model_locale->setPossibleValues(serialize($values));
            $model_locale->setId($model_withid->getId());
            $model_locale->setLocale($locale);
            $model_locale->save();
        }
    }

    /**
     * delete set of custom fields
     */
    public function deleteAll() {

        if (!$this->input->post('ids')) {
            showMessage(lang('No data transmitted', 'admin'), '', 'r');
            exit;
        }

        $ids = $this->input->post('ids');
        if (count($ids) > 0) {
            $model = CustomFieldsQuery::create()->setComment(__METHOD__)->findPks($this->input->post('ids'));

            if (!empty($model)) {
                foreach ($model as $order) {
                    $order->delete();
                }

                $this->lib_admin->log(lang('Field is removed', 'admin'));
                showMessage(lang('Field is removed', 'admin'));
            }
        }
    }

    /**
     * @param int $id
     */
    public function change_status_activ($id) {

        $this->change($id, 'Active');
    }

    /**
     * @param int $id
     * @param string $type
     */
    private function change($id, $type) {

        try {
            $model = CustomFieldsQuery::create()->findPk($id);
            $model->{"setIs$type"}(!$model->{"getIs$type"}());
            $model->save();
            showMessage(lang('Saved', 'admin'));
        } catch (PropelException $e) {
            showMessage($e->getPrevious()->getMessage(), false, 'r');
        } catch (Exception $e) {
            showMessage($e->getMessage(), false, 'r');
        }
    }

    /**
     * @param int $id
     */
    public function change_status_private($id) {

        $this->change($id, 'Private');
    }

    /**
     * @param int $id
     */
    public function change_status_required($id) {

        $this->change($id, 'Required');
    }

    /**
     * @return bool
     */
    public function save_positions() {

        if (!$this->input->post('positions')) {
            return false;
        }
        $updates = [];
        foreach ((array) $this->input->post('positions') as $key => $id) {
            $updates[] = [
                          'id'       => $id,
                          'position' => $key,
                         ];
        }
        $this->db->update_batch('custom_fields', $updates, 'id');
        showMessage(lang('Positions saved', 'admin'));
    }

}