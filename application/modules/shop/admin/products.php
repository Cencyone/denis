<?php

use Category\CategoryApi;
use CMSFactory\Events;
use Currency\Currency;
use MediaManager\GetImages;
use MediaManager\Image;
use Products\ProductApi;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;

/**
 * ShopAdminProducts
 *
 * @property Lib_admin $lib_admin
 * @property Cms_admin cms_admin
 * @uses ShopController
 * @package
 * @version $id$
 * @copyright 2010 SiteImage
 * @author <dev@imagecms.net>
 * @license
 */
class ShopAdminProducts extends ShopAdminController
{

    protected $per_page = 20;

    protected $allowedImageExtensions = [
        'jpg',
        'png',
        'gif',
        'jpeg',
    ];

    public $defaultLanguage = null;

    protected $imageSizes = [];

    protected $imageQuality = 99;

    public function __construct()
    {

        parent::__construct();

        ShopController::checkVar();
        ShopAdminController::checkVarAdmin();

        $this->per_page = ShopCore::app()->SSettings->getAdminProductsPerPage();
        $this->load->helper('url');
        $this->load->library('upload');
        $this->load->helper('translit');
        $this->load->helper('cookie');
        $this->defaultLanguage = getDefaultLanguage();
    }

    public function ajax_translit()
    {

        $this->load->helper('translit');
        $str = $this->input->post('str');
        echo translit_url($str);
    }

    /**
     * Display list of products in category
     *
     * @param integer $categoryID
     * @param int $offset
     * @param string $orderField
     * @param string $orderCriteria
     * @access public
     */
    public function index($categoryID = null, $offset = 0, $orderField = '', $orderCriteria = '')
    {

        $model = SCategoryQuery::create()
            ->findPk((int)$categoryID);

        if ($model === null) {
            $this->error404(lang('Category not found', 'admin'));
        }

        $products = SProductsQuery::create()
            ->filterByCategory($model);

        // Set total products count
        $totalProducts = clone $products;
        $totalProducts = $totalProducts->count();

        $products = $products
            ->limit($this->per_page)
            ->offset((int)$offset);

        $nextOrderCriteria = '';

        if ($orderField !== '' && $orderCriteria !== '' && method_exists($products, 'filterBy' . $orderField)) {
            switch ($orderCriteria) {
                case 'ASC':
                    $products = ($orderField != 'Price') ? $products->orderBy($orderField, Criteria::ASC) : $products->leftJoin('ProductVariant')->orderBy($orderField, Criteria::ASC);
                    $nextOrderCriteria = 'DESC';
                    break;

                case 'DESC':
                    $products = ($orderField != 'Price') ? $products->orderBy($orderField, Criteria::DESC) : $products->leftJoin('ProductVariant')->orderBy($orderField, Criteria::DESC);
                    $nextOrderCriteria = 'ASC';
                    break;
            }
        } else {
            $products->orderById('desc');
        }

        $products = $products->find();

        $products->populateRelation('ProductVariant');

        // Create pagination
        $this->load->library('pagination');
        $config = [];
        $config['base_url'] = $this->createUrl('products/index/', ['catId' => $model->getId()]);
        $config['container'] = 'shopAdminPage';
        $config['uri_segment'] = 8;
        $config['total_rows'] = $totalProducts;
        $config['per_page'] = $this->per_page;
        $config['suffix'] = ($orderField != '') ? $orderField . '/' . $orderCriteria : '';
        $this->pagination->num_links = 6;
        $this->pagination->initialize($config);
        $this->render(
            'list',
            [
                'model' => $model,
                'products' => $products,
                'totalProducts' => $totalProducts,
                'pagination' => $this->pagination->create_links_ajax(),
                'category' => SCategoryQuery::create()->setComment(__METHOD__)->findPk((int)$categoryID),
                'nextOrderCriteria' => $nextOrderCriteria,
                'orderField' => $orderField,
                'locale' => $this->defaultLanguage['identif'],
            ]
        );
    }

    /**
     * Validation for template name field
     * @param string $tpl
     * @return bool
     */
    public function tpl_validation($tpl)
    {

        if (preg_match('/^[A-Za-z\_\.]{0,50}$/', $tpl)) {
            return TRUE;
        }
        $this->form_validation->set_message('tpl_validation', lang('The %s field can only contain Latin characters', 'admin'));
        return FALSE;
    }

