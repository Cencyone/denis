<?php

use Propel\Runtime\ActiveQuery\Criteria;

/**
 * ShopAdminKits
 *
 * @uses ShopController
 * @package
 * @version $id$
 * @copyright 2012 Siteimage
 * @author <vasyl@siteimage.com.ua>
 * @license
 */
class ShopAdminKits extends ShopAdminController
{

    public function __construct() {
        parent::__construct();

        \ShopController::checkVar();
        \ShopAdminController::checkVarAdmin();
    }

    public function index() {
        $model = ShopKitQuery::create()
            ->joinWith('SProducts')
            ->useQuery('SProducts')
            ->joinWithI18n(MY_Controller::defaultLocale(), Criteria::JOIN)
            ->endUse()
            ->orderById(Criteria::ASC)
            ->find();

        $this->render(
            __FUNCTION__,
            ['model' => $model]
        );
    }

    /*     * *************  Product kits  ************** */

    /**
     * create a kit of products
     *
     * @param    integer $mainProductId - main product of a kit
     * @access    public
     * @return    void
     */
    public function kit_create($mainProductId = null) {
        $model = new ShopKit();

        if ($this->input->post()) {
            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                $mainProductId = $this->input->post('MainProductId');
                $attachedProductsIds = $this->input->post('AttachedProductsIds');

                foreach ($this->input->post('AttachedProductsIds') as $key => $value) {
                    $attachedProductsDiscounts[$value] = $this->input->post('Discounts')[$key];
                }

                $mainProduct = SProductsQuery::create()
                    ->findPk($mainProductId);
                if ($mainProduct === NULL) {
                    die(showMessage(ShopCore::t(lang('You did not ask for a set of main commodity', 'admin')), '', 'r'));
                }

                $attachedProducts = SProductsQuery::create()
                    ->findPks($attachedProductsIds);
                if ($attachedProducts->count() === 0) {
                    die(showMessage(ShopCore::t(lang('You must attach the goods to create a set', 'admin')), '', 'r'));
                }

                // check if there are doesn't exist same kit
                $kitCheck = $this->_kitCheck($mainProductId, $attachedProductsIds);

                if ($kitCheck === FALSE) {
                    die(showMessage(ShopCore::t(lang('Kit with such goods already exists', 'admin')), '', 'r'));
                }

                $model->fromArray($this->input->post() + ['Active' => $this->input->post('Active')]);

                //set max position for this kit between the kits with a same main Product
                $kitPosition = $this->_calcNewKitPosition($mainProductId);
                $model->setPosition($kitPosition);

                //main product of a kit
                $model->setProductId($mainProductId);

                foreach ($attachedProducts as $attachedProduct) {
                    $shopKitProduct = new ShopKitProduct();
                    $shopKitProduct->setProductId($attachedProduct->getId());
                    $shopKitProduct->setDiscount($attachedProductsDiscounts[$attachedProduct->getId()]);

                    $model->addShopKitProduct($shopKitProduct);
                }
                $model->save();

                $last_kit_id = $this->db->order_by('id', 'desc')->get('shop_kit')->row()->id;
                $this->lib_admin->log(lang('Kit created', 'admin') . '. Id: ' . $last_kit_id);
                showMessage(ShopCore::t(lang('Kit created', 'admin')));

                if ($this->input->post('action') == 'tomain') {
                    pjax('/admin/components/run/shop/kits/index');
                }

                if ($this->input->post('action') == 'save') {
                    pjax('/admin/components/run/shop/kits/kit_edit/' . $model->getId());
                }
            }
        } else {
            if ($mainProductId) {
                $model->setProductId($mainProductId);
            }
            $this->render(
                __FUNCTION__,
                ['model' => $model]
            );
        }
    }

    /**
     * edit a kit of products
     *
     * @param $kitId
     * @param bool $canChangeMainProduct
     * @throws \Propel\Runtime\Exception\PropelException
     * @internal param int $roleId
     */
    public function kit_edit($kitId, $canChangeMainProduct = true) {
        $postArray = $this->input->post();

        $model = ShopKitQuery::create()
            ->findPk($kitId);

        if ($model === null) {
            $this->error404(ShopCore::t(lang('The kit was not found', 'admin')));
        }

        if ($postArray) {
            $this->form_validation->set_rules($model->rules());

            if ($this->form_validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {

                $postArray['Active'] = (int) $this->input->post('Active');
                $mainProductId = $this->input->post('MainProductId');
                $attachedProductsIds = $this->input->post('AttachedProductsIds');

                foreach ($this->input->post('AttachedProductsIds') as $key => $value) {

                    $attachedProductsDiscounts[$value] = $this->input->post('Discounts')[$key];
                }

                $mainProduct = SProductsQuery::create()
                    ->findPk($mainProductId);
                if ($mainProduct === NULL) {
                    die(showMessage(ShopCore::t(lang('You did not ask for a set of main commodity ', 'admin'))));
                }

                $attachedProducts = SProductsQuery::create()
                    ->findPks($attachedProductsIds);
                if ($attachedProducts->count() === 0) {
                    die(showMessage(ShopCore::t(lang('You must attach the goods to create a set', 'admin'))));
                }

                // check if there are doesn't exist same kit
                $kitCheck = $this->_kitCheck($mainProductId, $attachedProductsIds, $model->getId());

                if ($kitCheck === FALSE) {
                    die(showMessage(ShopCore::t(lang('Kit with such goods already exists', 'admin'))));
                }

                $model->fromArray($postArray);

                //main product of a kit
                $model->setProductId($mainProductId);

                ShopKitProductQuery::create()->setComment(__METHOD__)->filterByShopKit($model)->delete();
                foreach ($attachedProducts as $attachedProduct) {
                    $shopKitProduct = new ShopKitProduct();
                    $shopKitProduct->setProductId($attachedProduct->getId());
                    $shopKitProduct->setDiscount($attachedProductsDiscounts[$attachedProduct->getId()]);

                    $model->addShopKitProduct($shopKitProduct);
                }
                $model->save();

                $this->lib_admin->log(lang('Kit edited', 'admin') . '. Id: ' . $kitId);
                showMessage(ShopCore::t(lang('Changes have been saved', 'admin')));

                if ($this->input->post('action') == 'tomain') {
                    pjax('/admin/components/run/shop/kits/index');
                } else {
                    pjax('/admin/components/run/shop/kits/' . __FUNCTION__ . '/' . $model->getId());
                }
            }
        } else {
            $this->render(
                __FUNCTION__,
                [
                 'model'                => $model,
                 'canChangeMainProduct' => $canChangeMainProduct,
                ]
            );
        }
    }

    /**
     * Save kits positions.
     *
     * @access    public
     * @return    void
     */
    public function kit_save_positions() {
        if (count($this->input->post('Position')) > 0) {
            foreach ($this->input->post('Position') as $id => $pos) {
                ShopKitQuery::create()
                    ->filterById($id)
                    ->update(['Position' => (int) $pos]);
            }
        }
    }

    /**
     * Change a kit active status
     * @param    integer $kitId
     * @access    public
     * @return    void
     */
    public function kit_change_active($kitId) {
        $model = ShopKitQuery::create()
            ->findPk($kitId);

        if ($model !== null) {
            $model->setActive(!$model->getActive());
            if ($model->save()) {
                showMessage(lang('Changes have been saved', 'admin'));
            }
        }
    }

    /**
     * delete a kit
     *
     * @param integer $kitId
     * @access public
     * @return    void
     */
    public function kit_delete() {
        $kitId = $this->input->post('ids');
        $model = ShopKitQuery::create()
            ->findPks($kitId);

        if ($model != null) {
            $model->delete();
        }

        if (count($kitId) > 1) {
            $message = lang('Kits has been removed', 'admin');
        } else {
            $message = lang('Kit has been removed', 'admin');
        }
        $this->lib_admin->log($message . '. Ids: ' . implode(', ', $kitId));
        showMessage($message);
    }

    /**
     * check if there are doesn't exist same kit
     *
     * @access    protected
     * @param    integer $mainProductId - main product Id
     * @param    array $attachedPIds - the ids of atached products
     * @return    boolean - TRUE if a kit doesnt exist(available for creation)
     */
    protected function _kitCheck($mainProductId, $attachedPIds, $kit = null) {
        //all existing kit with the same main product
        $kits = ShopKitQuery::create();

        if ($kit !== null) {
            $kits = $kits->filterById($kit, Criteria::NOT_IN);
        }

        $kits = $kits->filterByProductId($mainProductId)
            ->find();
        //if there are exist some kit|kits with a same main product
        if ($kits->count() > 0) {
            //getting attached products ids array of these kits
            foreach ($kits as $kit) {
                $criteria = ShopKitProductQuery::create()
                    ->select(['ProductId']);
                $pIds = $kit->getShopKitProducts($criteria)
                    ->toArray();

                //count the total atached products to a kit in db
                $attachedPIdsCount = count($attachedPIds);

                //if a kit from a db has the same products number
                if (count($pIds) == $attachedPIdsCount) {
                    //check if there are difference between those kits
                    $pIdsDiff = array_diff($pIds, $attachedPIds);

                    //return FALSE if the kits are the same
                    if (empty($pIdsDiff)) {
                        return FALSE;
                    }
                }
            }
        }

        //return TRUE if there are doesn't exist same kit
        return TRUE;
    }

    /**
     * calculate position for a new kit
     *
     * @param    integer $mainProductId - main product Id
     * @return    integer $newPosition - position for a new kit
     */
    protected function _calcNewKitPosition($mainProductId = NULL) {
        if ($mainProductId !== NULL) {
            //max position of all existing kit with a same main product
            $kit = ShopKitQuery::create()
                ->orderByPosition(Criteria::DESC)
                ->filterByProductId($mainProductId)
                ->limit(1)
                ->findOne();

            if ($kit !== null) {
                return $kit->getPosition() + 1;
            }
        }

        return 0;
    }

    /*     * *************  Other  ************** */

    /**
     * get the list of products
     * @access    public
     * @param string|null $type
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function get_products_list($type = 'products') {
        $locale = $this->input->post('locale') ?: \MY_Controller::defaultLocale();
        $products = SProductsQuery::create()
            ->joinWithI18n($locale);

        if ($this->input->post('noids')) {
            $noids = explode(',', $this->input->post('noids'));
            $products->filterById($noids, Criteria::NOT_IN);
        } else {
            $noids = [];
        }

        $searched = trim($this->input->get('term') ?: $this->input->post('q'));

        if ($searched) {
                /** @var SProductsQuery $products */
                $products = $products
                    ->joinWithProductVariant()
                    ->condition('numberCondition', 'ProductVariant.Number LIKE ?', '%' . $searched . '%')
                    ->condition('nameCondition', 'SProductsI18n.Name LIKE ?', '%' . $searched . '%')
                    ->condition('idtCondition', 'SProducts.Id LIKE ?', '%' . $searched . '%')
                    ->where(['numberCondition', 'nameCondition', 'idtCondition'], Criteria::LOGICAL_OR);
        }

        $products = $products
            ->distinct()
            ->find();

        $response = [];
        if ($type === 'products') {
            foreach ($products as $key => $product) {
                $name = $product->getName();
                $number = $product->getNumber() ? ' (' . $product->getNumber() . ')' : '';
                $label = ShopCore::encode($product->getId() . ' - ' . $name . $number);

                $response[] = [
                               'number'     => $number,
                               'label'      => $label,
                               'name'       => ShopCore::encode($product->getName()),
                               'id'         => $product->getId(),
                               'photo'      => $product->firstVariant->getSmallPhoto(),
                               'price'      => $product->firstVariant->getPrice(),
                               'value'      => $product->getId(),
                               'category'   => $product->getCategoryId(),
                               'cs'         => \Currency\Currency::create()->getSymbol(),
                               'identifier' => [
                                                'id' => $product->getId(),
                                               ],
                              ];
            }

        } else {
            $variants = SProductVariantsQuery::create()
                ->joinI18n($locale, null, Criteria::JOIN)
                ->filterBySProducts($products)
                ->orderById(Criteria::DESC)
                ->find();
            foreach ($variants as $variant) {
                if (in_array($variant->getProductId(), $noids)) {
                    continue;
                }
                $pVariants[$variant->getProductId()][$variant->getId()]['name'] = ShopCore::encode($variant->getName());
                $pVariants[$variant->getProductId()][$variant->getId()]['price'] = $variant->getPrice();
                $pVariants[$variant->getProductId()][$variant->getId()]['number'] = $variant->getNumber();
            }

            foreach ($products as $key => $product) {
                if ($pVariants[$product->getId()]) {
                    foreach ($pVariants[$product->getId()] as $key => $variant) {

                        $name = $variant['name'] ? $variant['name'] : $product->getName();
                        $label = ShopCore::encode($product->getId() . ' - ' . $name . ' (' . $variant['number'] . ')');
                        $response[] = [
                                       'number'     => ($type != NULL AND count($product) > 0) ? ShopCore::encode($product->getNameVariant()) : ' - ',
                                       'label'      => $label,
                                       'name'       => ShopCore::encode($product->getName()),
                                       'id'         => $product->getId(),
                                       'photo'      => $product->firstVariant->getSmallPhoto(),
                                       'price'      => $product->firstVariant->getPrice(),
                                       'value'      => $product->getId(),
                                       'category'   => $product->getCategoryId(),
                                       'variants'   => $pVariants[$product->getId()],
                                       'cs'         => \Currency\Currency::create()->getSymbol(),
                                       'identifier' => [
                                                        'id' => $product->getId(),
                                                       ],
                                      ];
                    }
                }
            }

        }

        echo json_encode($response);
    }

    /**
     * redirecting
     *
     * @param $model
     * @param string $entityName - name of a RBAC entity: role|privilege
     * @return    void
     */
    protected function _redirect($model, $entityName) {
        $redirect_url = null;
        //get controller name from a class name
        $controllerName = str_replace('ShopAdmin', '', get_class());
        $controllerName = strtolower($controllerName);
        if ($this->input->post('_add')) {
            $redirect_url = $controllerName . '/' . $entityName . '_list';
        }

        if ($this->input->post('_create')) {
            $redirect_url = $controllerName . '/' . $entityName . '_create';
        }

        if ($this->input->post('_edit')) {
            $redirect_url = $controllerName . '/' . $entityName . '_edit/' . $model->getId();
        }

        if ($redirect_url !== null) {
            $this->ajaxShopDiv($redirect_url);
        }
    }

}