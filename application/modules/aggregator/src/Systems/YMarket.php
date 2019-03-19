<?php namespace aggregator\src\Systems;

use aggregator\src\Aggregator;
use aggregator\src\DataProvider;

class YMarket extends Aggregator
{

    /**
     * @var array
     */
    private $offerNodes = [
        'currencyId' => 'currencyId',
        'categoryId' => 'categoryId',
        'name' => 'name',
        'vendor' => 'vendor',
        'vendorCode' => 'vendorCode',
        'description' => 'description',
        'cpa' => 'cpa',
    ];

    /**
     * YMarket constructor.
     * @param DataProvider $dataProvider
     */
    public function __construct(DataProvider $dataProvider)
    {
        parent::__construct($dataProvider);
        $this->name = lang('Яндекс маркет', 'aggregator');
        $this->id = 'ymarket';
    }

    public function getProductViewFields()
    {

        $month = [

            'false' => 'нет',
            'true' => 'есть',
            'P1M' => 1,
            'P2M' => 2,
            'P3M' => 3,
            'P6M' => 6,
            'P9M' => 9,
            'P1Y' => 12,
            'P1Y6M' => 18,
            'P2Y' => 24,
            'P2Y6M' => 30,
            'P3Y' => 36,
            'P3Y6M' => 42,
            'P4Y' => 48,
        ];
        return [
            'country_of_origin' => [
                'name' => 'country_of_origin',
                'label' => lang('Сountry of product manufacture', 'aggregator'),
                'type' => 'product_select',
                'options' => $this->dataProvider->getCountries(),
            ],
            'manufacturer_warranty' => [
                'name' => 'manufacturer_warranty',
                'label' => lang('Manufacturer warranty, months', 'aggregator'),
                'type' => 'product_select',
                'options' => $month,
            ],
            'seller_warranty' => [
                'name' => 'seller_warranty',
                'label' => lang('Seller warranty, months', 'aggregator'),
                'type' => 'product_select',
                'options' => $month,
            ],

        ];
    }

    public function getModuleViewFields()
    {
        return [
            'brands' => [
                'name' => 'brands',
                'multiple' => true,
                'label' => lang('Brands', 'aggregator'),
                'type' => 'select',
                'options' => $this->dataProvider->getBrands(),

            ],
            'categories' => [
                'name' => 'categories',
                'multiple' => true,
                'label' => lang('Categories', 'aggregator'),
                'type' => 'select',
                'options' => $this->dataProvider->getCategoriesOptions(),

            ],
            'adult' => [
                'name' => 'adult',
                'label' => lang('Adult products', 'aggregator'),
                'type' => 'checkbox',

            ],
            'apply_discount' => [
                'name' => 'apply_discount',
                'label' => lang('apply discount', 'aggregator'),
                'type' => 'checkbox',

            ],

        ];
    }

    public function generateXml()
    {
        /* create a dom document with encoding utf8 */
        $dom = new \DOMDocument('1.0', 'utf-8');

        /* create the root element of the xml tree */
        $rootNode = $dom->createElement('yml_catalog');
        $rootNode->setAttribute('date', date('Y-m-d H:i'));
        $dom->appendChild($rootNode);

        $shopNode = $rootNode->appendChild($dom->createElement('shop'));
        $siteInfo = $this->dataProvider->getSiteInfo();

        $shopNode->appendChild($dom->createElement('name', $siteInfo['site_short_title']));
        $shopNode->appendChild($dom->createElement('company', $siteInfo['site_title']));
        $shopNode->appendChild($dom->createElement('url', $siteInfo['base_url']));
        $shopNode->appendChild($dom->createElement('platform', 'ImageCMS'));
        $shopNode->appendChild($dom->createElement('version', $siteInfo['imagecms_number']));
        $shopNode->appendChild($dom->createElement('email', $siteInfo['siteinfo_adminemail']));

        $currencies = $this->dataProvider->getCurrencies();

        $currenciesNode = $dom->createElement('currencies');

        $shopNode->appendChild($currenciesNode);

        foreach ($currencies as $currency) {
            $currencyNode = $dom->createElement('currency');
            $currencyNode->setAttribute('id', $currency['code']);
            $currencyNode->setAttribute('rate', $currency['rate']);
            $currenciesNode->appendChild($currencyNode);
        }

        $categories = $this->dataProvider->getCategories(false, $this->getConfigItem('categories'));

        $categoriesNode = $dom->createElement('categories');
        $shopNode->appendChild($categoriesNode);

        foreach ($categories as $category) {
            $categoryNode = $dom->createElement('category', $category['Name']);
            $categoryNode->setAttribute('id', $category['Id']);
            $parentId = $category['ParentId'];
            if ($parentId) {
                $categoryNode->setAttribute('parentId', $category['ParentId']);
            }
            $categoriesNode->appendChild($categoryNode);
        }

        $products = $this->dataProvider->getProducts($this->getConfigItem('categories'), $this->getConfigItem('brands'), $this->getId());

        $productsNode = $dom->createElement('offers');
        $shopNode->appendChild($productsNode);

        foreach ($products as $id => $product) {
            $productNode = $dom->createElement('offer');
            $productNode->setAttribute('id', $id);
            $productNode->setAttribute('available', $product['quantity'] > 0 ? 'true' : 'false');

            $productNode->appendChild($dom->createElement('url', $product['url']));

            $discount = 0;
            if ($this->getConfigItem('apply_discount') == 'on') {
                $discount = $this->dataProvider->getDiscount(
                    $product['product_id'],
                    $product['categoryId'],
                    $product['vendor_id'],
                    $product['id'],
                    $product['price']
                );
            }

            if ($discount > 0) {
                $productNode->appendChild($dom->createElement('oldprice', (float)$product['price']));
                $productNode->appendChild($dom->createElement('price', (float)($product['price'] - $discount)));
            } else {
                if ($product['old_price'] > 0) {
                    $productNode->appendChild($dom->createElement('oldprice', (float)$product['old_price']));
                }
                $productNode->appendChild($dom->createElement('price', (float)$product['price']));
            }

            foreach ($this->offerNodes as $input => $output) {
                if (array_key_exists($input, $product)) {
                    $productNode->appendChild($dom->createElement($output, $product[$input]));
                }
            }

            if ($product['picture']) {
                foreach ($product['picture'] as $picture) {
                    $productNode->appendChild($dom->createElement('picture', $picture));
                }
            }

            if ($product['param']) {
                foreach ($product['param'] as $param) {
                    $paramNode = $dom->createElement('param', $param['value']);
                    $paramNode->setAttribute('name', $param['name']);
                    $productNode->appendChild($paramNode);
                }
            }

            $prodParams = $this->dataProvider->getProductConfig($this->getId(), $product['product_id']);

            foreach ($prodParams as $key => $prodParam) {

                if (in_array($key, ['country_of_origin', 'manufacturer_warranty', 'seller_warranty'])) {
                    $productNode->appendChild($dom->createElement($key, $prodParam));

                }
            }

            if ($this->getConfigItem('adult') == 'on') {
                $productNode->appendChild($dom->createElement('adult', 'true'));
            }

            $productsNode->appendChild($productNode);
        }

        header('content-type: text/xml');
        echo $dom->saveXML();
    }

}