    /**
     * Create new product, upload and resize images.
     *
     * @access public
     */
    public function create()
    {

        $model = new SProducts();
        $locale = MY_Controller::getCurrentLocale();

        Events::create()->registerEvent('', 'ShopAdminProducts:preCreate');
        Events::runFactory();

        if ($this->input->post()) {
            $this->form_validation->set_rules($model->rules());
            $validation = $this->form_validation->set_rules('Created', lang('Date Created', 'admin'), 'required|valid_date');

            if ($validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                $images = $this->upload_all();
                $datas = $this->input->post();

                $data = [
                    'product_name' => $datas['Name'],
                    'active' => $datas['Active'],
                    'variant_name' => $datas['variants']['Name'][0],
                    'price_in_main' => $datas['variants']['PriceInMain'][0],
                    'currency' => $datas['variants']['currency'][0],
                    'number' => $datas['variants']['Number'][0],
                    'stock' => $datas['variants']['Stock'][0],
                    'brand_id' => $datas['BrandId'],
                    'category_id' => $datas['CategoryId'],
                    'additional_categories_ids' => $datas['Categories'] ?: [],
                    'short_description' => $datas['ShortDescription'],
                    'full_description' => $datas['FullDescription'],
                    'old_price' => $datas['OldPrice'],
                    'tpl' => $datas['tpl'],
                    'url' => $datas['Url'],
                    'meta_title' => $datas['MetaTitle'],
                    'meta_description' => $datas['MetaDescription'],
                    'meta_keywords' => $datas['MetaKeywords'],
                    'related_products' => $datas['RelatedProducts'],
                    'enable_comments' => $datas['EnableComments'],
                    'created' => $datas['Created'] ? strtotime($datas['Created']) : '',
                    'updated' => time(),
                    'hit' => $datas['hit'],
                    'hot' => $datas['hot'],
                    'action' => $datas['actions'],
                ];

                /** Set product first variant mainImage name uploaded from computer or internet */
                if ($datas['changeImage'][0]) {
                    $data['mainImage'] = $images['image0'] ?: '';
                } else {
                    if ($datas['variants']['inetImage'][0]) {
                        $imageName = GetImages::create()->saveImage($datas['variants']['inetImage'][0]);
                        $images['image0'] = $imageName;
                        $data['mainImage'] = $imageName;
                    }
                }

                /** Delete product first variant image */
                if ($datas['variants']['MainImageForDel'][0]) {
                    Image::create()->deleteAllProductImages($datas['variants']['mainImageName'][0]);
                    $data['mainImage'] = '';
                }

                $model = ProductApi::getInstance()->addProduct($data, $locale);

                if (ProductApi::getInstance()->getError()) {
                    showMessage(ProductApi::getInstance()->getError(), '', 'r');
                    exit;
                }
                if (count($datas['variants']['PriceInMain']) > 1) {
                    $variantsCount = count($datas['variants']['PriceInMain']);
                    for ($i = 1; $i < $variantsCount; $i++) {
                        $varDatas[$i] = [
                            'number' => $datas['variants']['Number'][$i],
                            'stock' => $datas['variants']['Stock'][$i],
                            'currency' => $datas['variants']['currency'][$i],
                            'price_in_main' => $datas['variants']['PriceInMain'][$i],
                            'position' => $i,
                            'variant_name' => $datas['variants']['Name'][$i],
                        ];

                        /** Set product variants mainImage name uploaded from computer or internet */
                        if ($datas['changeImage'][$i] && !$datas['variants']['MainImageForDel'][$i]) {
                            $varDatas[$i]['mainImage'] = $images['image' . $i] ?: '';
                        } else {
                            if ($datas['variants']['inetImage'][$i]) {
                                $imageName = GetImages::create()->saveImage($datas['variants']['inetImage'][$i]);
                                $images['image' . $i] = $imageName;
                                $varDatas[$i]['mainImage'] = $imageName;
                            }
                        }

                        /** Copy previous variant image if image not selected */
                        if (!$varDatas[$i]['mainImage'] && !$datas['variants']['MainImageForDel'][$i]) {
                            if ($i > 1) {
                                $imageName = $datas['variants']['mainImageName'][$i];
                            } else {
                                $imageName = $data['mainImage'] ?: $datas['variants']['mainImageName'][$i];
                            }

                            $varDatas[$i]['mainImage'] = ProductApi::getInstance()->copyProductImage($imageName) ?: '';
                            $datas['variants']['mainImageName'][$i] = $varDatas[$i]['mainImage'];
                            $images["image$i"] = $varDatas[$i]['mainImage'];
                        }

                        /** Delete product first variant image */
                        if ($datas['variants']['MainImageForDel'][$i] && !$datas['changeImage'][$i] && !$datas['variants']['inetImage'][$i]) {
                            Image::create()->deleteAllProductImages($datas['variants']['mainImageName'][$i]);
                            $varDatas[$i]['mainImage'] = '';
                        }

                        ProductApi::getInstance()->addVariant($model->getId(), $varDatas[$i]);
                    }
                }

                /** Check folder and process images * */
                Image::create()->checkOriginFolder();
                /** Check images folders* */
                Image::create()->checkImagesFolders();
                /** Check watermarks folder */
                Image::create()->checkWatermarks();

                Image::create()->resizeByName($images);

                /** Init Event. Create Shop Product */
                Events::create()->registerEvent(['model' => $model, 'productId' => $model->getId(), 'userId' => $this->dx_auth->get_user_id()]);
                Events::runFactory();

                $last_prod_id = $this->db->order_by('id', 'desc')->get('shop_products')->row()->id;
                $this->lib_admin->log(lang('The product was created', 'admin') . '. Id: ' . $last_prod_id);
                showMessage(lang('The product was successfully created', 'admin'));

                if ($this->input->post('action') === 'close') {
                    pjax('/admin/components/run/shop/search/index');
                } else {
                    pjax('/admin/components/run/shop/products/edit/' . $model->getId());
                }
            }
        } else {

            $offset = 0;
            $per_page = 10000;

            $brands = SBrandsQuery::create()
                ->joinWithI18n(MY_Controller::defaultLocale(), Criteria::JOIN)
                ->distinct()
                ->orderByPosition(Criteria::DESC)
                ->limit($per_page)
                ->offset((int)$offset)
                ->find();

            $currencies = SCurrenciesQuery::create()->setComment(__METHOD__)->find();
            $this->render(
                'create',
                [
                    'brands' => $brands,
                    'model' => $model,
                    'categories' => SCategoryQuery::create()->getTree(0, SCategoryQuery::create()->joinWithI18n($locale))->getCollection(),
                    'cur_date' => date('Y-m-d H:i:s'),
                    'locale' => $locale,
                    'currencies' => $currencies,
                    'imagesPopup' => $this->render('images', [], TRUE),
                ]
            );
        }
    }

    /**
     * @return void
     */
    public function fastProdCreate()
    {

        /** Init Event. PreEdit Shop Product */
        Events::create()->registerEvent('', 'ShopAdminProducts:prefastProdCreate');
        Events::runFactory();

        if ($this->input->post()) {

            $model = new SProducts();

            $rule = [
                [
                    'field' => 'Name',
                    'label' => $model->getLabel('Name'),
                    'rules' => 'required',
                ],
                [
                    'field' => 'price',
                    'label' => $model->getLabel('Price'),
                    'rules' => 'trim|required',
                ],
                [
                    'field' => 'CategoryId',
                    'label' => $model->getLabel('CategoryId'),
                    'rules' => 'required|integer',
                ],
            ];

            $this->form_validation->set_rules($rule);

            if ($this->form_validation->run()) {

                $locale = MY_Controller::getCurrentLocale();

                if ($_FILES['mainPhoto']) {
                    $config['upload_path'] = PUBPATH . 'uploads/shop/products/origin';
                    $config['allowed_types'] = 'gif|jpg|jpeg|png';
                    $config['encrypt_name'] = true;
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('mainPhoto')) {
                        echo json_encode(
                            [
                                'error' => 1,
                                'data' => $this->upload->display_errors(),
                            ]
                        );
                        exit;
                    } else {
                        $result = ['upload_data' => $this->upload->data()];
                    }
                }

                $data = [
                    'active' => (int)$this->input->post('active'),
                    'archive' => (int)$this->input->post('archive'),
                    'hit' => (int)$this->input->post('hit'),
                    'action' => (int)$this->input->post('action'),
                    'price_in_main' => str_replace(',', '.', $this->input->post('price')),
                    'hot' => (int)$this->input->post('hot'),
                    'category_id' => $this->input->post('CategoryId'),
                    'brand_id' => 0,
                    'url' => translit_url($this->input->post('Name')),
                    'created' => time(),
                    'product_name' => $this->input->post('Name'),
                    'enable_comments' => '1',
                    'stock' => 1,
                    'number' => $this->input->post('number'),
                    'currency' => Currency::create()->default->getId(),
                ];

                $data['mainImage'] = $result['upload_data']['file_name'] ? $result['upload_data']['file_name'] : '';

                $model = ProductApi::getInstance()->addProduct($data, $locale);

                if (ProductApi::getInstance()->getError()) {
                    echo json_encode(
                        [
                            'error' => 1,
                            'data' => ProductApi::getInstance()->getError(),
                        ]
                    );
                    exit;
                }

                /** Check folder and process images * */
                Image::create()->checkOriginFolder();
                /** Check images folders* */
                Image::create()->checkImagesFolders();
                /** Check watermarks folder */
                Image::create()->checkWatermarks();

                Image::create()->resizeByName([$data['mainImage']]);

                /** Init Event. Create Shop Product */
                Events::create()->registerEvent(['model' => $model, 'productId' => $model->getId(), 'userId' => $this->dx_auth->get_user_id()]);
                Events::runFactory();

                $categories = SCategoryQuery::create()->getTree(0, SCategoryQuery::create()->joinWithI18n(MY_Controller::defaultLocale()))->getCollection();
                $oneProductView = $this->render('oneProductListItem', ['model' => $model], TRUE);
                $fastCreateFormView = $this->render('fastCreateForm', ['show_fast_form' => TRUE, 'categories' => $categories], TRUE);
                echo json_encode(
                    [
                        'error' => 0,
                        'data' => lang('The product was successfully created', 'admin'),
                        'viewOneProduct' => $oneProductView,
                        'viewFastCreateForm' => $fastCreateFormView,
                    ]
                );
            } else {
                echo json_encode(
                    [
                        'error' => 1,
                        'data' => validation_errors(),
                    ]
                );
            }
        }
    }

    /**
     * @param $type
     * @return void
     */
    public function get_images($type)
    {

        $url = trim($this->input->post('q'));
        if ($type == 'url') {

            $image = GetImages::create()->getImage($url);
            $url1 = $image === FALSE ? '0' : $url;
            echo json_encode(['url' => $url1]);
        }
    }

    /**
     * @return void
     */
    public function checkImageStatus()
    {

        $imageUrl = $this->input->get('imageUrl');
        $imageStatus = GetImages::create()->checkImage($imageUrl);

        if ($imageStatus === FALSE) {
            header('HTTP/1.0 404 Not Found');
        }
    }

    /**
     * @return void
     */
    public function save_image()
    {

        $url = $this->input->post('url');
        GetImages::create()->saveImages($this->input->post('productId'), trim($url));
    }

    /**
     * @return void
     */
    public function getGImagesProgress()
    {

        $count = GetImages::create()->getProgress();
        echo json_encode(['count' => $count]);
    }

    /**
     * @param string $name
     * @return void
     */
    private function unlinkImage($name)
    {

        unlink(PUBPATH . 'uploads/shop/products/origin/' . $name);
        unlink(PUBPATH . 'uploads/shop/products/additional/' . $name);
        unlink(PUBPATH . 'uploads/shop/products/large/' . $name);
        unlink(PUBPATH . 'uploads/shop/products/main/' . $name);
        unlink(PUBPATH . 'uploads/shop/products/medium/' . $name);
        unlink(PUBPATH . 'uploads/shop/products/small/' . $name);
    }

    /**
     * @param int $productId
     * @param null|string $locale
     * @throws Exception
     */
    public function edit($productId, $locale = null)
    {

        $locale = $locale == null ? MY_Controller::getCurrentLocale() : $locale;

        /** Магия, отвечат за загрузку дополнительных изображений */
        if ($_FILES['userFile']) {

            /** @var int максимальная позиция фотографий $countImages */
            $countImages = $this->input->post('countImages') ?: 0;

            try {
                $additional = $this->saveNewAdditionalPhoto($countImages);
            } catch (Exception $e) {
                showMessage($e->getMessage(), '', 'r');
            }
        }

        $model = SProductsQuery::create()
            ->useProductVariantQuery()
            ->orderByPosition()
            ->endUse()
            ->leftJoinWith('ProductVariant')
            ->findPk((int)$productId);

        $useVariantPrice = false;

        if (MY_Controller::isPremiumCMS()) {

            $variant_price = SProductVariantPriceTypeQuery::create()
                ->joinWithCurrency(Criteria::LEFT_JOIN)
                ->orderByPosition()
                ->find();

            $useVariantPrice = true;
        }

        if ($model === null) {
            $this->error404(lang('Product not found', 'admin'));
        }

        /** Init Event. PreEdit Shop Product */
        Events::create()->registerEvent(['model' => $model, 'userId' => $this->dx_auth->get_user_id(), 'url' => $model->getUrl()], 'ShopAdminProducts:preEdit');
        Events::runFactory();

        if ($this->input->post()) {

            $this->form_validation->set_rules($model->rules());
            $validation = $this->form_validation->set_rules('Created', lang('Date Created', 'admin'), 'required|valid_date');
            $validation = $model->validateCustomData($validation);

            if ($validation->run($this) == FALSE) {
                showMessage(validation_errors(), '', 'r');
            } else {
                $previousCategoriId = $model->getCategoryId();
                $datas = $this->input->post();
                $variantsInDb = $model->getProductVariants();
                $images = $this->upload_all();

                /** Data array for product update */
                $data = [
                    'product_name' => $datas['Name'],
                    'active' => $datas['Active'],
                    'variant_name' => $datas['variants']['Name'][0],
                    'price_in_main' => $datas['variants']['PriceInMain'][0],
                    'currency' => $datas['variants']['currency'][0],
                    'number' => $datas['variants']['Number'][0],
                    'stock' => $datas['variants']['Stock'][0],
                    'brand_id' => $datas['BrandId'],
                    'category_id' => (int)$datas['CategoryId'],
                    'additional_categories_ids' => $datas['Categories'],
                    'short_description' => $datas['ShortDescription'],
                    'full_description' => $datas['FullDescription'],
                    'old_price' => $datas['OldPrice'],
                    'tpl' => $datas['tpl'],
                    'url' => $datas['Url'],
                    'meta_title' => $datas['MetaTitle'],
                    'meta_keywords' => $datas['MetaKeywords'],
                    'meta_description' => $datas['MetaDescription'],
                    'enable_comments' => $datas['EnableComments'],
                    'related_products' => $datas['RelatedProducts'],
                    'created' => strtotime($this->input->post('Created')),
                    'updated' => time(),
                ];

                /** Set product first variant mainImage name uploaded from computer or internet */
                if ($datas['changeImage'][0]) {
                    $data['mainImage'] = $images['image0'] ? $images['image0'] : '';
                } else {
                    if ($datas['variants']['inetImage'][0]) {
                        /* delete old variants images */
                        //                        $this->deleteOldImageVariantInternet($productId, 0);
                        $imageName = GetImages::create()->saveImage($datas['variants']['inetImage'][0]);
                        $images['image0'] = $imageName;
                        $data['mainImage'] = $imageName;
                    }
                }

                /** Delete product first variant image */
                if ($datas['variants']['MainImageForDel'][0]) {
                    $data['mainImage'] = '';
                }

                /** Update product */
                $model = ProductApi::getInstance()->updateProduct((int)$productId, $data, $locale, $datas['variants']['CurrentId'][0]);

                /** Show error message if error exists */
                if (ProductApi::getInstance()->getError()) {
                    showMessage(ProductApi::getInstance()->getError(), '', 'r');
                    exit;
                }

                /** Set product variants data */
                $varDatas = [];
                if (count($datas['variants']['PriceInMain']) > 1) {
                    $variantsCount = count($datas['variants']['PriceInMain']);
                    for ($i = 1; $i < $variantsCount; $i++) {
                        $varDatas[$i] = [
                            'number' => $datas['variants']['Number'][$i],
                            'stock' => $datas['variants']['Stock'][$i],
                            'currency' => $datas['variants']['currency'][$i],
                            'price_in_main' => $datas['variants']['PriceInMain'][$i],
                            'position' => $i,
                            'variant_name' => $datas['variants']['Name'][$i],
                        ];

                        /** Set product variants mainImage name uploaded from computer or internet */
                        if ($datas['changeImage'][$i]) {
                            $varDatas[$i]['mainImage'] = $images['image' . $i] ?: '';
                        }

                        if ($datas['variants']['inetImage'][$i]) {
                            /* delete old variants images */
                            $imageName = GetImages::create()->saveImage($datas['variants']['inetImage'][$i]);
                            $images['image' . $i] = $imageName;
                            $varDatas[$i]['mainImage'] = $imageName;
                        }

                        /** Copy previous variant image if image not selected */
                        if (!$datas['variants']['CurrentId'][$i] && !$varDatas[$i]['mainImage']) {
                            if ($i >= 1) {
                                $imageName = $datas['variants']['mainImageName'][$i];
                                $imageName = $datas['variants']['copyImage'][$i] ? $datas['variants']['copyImage'][$i] : $imageName;
                            } else {
                                $imageName = $data['mainImage'] ?: $datas['variants']['mainImageName'][$i];
                            }

                            $varDatas[$i]['mainImage'] = ProductApi::getInstance()->copyProductImage($imageName);
                            $datas['variants']['mainImageName'][$i] = $varDatas[$i]['mainImage'];
                            $images["image$i"] = $varDatas[$i]['mainImage'];
                        }

                        /** Delete product first variant image */
                        if ($datas['variants']['MainImageForDel'][$i] && !$datas['changeImage'][$i] && !$datas['variants']['inetImage'][$i]) {
                            Image::create()->deleteAllProductImages($datas['variants']['mainImageName'][$i]);
                            $varDatas[$i]['mainImage'] = '';
                        }

                        /** Update or add product variant if variant_id not exists */
                        if ($datas['variants']['CurrentId'][$i]) {
                            $variantsIds[] = $datas['variants']['CurrentId'][$i];
                            $varId = ProductApi::getInstance()->updateVariant($productId, $varDatas[$i], $locale, $datas['variants']['CurrentId'][$i]);
                        } else {
                            $varId = ProductApi::getInstance()->addVariant($productId, $varDatas[$i], $locale);
                        }
                    }
                }

                /** Delete product first variant image */
                if ($datas['variants']['MainImageForDel'][0]) {
                    Image::create()->deleteAllProductImages($datas['variants']['mainImageName'][0]);
                }

                /** Prepare array variants ids to delete */
                $variantsIdsToDelete = [];
                $variantsIds[] = $datas['variants']['CurrentId'][0];
                foreach ($variantsInDb as $variant) {
                    if (!in_array($variant->getId(), $variantsIds)) {
                        $variantsIdsToDelete[] = $variant->getId();
                    }
                }

                /** Delete variants */
                if (count($variantsIdsToDelete) > 0) {
                    foreach ($variantsIdsToDelete as $value) {
                        $res = $this->db
                            ->where('id', $value)
                            ->get('shop_product_variants')
                            ->row_array();

                        $this->unlinkImage($res['mainImage']);
                    }
                    SProductVariantsQuery::create()->setComment(__METHOD__)->filterById($variantsIdsToDelete)->delete();
                }

                $postProperty = $this->input->post('productProperties');

                $data_delete = SProductPropertiesDataQuery::create()
                    ->useSPropertyValueQuery()
                    ->joinI18n($locale, null, Criteria::INNER_JOIN)
                    ->endUse()
                    ->filterByProductId($productId)
                    ->find();

                $data_delete->delete();
                if (count($postProperty) > 0) {
                    foreach ($postProperty as $property_id => $property_value) {
                        if ($property_value !== '') {

                            ProductApi::getInstance()->setProductPropertyValue($productId, $property_id, $property_value, $locale);
                        } else {
                            ProductApi::getInstance()->deleteProductPropertyValue($productId, $property_id, $locale);
                        }
                    }
                }

                /** Check folder and process images * */
                Image::create()->checkOriginFolder();
                /** Check images folders* */
                Image::create()->checkImagesFolders();
                /** Check watermarks folder */
                Image::create()->checkWatermarks();

                Image::create()->resizeByName($images);

                /**  Save Additional images (from internet)  */
                $j = 0;
                $params = ['upload_dir' => PUBPATH . 'uploads/shop/products/origin/additional/'];

                GetImages::create($params);
                while (array_key_exists('add_img_urls_' . $j, $datas)) {
                    if (!empty($datas['add_img_urls_' . $j]) & !array_key_exists($j, $additional)) {
                        /* delete old variants images */
                        // $this->deleteOldImageVariant($productId, $additional);
                        if (FALSE !== $image = GetImages::create($params)->saveImage($datas['add_img_urls_' . $j])) {
                            $additional[$j] = $image;
                        }
                    }
                    $j++;
                }

                $results = [];
                foreach ($model->getSProductImagess() as $image) {
                    $oldAdditional[$image->getPosition()] = $image->getImageName();
                }

                /** Если в бд уже есть другие фото, только тогда оно их мерджит */
                if ($oldAdditional) {
                    ksort($oldAdditional);
                    $additional = array_merge($additional, $oldAdditional);
                }

                foreach ($additional as $position => $image) {
                    Image::create()->makeResizeAndWatermarkAdditional($image);
                    $results[] = ProductApi::getInstance()->saveProductAdditionalImage($productId, $image, $position);
                }

                /** Init Event. Edit Shop Product */
                Events::create()->registerEvent(['model' => $model, 'productId' => $model->getId(), 'url' => $model->getUrl(), 'userId' => $this->dx_auth->get_user_id()]);
                Events::runFactory();

                /**
                 * send notifications if changes product quantity
                 */
                Notificator::run($model->getId());

                $this->lib_admin->log(lang('The product was edited', 'admin') . '. Id: ' . $productId);
                showMessage(lang('The product was successfully edited', 'admin'));
                $action = $this->input->post('action');

                if ($action == 'close') {
                    pjax('/admin/components/run/shop/search/index' . $this->session->userdata('ref_url'));
                } else {
                    pjax('/admin/components/run/shop/products/edit/' . $model->getId() . '/' . $locale . $this->session->userdata('ref_url'));
                }
            }
        } else {
            // Create array from ids of additional product categories.
            $productCategories = [];
            foreach ($model->getCategories() as $productCategory) {
                array_push($productCategories, $productCategory->getId());
            }

            $model->setLocale($locale);

            foreach ($model->getProductVariants() as $variant) {
                $variant->setLocale($locale);
            }
            $currencies = SCurrenciesQuery::create()->setComment(__METHOD__)->find();

            $links = $this->prev_next($model->getId(), $this->get_ids($this->input->get(), $productId));

            $brands = SBrandsQuery::create()
                ->orderById(Criteria::DESC)
                ->joinWithI18n(MY_Controller::getCurrentLocale(), Criteria::JOIN)
                ->orderByPosition(Criteria::DESC)
                ->find();

            $properties = SPropertiesQuery::create()
                ->setComment(__METHOD__)
                ->joinWithI18n($locale)
                ->orderByPosition();

            foreach ($model->getShopProductCategoriess() as $cat) {
                $properties->_or()->filterByPropertyCategory($cat->getCategory());
            }

            $properties->groupById()->find();

            $propertiesData = $this->getPropertiesData($model);

            $this->render(
                'edit',
                [
                    'propertiesData' => $propertiesData,
                    'properties' => $properties,
                    'variant_price' => $variant_price,
                    'brands' => $brands,
                    'model' => $model,
                    'languages' => $this->cms_admin->get_langs(true),
                    'categories' => SCategoryQuery::create()->getTree(0, SCategoryQuery::create()->joinWithI18n($locale, Criteria::INNER_JOIN))->getCollection(),
                    'productCategories' => $productCategories,
                    'additionalImagePositions' => $model->getSProductImagess(SProductImagesQuery::create()->setComment(__METHOD__)->orderByPosition()),
                    'defaultLocale' => MY_Controller::defaultLocale(),
                    'locale' => $locale,
                    'currencies' => $currencies,
                    'prev_id' => $links['prev'],
                    'next_id' => $links['next'],
                    'imagesPopup' => $this->render('images', ['showAdditionalChecker' => TRUE], TRUE),
                    'addField' => ShopCore::app()->CustomFieldsHelper->getCustomFields('product', $model->getId())->asAdminHtml(),
                    'usePriceType' => $useVariantPrice,

                ]
            );
        }
    }

    /**
     * @param SProducts $model
     * @return array
     * @throws PropelException
     */
    private function getPropertiesData(SProducts $model)
    {
        $data = $model->getSProductPropertiesDatas();
        $propertiesData = [];
        foreach ($data as $item) {
            $propertiesData[$item->getPropertyId()][] = $item->getValueId();
        }
        return $propertiesData;
    }

    /**
     * Delete product
     *
     * @access public
     */
    public function delete()
    {

        $model = SProductsQuery::create()->setComment(__METHOD__)->findPk((int)$this->input->post('productId'));

        if ($model !== null) {
            $model->delete();
        }

        /** Init Event. Create Shop Product */
        Events::create()->registerEvent(['productId' => $this->input->post('productId'), 'userId' => $this->dx_auth->get_user_id()]);
        Events::runFactory();
    }

    /**
     * @param null|integer $productId
     * @throws PropelException
     */
    public function ajaxChangeActive($productId = null)
    {

        if ($this->input->post('ids')) {
            $model = SProductsQuery::create()
                ->findPks($this->input->post('ids'));

            foreach ($model as $product) {
                $product->setActive(!$product->getActive());
                $product->save();
            }
        } else {
            $model = SProductsQuery::create()
                ->findPk($productId);
            if ($model !== null) {
                $model->setActive(!$model->getActive());
                $model->save();
            }
        }

        Events::create()->registerEvent(
            [
                'model' => $model,
                'userId' => $this->dx_auth->get_user_id(),
            ],
            'ShopAdminProducts:ajaxChangeActive'
        );
        Events::runFactory();

        showMessage(lang('Successfully changed', 'admin'));
        $url = $this->input->server('HTTP_REFERER');
        $this->cache->delete_all();
        pjax($url);
    }

    /**
     * @param null|integer $productId
     */
    public function ajaxChangeHit($productId = null)
    {

        $this->change('Hit', $productId);
    }

    /**
     * @param null|integer $productId
     */
    public function ajaxChangeHot($productId = null)
    {

        $this->change('Hot', $productId);
    }

    /**
     * @param null|integer $productId
     */
    public function ajaxChangeArchive($productId = null)
    {

        $this->change('Archive', $productId);
    }

    /**
     * @param null|integer $productId
     */
    public function ajaxChangeAction($productId = null)
    {

        $this->change('Action', $productId);
    }

    /**
     * @param integer $productId
     * @param int $customFieldId
     */
    public function ajaxChangeCustomField($productId, $customFieldId)
    {
        $field = CustomFieldsDataQuery::create()
            ->filterByentityId($productId)
            ->filterByfieldId($customFieldId)
            ->findOneOrCreate();

        $field->setdata(!(int)$field->getdata());

        if ($field->getId() === null) {
            $field->setfieldId($customFieldId);
            $field->setLocale(MY_Controller::getCurrentLocale());
        }

        $field->save();
    }

    /**
     * @param string $type
     * @param null|integer $productId
     * @throws PropelException
     */
    private function change($type, $productId = null)
    {

        $model = SProductsQuery::create()
            ->findPk($productId);

        if ($model !== null) {
            $model->{"set$type"}(!$model->{"get$type"}());
            $model->save();
            $this->cache->delete_all();
        }

        if (count($this->input->post('ids') > 0)) {
            $model = SProductsQuery::create()
                ->findPks($this->input->post('ids'));

            if (!empty($model)) {
                foreach ($model as $product) {
                    $product->{"set$type"}(!$product->{"get$type"}());
                    $product->save();
                }
            }
            $url = $this->input->server('HTTP_REFERER');
            $this->cache->delete_all();

            Events::create()->raiseEvent(null, 'ShopAdminProducts:ajaxChangeStatus');
            pjax($url);
        }
    }

    /**
     * @param null|integer $productId
     * @throws PropelException
     */
    public function ajaxUpdatePrice($productId = null)
    {

        if ($productId !== null) {
            $productVariant = SProductVariantsQuery::create()
                ->filterByProductId($productId);

            if ($this->input->post('variant')) {
                $productVariant = $productVariant->filterById($this->input->post('variant'));
            }

            $productVariant = $productVariant->findOne();
            $productVariant->setPriceInMain($this->input->post('price'));

            //set value in main currency
            $productVariant->setPrice($this->input->post('price'), $productVariant->getCurrency());

            $productVariant->save();
            /** Init Event. Edit Shop Product */
            Events::create()->registerEvent(
                [
                    'productId' => $productId,
                    'userId' => $this->dx_auth->get_user_id(),
                ],
                'ShopAdminProducts:edit'
            );
            Events::runFactory();

            showMessage(lang('Price updated', 'admin'));

            Currency::create()->checkPrices();
        }
    }

    public function ajaxCloneProducts()
    {

        if (count($this->input->post('ids'))) {
            $products = SProductsQuery::create()
                ->findPks($this->input->post('ids'));

            foreach ($products as $p) {
                /* @var $cloned SProducts */
                $cloned = $p->copy();
                $cloned->setName($p->getName() . lang('(copy)', 'admin'));
                $cloned->setUpdated(time());
                $cloned->setRoute(null);
                $cloned->setMetaTitle($p->getMetaTitle());
                $cloned->setMetaDescription($p->getMetaDescription());
                $cloned->setMetaKeywords($p->getMetaKeywords());
                $cloned->setFullDescription($p->getFullDescription());
                $cloned->setShortDescription($p->getShortDescription());
                $cloned->setUrl($p->getUrl() . time());
                $cloned->save();

                // Clone product variants
                $variants = SProductVariantsQuery::create()
                    ->joinWithI18n(MY_Controller::defaultLocale())
                    ->filterByProductId($p->getId())
                    ->find();

                /* @var $v SProductVariants */
                foreach ($variants as $v) {
                    $newImageName = ProductApi::getInstance()->copyProductImage($v->getMainimage());

                    /* @var $variantClone SProductVariants */
                    $variantClone = $v->copy();
                    $variantClone->clearShopKitProducts();
                    $variantClone
                        ->setProductId($cloned->getId())
                        ->setMainimage($newImageName)
                        ->save();
                }

                /* copy locale shop_products shop_product_variants
                 *
                 */

                $langs = $this->db->get('languages')->result_array();
                foreach ($langs as $lan) {

                    $productI18nOrigin = SProductsI18nQuery::create()->setComment(__METHOD__)->filterById($p->getId())->filterByLocale($lan['identif'])->findOne();
                    if ($productI18nOrigin) {
                        unset($productI18n);
                        $productI18n = SProductsI18nQuery::create()->setComment(__METHOD__)->filterById($cloned->getId())->filterByLocale($lan['identif'])->findOne();
                        if (!$productI18n) {
                            $productI18n = new SProductsI18n;
                            $productI18n->setId($cloned->getId());
                            $productI18n->setLocale($lan['identif']);
                        }
                        $productI18n->setName($productI18nOrigin->getName() . lang('(copy)', 'admin'));
                        $productI18n->setMetaTitle($productI18nOrigin->getMetaTitle());
                        $productI18n->setMetaDescription($productI18nOrigin->getMetaDescription());
                        $productI18n->setMetaKeywords($productI18nOrigin->getMetaKeywords());
                        $productI18n->setFullDescription($productI18nOrigin->getFullDescription());
                        $productI18n->setShortDescription($productI18nOrigin->getShortDescription());

                        $productI18n->save();
                    }
                    $name = [];
                    $ProductVarOrigin = SProductVariantsQuery::create()->setComment(__METHOD__)->filterByProductId($p->getId())->find();
                    if (count($ProductVarOrigin)) {
                        foreach ($ProductVarOrigin as $prodVarOrigin) {
                            $productvarI18nOrigin = SProductVariantsI18nQuery::create()->setComment(__METHOD__)->filterById($prodVarOrigin->getId())->filterByLocale($lan['identif'])->findOne();
                            if ($productvarI18nOrigin) {
                                $name[] = $productvarI18nOrigin->getName();
                            }
                        }
                    }

                    $prodId = (int)$cloned->getId();
                    $productVarIds = SProductVariantsQuery::create()->setComment(__METHOD__)->filterByProductId($prodId)->find();

                    if (count($productVarIds)) {
                        $cnt = 0;
                        foreach ($productVarIds as $prodVar) {
                            unset($productvarI18n);
                            $productvarI18n = SProductVariantsI18nQuery::create()->setComment(__METHOD__)->filterById($prodVar->getId())->filterByLocale($lan['identif'])->findOne();
                            if (!$productvarI18n) {
                                $productvarI18n = new SProductVariantsI18n;
                                $productvarI18n->setLocale($lan['identif']);
                                $productvarI18n->setId($prodVar->getId());
                            }

                            if ($name[$cnt]) {
                                $productvarI18n->setName($name[$cnt] . lang('(copy)', 'admin'));
                            } else {
                                $productvarI18n->setName('');
                            }
                            $productvarI18n->save();
                            $cnt++;
                        }
                    }
                }
                /*
                 * end copy language
                 */

                // Clone category relations
                //$cats = ShopProductCategoriesQuery::create()->setComment(__METHOD__)->joinWithI18n('ru')
                $cats = ShopProductCategoriesQuery::create()
                    ->filterByProductId($p->getId())
                    ->find();

                if (count($cats) > 0) {
                    foreach ($cats as $catClone) {
                        $CC = new ShopProductCategories();
                        $CC->setProductId($cloned->getId());
                        $CC->setCategoryId($catClone->getCategoryId());
                        $CC->save();
                    }
                }

                // Clone properties
                $props = SProductPropertiesDataQuery::create()
                    ->filterByProductId($p->getId())
                    ->find();

                if ($props->count() > 0) {
                    foreach ($props as $prop) {
                        $propClone = new SProductPropertiesData;
                        $propClone->setProductId($cloned->getId());
                        $propClone->setPropertyId($prop->getPropertyId());
                        $propClone->setValueId($prop->getValueId());
                        $propClone->save();
                    }
                }

                $cloned->save();

                // coping custom fields data
                ShopCore::app()->CustomFieldsHelper->copyProductCustomFieldsData($p->getId(), $cloned->getId());

                try {
                    Image::create()->checkOriginFolder();
                    //copying additional images
                    $additionalImages = SProductImagesQuery::create()->setComment(__METHOD__)->filterByProductId($p->getId())->find();
                    if (count($additionalImages)) {
                        foreach ($additionalImages as $img) {
                            $sourceImgPath = ShopCore::$imagesUploadPath . 'products/origin/additional/' . $img->getImageName();
                            if (file_exists($sourceImgPath)) {
                                $ext = pathinfo($sourceImgPath, PATHINFO_EXTENSION);
                                $destFileName = $cloned->getId() . '_' . $img->getPosition() . '.' . $ext;
                                $destImgName = ShopCore::$imagesUploadPath . 'products/origin/additional/' . $destFileName;
                                $copyAddImage = new SProductImages();
                                $copyAddImage->setImageName($destFileName);
                                $copyAddImage->setProductId($cloned->getId());
                                $copyAddImage->setPosition($img->getPosition());
                                $copyAddImage->save();

                                copy($sourceImgPath, $destImgName);
                            }
                        }
                        Image::create()->resizeByIdAdditional($cloned->getId());
                    }
                } catch (PropelException $e) {
                    showMessage($e->getMessage(), '', 'r');
                }

                $cloned->save();

                $message = lang('Created product clone. Id:', 'admin') . ' '
                    . $p->getId() . '. '
                    . lang('Copy product ID:', 'admin') . ' '
                    . $cloned->getId();
                $this->lib_admin->log($message);
            }

            Currency::create()->checkPrices();

            showMessage(lang('A copy was successfully created', 'admin'));
            pjax($this->input->server('HTTP_REFERER'));
        }
    }

    /**
     * Delete products
     */
    public function ajaxDeleteProducts()
    {

        $modelProduct = SProductsQuery::create()
            ->findPks($this->input->post('ids'));

        /** Init Event. Create Shop Product */
        Events::create()->registerEvent(['model' => $modelProduct, 'userId' => $this->dx_auth->get_user_id()], 'ShopAdminProducts:delete');
        Events::runFactory();

        foreach ($this->input->post('ids') as $id) {
            ProductApi::getInstance()->deleteProduct($id);
        }

        $this->lib_admin->log(lang('The product was removed', 'admin') . '. IdS: ' . implode(', ', $this->input->post('ids')));
        showMessage(lang('Deleted complete', 'admin'));
        //End. Product delete
    }

    /**
     * Show move products window
     * @param integer $categoryId
     */
    public function ajaxMoveWindow($categoryId)
    {

        $this->render(
            '_moveWindow',
            [
                'categories' => SCategoryQuery::create()->getTree(0, SCategoryQuery::create()->joinWithI18n(MY_Controller::getCurrentLocale()))->getCollection(),
                'categoryId' => $categoryId,
            ]
        );
    }

    /**
     * Move products to another category
     */
    public function ajaxMoveProducts()
    {

        $category_id = $this->input->post('categoryId');
        $newCategoryModel = SCategoryQuery::create()
            ->findPk($category_id);

        $products = SProductsQuery::create()
            ->findPks($this->input->post('ids'));

        $category_ids = [];
        foreach ($products as $product) {
            $category_ids[] = $product->getCategoryId();
        }

        $category_properties = $this->db
            ->select('property_id,category_id')
            ->where_in('category_id', $category_ids)
            ->get('shop_product_properties_categories');

        if ($category_properties) {
            $category_properties = $category_properties->result_array();

            $newCategoryProperties = $this->db
                ->select('property_id')
                ->where('category_id', $category_id)
                ->get('shop_product_properties_categories');

            if ($newCategoryProperties) {
                $newCategoryProperties = $newCategoryProperties->result_array();
                $newCategoryPropertiesArray = [];
                foreach ($newCategoryProperties as $newCatProp) {
                    $newCategoryPropertiesArray[] = $newCatProp['property_id'];
                }

                $delete_category_properties = [];
                foreach ($category_properties as $property) {
                    if (!(in_array($property['property_id'], $newCategoryPropertiesArray))) {
                        $delete_category_properties[] = $property['property_id'];
                    }
                }
                SProductPropertiesDataQuery::create()
                    ->filterByProductId($this->input->post('ids'))
                    ->filterByPropertyId($delete_category_properties)
                    ->delete();
            }
        }

        if ($newCategoryModel !== null && !empty($products)) {
            // Delete category relations
            $this->db
                ->where_in('product_id', $this->input->post('ids'))
                ->delete('shop_product_categories');

            foreach ($products as $product) {
                // Add new main category relation
                $product->setCategoryId($newCategoryModel->getId());
                $product->addCategory($newCategoryModel);
                $product->save();

                $message = lang('Product Id:', 'admin') . $product->getId() . ' ' . lang('moved in category. Category Id:', 'admin') . $newCategoryModel->getId();
                $this->lib_admin->log($message);
            }

            pjax('/admin/components/run/shop/search/index/?CategoryId=' . $category_id);
        }
    }

    /**
     * @param array $param
     * @return array|null
     */
    public function get_ids($param = null, $idProduct)
    {

        $model = SProductsQuery::create()
            ->joinWithI18n(MY_Controller::getCurrentLocale())
            ->leftJoinProductVariant();

        if (isset($param['CategoryId']) && $param['CategoryId'] > 0) {
            $model = $model->filterByCategoryId((int)$param['CategoryId']);
        }

        if (isset($param['filterID']) && $param['filterID'] > 0) {
            $model = $model->filterById((int)$param['filterID']);
        }

        if (isset($param['number']) && $param['number'] != '') {
            $model = $model->where('ProductVariant.Number = ?', $param['number']);
        }

        if (!empty($param['text'])) {
            $text = $param['text'];
            if (!strpos($text, '%')) {
                $text = '%' . $text . '%';
            }

            $model = $model->useI18nQuery($this->defaultLanguage['identif'])
                ->where('SProductsI18n.Name LIKE ?', $text)
                ->endUse()
                ->_or()
                ->where('ProductVariant.Number = ?', $text);
        }

        if (isset($param['min_price']) && $param['min_price'] > 0) {
            $model = $model->where('ProductVariant.Price >= ?', $param['min_price']);
        }

        if (isset($param['max_price']) && $param['max_price'] > 0) {
            $model = $model->where('ProductVariant.Price <= ?', $param['max_price']);
        }

        if ($param['Active'] == 'true') {
            $model = $model->filterByActive(true);
        } elseif ($this->input->get('Active') == 'false') {
            $model = $model->filterByActive(false);
        }

        if (isset($param['s'])) {
            if ($param['s'] == 'Hit') {
                $model = $model->filterByHit(true);
            }

            if ($param['s'] == 'Hot') {
                $model = $model->filterByHot(true);
            }

            if ($param['s'] == 'Action') {
                $model = $model->filterByAction(true);
            }
        }

        $queryMin = $model;
        $queryMax = clone  $model;
        $res = [];

        $res[] = $queryMin
            ->select('min_id')
            ->where('SProducts.Id > ?', $idProduct)
            ->withColumn('min(SProducts.Id)', 'min_id')
            ->findOne();
        $res[] = $idProduct;
        $res[] = $queryMax
            ->select('max_id')
            ->where('SProducts.Id < ?', $idProduct)
            ->withColumn('max(SProducts.Id)', 'max_id')
            ->findOne();

        return $res;
    }

    public function prev_next($cur, $arr = NULL)
    {

        $res = null;
        if (in_array($cur, $arr)) {
            $index_cur = array_search($cur, $arr);
            $res['prev'] = $arr[$index_cur - 1];
            $res['next'] = $arr[$index_cur + 1];
        } else {
            $res = null;
        }

        return $res;
    }

    /**
     * @param integer $id
     * В посте приходит имя картинки
     */
    public function deleteAddImage($id)
    {
        $image_name = $this->input->post('ids');  // имя картинки
        if (is_array($image_name)) {
            foreach ($image_name as $img) {
                $image = $this->db->where('product_id', $id)->where('image_name', $img)->get('shop_product_images')->row_array();
                $imageForDelete = $image['image_name'];

                Image::create()->deleteAllProductAdditionalImages($imageForDelete);

                $this->db->where('product_id', $id)->where('image_name', $img)->delete('shop_product_images');

                $this->unlinkImage($imageForDelete);
            }
        } else {
            $image = $this->db->where('product_id', $id)->where('image_name', $image_name)->get('shop_product_images')->row_array();
            $imageForDelete = $image['image_name'];

            Image::create()->deleteAllProductAdditionalImages($imageForDelete);

            $this->db->where('product_id', $id)->where('image_name', $image_name)->delete('shop_product_images');
            $this->unlinkImage($imageForDelete);

        }

    }

    /**
     * @return array
     */
    public function upload_all()
    {

        $files = [];
        $config = [];

        $config['upload_path'] = PUBPATH . 'uploads/shop/products/origin';
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['encrypt_name'] = true;
        $this->upload->initialize($config);
        foreach ($_FILES as $key => $value) {
            if (!$this->upload->do_upload($key)) {
                showMessage($this->upload->display_errors(), '', 'r');
                exit;
            } else {
                $result = ['upload_data' => $this->upload->data()];
                $files[$key] .= $result['upload_data']['file_name'];
            }
        }
        foreach ($files as $k => $value) {
            if (preg_match('/userFile/i', $k)) {
                unlink(PUBPATH . 'uploads/shop/products/origin/' . $value);
            }
        }

        return $files;
    }

    /**
     * @return array
     */
    public function upload_all_additionalImages()
    {

        $files = [];

        $this->upload->initialize(
            [
                'upload_path' => PUBPATH . 'uploads/shop/products/origin/additional',
                'allowed_types' => 'gif|jpg|jpeg|png',
                'encrypt_name' => true,
            ]
        );

        foreach ($_FILES as $key => $value) {
            if (!$this->upload->do_upload($key)) {
                showMessage($this->upload->display_errors(), '', 'r');
                exit;
            } else {
                $result = ['upload_data' => $this->upload->data()];
                $matches = [];
                preg_match('/[\d]+/', $key, $matches);
                if (strstr($key, 'additionalImages')) {
                    $files[$matches[0]] .= $result['upload_data']['file_name'];
                } else {
                    unlink(PUBPATH . 'uploads/shop/products/origin/additional/' . $result['upload_data']['file_name']);
                }
            }
        }
        return $files;
    }

    /**
     *
     */
    public function fastCategoryCreate()
    {

        $post = $this->input->post();

        if ($post['name']) {
            $locale = $post['locale'] ?: MY_Controller::defaultLocale();
            $data = [
                'name' => $post['name'],
                'parent_id' => (int)$post['parent_id'],
                'active' => 1,
                'show_in_menu' => 1,
            ];

            if ($model = CategoryApi::getInstance()->addCategory($data, $locale)) {
                $message = lang('Category created', 'admin');
                $categories = SCategoryQuery::create()->getTree(0, SCategoryQuery::create()->joinWithI18n($locale))->getCollection();
                $categories = $this->render('categories_selector', ['categories' => $categories, 'selected_id' => $model->getId()], TRUE);
                echo json_encode(['success' => TRUE, 'message' => $message, 'categories' => $categories]);
            } else {
                $message = CategoryApi::getInstance()->getError();
                echo json_encode(['success' => FALSE, 'message' => $message]);
            }
        } else {
            $message = lang('Can not create category without name.', 'admin');
            echo json_encode(['success' => FALSE, 'message' => $message]);
        }
    }

    /**
     *
     */
    public function fastBrandCreate()
    {

        $post = $this->input->post();

        if ($post['name']) {

            $this->load->helper('translit');
            $data = [
                'url' => translit_url($post['name']),
                'image' => '',
                'position' => 0,
                'created' => time(),
                'updated' => time(),
            ];

            /** Check if brand URL is aviable. * */
            $urlCheck = SBrandsQuery::create()
                ->where('SBrands.Url = ?', $data['url'])
                ->findOne();

            if ($urlCheck !== null) {
                echo json_encode(['success' => FALSE, 'message' => lang('This URL is already in use', 'admin')]);
                return;
            }

            $this->db->insert('shop_brands', $data);
            $brand_id = $this->db->insert_id();
            $data_i18n = [
                'id' => $brand_id,
                'name' => $post['name'],
                'locale' => MY_Controller::defaultLocale(),
            ];

            if ($this->db->insert('shop_brands_i18n', $data_i18n)) {
                $message = lang('Brand created', 'admin');

                $brands = SBrandsQuery::create()
                    ->joinWithI18n(MY_Controller::defaultLocale(), Criteria::JOIN)
                    ->orderByPosition();
                $offset = 0;
                $per_page = 10000;
                $brands = $brands
                    ->orderById(Criteria::DESC)
                    ->distinct()
                    ->limit($per_page)
                    ->offset((int)$offset)
                    ->find();

                $brands = $this->render('brands_selector', ['brands' => $brands, 'selected_id' => $brand_id], TRUE);
                echo json_encode(['success' => TRUE, 'message' => $message, 'brands' => $brands]);
            } else {
                $message = lang('Can not create brand without name.', 'admin');
                echo json_encode(['success' => FALSE, 'message' => $message]);
            }
        } else {
            $message = lang('Can not create brand without name.', 'admin');
            echo json_encode(['success' => FALSE, 'message' => $message]);
        }
    }

    /**
     * @param int $countImages
     * @return array
     * @throws Exception
     */
    public function saveNewAdditionalPhoto($countImages)
    {

        ++$countImages;

        $additional = [];
        $field = 'userFile';
        $newFiles = [];
        $count = count($_FILES[$field]['name']);
        for ($i = 0; $i < $count; $i++) {
            $oneFileData = [];

            /** Название картинки конвертимруеться в md5 $name */
            $name = explode('.', $_FILES[$field]['name'][$i]);
            $name[0] = md5($name[0]);

            $_FILES[$field]['name'][$i] = implode('.', $name);

            foreach ($_FILES[$field] as $assocKey => $fileDataArray) {
                $oneFileData[$assocKey] = $fileDataArray[$i];
            }
            $newFiles[$field . '_' . $i] = $oneFileData;
        }

        $_FILES = $newFiles;

        $config['upload_path'] = PUBPATH . 'uploads/shop/products/origin/additional';
        $config['allowed_types'] = '*';

        $this->load->library('upload')->initialize($config);

        foreach ($_FILES as $key => $value) {

            if (!$this->upload->do_upload($key)) {
                showMessage($this->upload->display_errors(), '', 'r');
                exit;
            } else {
                $result = ['upload_data' => $this->upload->data()];
                $test[] = $result['upload_data'];
            }
        }

        foreach ($test as $item) {
            $additional[$countImages] = $item['file_name'];
            ++$countImages;
        }

        return $additional;

    }

    /**
     * @param int $id
     * @return void
     */
    public function changeImagePosition($id)
    {

        $position = $this->input->post('positions');

        foreach ($position as $key => $item) {
            $this->db
                ->where('product_id', $id)
                ->where('image_name', $item)
                ->update('shop_product_images', ['position' => $key]);
        }

        showMessage(lang('Positions updated', 'admin'));
        $this->cache->delete_all();
    }

}