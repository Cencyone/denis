<?php

namespace Search;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use SCategoryQuery;
use ShopController;
use ShopCore;
use SProducts;
use SProductsQuery;
use SProductsWordsQuery;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Shop Controller
 *
 * @uses \ShopController
 * @package Shop
 * @copyright ImageCMS (c) 2016, <s.knysh@imagecms.net>
 */
class BaseSearch extends ShopController
{

    const SEARCH_ALL = 1;
    const SEARCH_NAME = 2;
    const SEARCH_LIMIT = 3;

    /**
     * @var  string  Категории
     */
    public $category;

    /**
     * @var int Время жизни скрипта кеша
     */
    private $live_cache_script;

    /**
     * @var array
     */
    public $correct;

    /**
     * @var bool Поиск по артикулу
     */
    public $findNumber = false;

    /**
     * @var
     */
    public $locate;

    /**
     * @var int Количество товаров на странице
     */
    public $offset;

    /**
     * @var string  Сортировка
     */
    public $orderBy;

    /**
     * @var int Страницы пагинации
     */
    public $per_page;

    /**
     * @var SProductsQuery поиск с учетом росстояния Левенштейна
     */
    public $relativeSearch;

    /**
     * @var int Настройки поиска
     */
    public $searchSetting;

    /**
     * @var  array слова транслитерации,
     * где ключ русское слово(исходное) значение транслитерация
     */
    private $words_transit = [];

    /**
     * Временное хранение коректных слова
     * @var array
     */
    private $time_correct_word;

    /**
     * @var int Количество товаров всего
     */
    public $total_row;

    /**
     * @var bool єсли не находит имя ищет по варианту
     */
    public $variantName;

    public function __construct() {
        $this->load->helper('translit');
        $this->setSearchSetting();
        $this->setLiveCacheScript();

        lang('Бренды');
        parent::__construct();

    }

    /**
     * @param string $word
     * @param string $locale
     * @return array|mixed|ActiveRecordInterface[]|ObjectCollection|SProducts[]
     */
    public function getAutoCompProduct($word, $locale) {

        $res = $this->getProducts($word, $locale)
            ->limit(5)
            ->find();

        return $res;
    }

    /**
     * @param string $product
     * @param string $locate
     * @return SProductsQuery
     */
    public function getProducts($product, $locate = null) {

        $this->setLocate($locate);

        if ($this->getSearchSetting() == self::SEARCH_LIMIT) {

            $words = explode(' ', $product);

        } else {

            $productsName = mb_strtolower($product);
            $words = preg_split('/[\W_]/iu', $productsName);
        }

        $words = array_unique($words);

        $newWord = $this->splitWords($words);
        $this->findWords($newWord['singlWord']);
        return $this->getRelativeSearch();

    }

    /**
     * 1 - Поиск по имени товара и имени варианта , артикулу
     * 2 - Поиск по имени товара , потом по имени , после чего по артикулу.
     * 3 - Точный поиск через конструкцию LIKE
     * @return int
     */
    public function getSearchSetting() {

        return $this->searchSetting;
    }

    /**
     * @return void
     */
    public function setSearchSetting() {

        $this->searchSetting = ShopCore::app()->SSettings->getSearchName() ?: 2;
    }

    /**
     * @param array $words
     * @return array
     */
    public function splitWords(array $words) {

        foreach ($words as $word) {

            $word = trim($word);

            if ($word) {

                $newWords[] = $word;
                $wordsWithStar[] = $word . '*';

            }
        }

        $product = implode(' ', $wordsWithStar);
        $singleSearch = implode('%', $newWords);

        return [
                'morphin'      => $product ?: ' ',
                'singlWord'    => $newWords ?: ' ',
                'singleSearch' => $singleSearch ?: ' ',
               ];
    }

    /**
     * @param $word
     * @throws PropelException
     * @return void
     */
    public function findWords($word) {
        /** Cache */
        if ($this->getCache()->contains('search_words_keys')) {

            $all_words = $this->getCache()->fetch('search_words_keys');

        } else {

            $all_words = SProductsWordsQuery::create()
                ->select(['name'])
                ->findByLocal($this->getLocate())
                ->toArray();

            $this->getCache()->save('search_words_keys', $all_words, config_item('cache_ttl'));
        }

        $count = count($all_words);
        $i = 0;

        /** Создает масив где ключ русское слово(исходное) а значение транслитерация */
        while ($i < $count) {
            $word_translit[$all_words[$i]] = translit($all_words[$i]);

            unset($all_words[$i]);
            ++$i;
        }
        unset($count);

        $this->setWordsTransit($word_translit);
        $this->startSortOutWords($word);
        $this->searchRelative(array_unique($this->getCorrect()));

    }

    /**
     * @param array $words слова введенные юзером
     *
     * @return void
     */
    public function startSortOutWords($words) {

        $count_words = count($words);

        /** Перебор введенних слов $words[$x] */
        for ($x = 0; $x < $count_words; $x++) {

            if (mb_strlen($words[$x]) >= 4) {

                $this->bustWords($words[$x], $x);

            } else {
                $this->setCorrect($words[$x]);
            }
        }
        unset($count_words);
    }

    /**
     * Перебирает каждое введенное слово с вариантами транслитерации
     * @param $word
     * @param integer $word_number
     * @return void
     */
    private function bustWords($word, $word_number = null) {

        $words_transit = $this->getWordsTransit();

        $countCorrect = count($this->getCorrect());

        if (array_key_exists($word, $words_transit)) {

            $this->setCorrect($word);
        } else {

            $my_word = translit($word);

            foreach ($words_transit as $key => $item) {

                if ($this->getSearchSetting() == self::SEARCH_LIMIT) {

                    $this->distanceLivenshtainSingleLimitSearch($my_word, $item, $key, $word_number);

                } else {

                    $this->distanceLivenshtineSearch($my_word, $item, $key);
                }
                unset($item, $key);
            }
            unset($my_word);

        }

        $this->checkCorrectWords($countCorrect, $word);
    }

    /**
     * @return array
     */
    public function getCorrect() {

        return $this->correct;
    }

    /**
     * @param string $correct
     * @return void
     */
    public function setCorrect($correct) {

        if ($correct) {
            $this->correct[] = $correct;
        }
    }

    /**
     * @param string $my_word
     * @param string $item
     * @param string $key
     * @param integer $word_number
     * @return void
     *
     * Если метафон слова больше половины, его по расстоянию левенштейна запускаем вторую проверку,
     * где расстояние в слове не превишает " N < 2" для болие точного поиска может отличаться только одна буква
     *
     */
    private function distanceLivenshtainSingleLimitSearch($my_word, $item, $key, $word_number) {

        if (levenshtein(metaphone($my_word), metaphone($item)) < mb_strlen(metaphone($my_word)) / 2) {

            if (levenshtein($my_word, $item) < 2) {

                if (levenshtein($my_word, $item) < 1) {

                    $this->setCorrect($key);

                } else {

                    /** В етом поиске должно быть только одно корректное слово */
                    if (!$this->time_correct_word[$word_number]) {
                        $this->time_correct_word[$word_number] = $key;

                        $this->setCorrect($key);

                    }
                }
            }
        }

    }

    /**
     * @param string $my_word
     * @param string $item
     * @param string $key
     * @return void
     *
     * Если метафон слова больше половины, по расстоянию левенштейна его запускаем вторую проверку,
     * где расстояние в слове не превишает " N / 2 /2"
     */
    private function distanceLivenshtineSearch($my_word, $item, $key) {

        if (levenshtein(metaphone($my_word), metaphone($item)) <= mb_strlen(metaphone($my_word)) / 2) {

            /** Заменив в конце условия / 2 -1 увеличитса диапазон поиска  */
            if (levenshtein($my_word, $item) <= mb_strlen($my_word) / 2 / 2) {
                $this->setCorrect($key);
            }
        }
    }

    /**
     * @param int $countCorrect
     * @param string $word
     *
     * Записивает слово которое было вписано изначально,
     * если количество строк масива $correct не изменилось
     */
    private function checkCorrectWords($countCorrect, $word) {

        if ($countCorrect == count($this->getCorrect())) {
            $this->setCorrect($word);
        }
    }

    /**
     * Проверка настроек поиска
     *
     * Сначало вызывается метод Query_words,
     * если поиск не дал результатов идет поиск по имени варианта, если $this->getSearchSetting стоит 2
     * после чего если пустая  переменная $check, идет поиск по артикулу
     *
     * @param array|string $correct_word
     * @return void
     */
    public function searchRelative($correct_word) {

        if ($this->getSearchSetting() == self::SEARCH_LIMIT) {

            $product = $this->splitWords($correct_word)['singleSearch'];
            $res = $this->likeQuery($product);

        } else {
            $product = $this->splitWords($correct_word)['morphin'];
            $res = $this->Query_Words($product);

            $check = $this->countProducts($res) ?: false;
            if (!$check) {
                if (!$this->getVariantName() && $this->getSearchSetting() == self::SEARCH_NAME) {

                    $this->setVariantName(true);
                    $res = $this->Query_Words($product);
                }

                $check = $this->countProducts($res) ?: false;

                if (!$check) {

                    $this->setVariantName(false);
                    $this->searchSetting = false;
                    $this->setFindNumber(true);
                    $res = $this->Query_Words($product);
                }

            }

        }

        $this->setRelativeSearch($res);
    }

    /**
     * @param string $word
     * @return SProductsQuery
     */
    public function likeQuery($word) {

        $locate = $this->getLocate() ?: 'ru';

        $orderBy = $this->getOrderBy();

        $res = SProductsQuery::create()
            ->distinct()
            ->filterByActive(1)
        //            ->withColumn('IF(sum(shop_product_variants.stock) > 0, 1, 0)', 'allstock')
            ->joinI18n($locate, '', Criteria::RIGHT_JOIN)
            ->joinMainCategory('', Criteria::RIGHT_JOIN)
            ->useMainCategoryQuery()
            ->filterByActive(1)
            ->endUse()
            ->joinProductVariant()
            ->useProductVariantQuery()
            ->joinI18n($locate, '', Criteria::INNER_JOIN)
            ->endUse()
            ->condition('numberCondition', 'ProductVariant.Number LIKE ?', '%' . $word . '%')
            ->condition('nameCondition', 'SProductsI18n.Name LIKE ?', '%' . $word . '%')
            ->condition('nameVariantCondition', 'SProductVariantsI18n.Name LIKE ?', '%' . $word . '%')
            ->where(['numberCondition', 'nameCondition', 'nameVariantCondition'], Criteria::LOGICAL_OR)
        //            ->groupBy('shop_products_i18n.id')
        //            ->orderBy('allstock', Criteria::DESC)
            ->orderBy('shop_product_variants.stock', Criteria::DESC)
            ->_if($orderBy)
            ->globalSort($orderBy)
            ->_endif();

        return $res;

    }

    /**
     * @return mixed
     */
    public function getLocate() {

        return $this->locate ?: 'ru';
    }

    /**
     * @param string $locate
     */
    public function setLocate($locate) {

        $this->locate = $locate;
    }

    /**
     * @return string
     */
    public function getOrderBy() {

        if ($this->getSearchSetting() == self::SEARCH_LIMIT) {
            return $this->orderBy;
        }

        return $this->orderBy ?: 'rel';
    }

    /**
     * @param $orderBy
     */
    public function setOrderBy($orderBy) {

        $this->orderBy = $orderBy;

    }

    /**
     * В самом начале сетается $this->searchSetting где указан параметр поиска,
     * если 1 тогда поиск идет по имени товара и варианта в одной строке
     * Если параметр 2 тогда поиск ведется отдельно по имени товара и отдельно по имени варианта.
     * @param string $word
     * @param string $locate
     * @return SProductsQuery
     */
    public function Query_Words($word, $locate = 'ru') {

        $orderBy = $this->getOrderBy();

        $findNumber = $this->getFindNumber();

        $locate = $this->getLocate() ?: $locate;
        $variant = $this->getVariantName();

        /** @var SProductsQuery $res
         *
         * $findNumber - повторно заходит в метод когда не было найдено результатов в имени товара и имени варианта,
         * и ишет по Артикулу товара
         * $variant  - повторно заходит в метод , когда настройка поиска выбрана по имени и отдельно по имени варианта,
         * и ишет по имени варианта
         *
         * $this->getSearchSetting - настройка поиска, варианты могут быть:
         *  - поиск сразу по имени товара и имени варианта,так поиск работает дольше в 2 раза .
         *  - И может быть поиск по имени товара, если не находит ищет в имени варианта,
         * и после етого ишет по артикулу товара
         *
         * По возможности заменить строку  ->withColumn('IF(sum(shop_product_variants.stock) > 0, 1, 0)', 'allstock')
         * ето сортировка по наличию товара,  замедляет поиск на 40 %
         *
         * Метод возвращает SProductsQuery - гдле дальше на него налаживаются фильтры если єто основной поиск,
         * в случае если єто  вызывается из метода Автокомплита тогда делается лимит и find()
         */
        $res = SProductsQuery::create()
            ->distinct()
            ->filterByActive(1)
            ->orderByArchive(Criteria::ASC)
            ->_if($findNumber)
            ->withColumn("MATCH(shop_product_variants.number) AGAINST('$word' IN BOOLEAN MODE)", 'rel')
            ->_elseif($variant)
            ->withColumn("MATCH(shop_product_variants_i18n.name) AGAINST('$word' IN BOOLEAN MODE)", 'rel')
            ->_elseif($this->getSearchSetting() == self::SEARCH_ALL && !$findNumber)
            ->withColumn("MATCH(shop_products_i18n.name) AGAINST('$word' IN BOOLEAN MODE)", 'rel1')
            ->withColumn("max(MATCH(shop_products_i18n.name , shop_product_variants_i18n.name , shop_product_variants.number) AGAINST('$word' IN BOOLEAN MODE))", 'rel')
            ->_else()
            ->withColumn("MATCH(shop_products_i18n.name) AGAINST('$word' IN BOOLEAN MODE)", 'rel')
            ->_endif()
            ->withColumn('IF(sum(shop_product_variants.stock) > 0, 1, 0)', 'allstock')
            ->joinI18n($locate, '', Criteria::RIGHT_JOIN)
            ->joinMainCategory('', Criteria::RIGHT_JOIN)
            ->useMainCategoryQuery()
            ->filterByActive(1)
            ->endUse()
            ->joinProductVariant()
            ->useProductVariantQuery()
            ->joinI18n($locate, '', Criteria::INNER_JOIN)
            ->endUse()
            ->_if($findNumber)
            ->where("MATCH(shop_product_variants.number) AGAINST('$word' IN BOOLEAN MODE)")
            ->_elseif($variant)
            ->where("MATCH(shop_product_variants_i18n.name) AGAINST('$word' IN BOOLEAN MODE)")
            ->_elseif($this->getSearchSetting() == self::SEARCH_ALL && !$findNumber)
            ->where("MATCH(shop_products_i18n.name, shop_product_variants_i18n.name, shop_product_variants.number) AGAINST('$word' IN BOOLEAN MODE)")
            //                ->condition('nameCondition', "MATCH(shop_products_i18n.name) AGAINST('$word' IN BOOLEAN MODE)")
            //                ->condition('VariantNameCondition', "MATCH(shop_products_i18n.name, shop_product_variants_i18n.name) AGAINST('$word' IN BOOLEAN MODE)")
            //                ->where(['nameCondition', 'VariantNameCondition'], Criteria::LOGICAL_OR)
            ->_else()
            ->where("MATCH(shop_products_i18n.name) AGAINST('$word' IN BOOLEAN MODE)")
            ->_endif()
            ->groupBy('shop_products_i18n.id')
            ->orderBy('allstock', Criteria::DESC)

            ->_if($this->getSearchSetting() == self::SEARCH_ALL && !$findNumber)
            ->orderBy('rel1', Criteria::DESC)
            ->_endif()
            ->globalSort($orderBy);

        return $res;
    }

    /**
     * @return bool
     */
    public function getFindNumber() {

        return $this->findNumber ?: false;
    }

    /**
     * @param bool $findNumber
     */
    public function setFindNumber($findNumber) {

        $this->findNumber = $findNumber;
    }

    /**
     * @return bool
     */
    public function getVariantName() {

        return $this->variantName ?: false;
    }

    /**
     * @param bool $variantName Сетается при повторном поиске если не найдено имя товара
     */
    public function setVariantName($variantName) {

        $this->variantName = $variantName;
    }

    /**
     * @param SProductsQuery $data
     * @return int
     */
    public function countProducts($data) {

        return $data->find()->count();
    }

    /**
     * @return mixed
     */
    public function getRelativeSearch() {

        return $this->relativeSearch;
    }

    /**
     * @param SProductsQuery $relativeSearch
     */
    public function setRelativeSearch($relativeSearch) {

        $this->relativeSearch = $relativeSearch;
    }

    /**
     * @param string $string_query
     * @param string $locale
     * @return array
     */
    public function getIndexProduct($string_query, $locale = 'ru') {

        $this->setLocate($locale);

        $data = $this->getProducts($string_query, $this->getLocate());

        $products = clone $data;

        $products = $this->getProductsByFilters($products);

        $data = $data->find();

        $res = [
                'products'      => $products,
                'totalProducts' => $this->getTotalRow(),
                'categories'    => $this->getProductsCategories($data),
               ];

        return $res;

    }

    /**
     * @param SProductsQuery $data
     * @return ObjectCollection
     */
    public function getProductsByFilters($data) {

        /** @var SProductsQuery $data */
        $data
            ->_if($this->getCategory())
            ->filterByCategoryId($this->getCategory())
            ->_endif();

        $this->setTotalRow($this->countProducts($data));

        $data = $data
            ->_if($this->getOffset())
            ->offset((int) $this->getOffset())
            ->_endif()
            ->_if($this->getPerPage())
            ->limit($this->getPerPage())
            ->_endif()
            ->find();
        return $data;

    }

    /**
     * @return string
     */
    public function getCategory() {

        return $this->category;
    }

    /**
     * @param $category
     */
    public function setCategory($category) {

        $this->category = $category;
    }

    /**
     * @return int
     */
    public function getOffset() {

        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset) {

        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getPerPage() {

        return $this->per_page;
    }

    /**
     * @param int $per_page
     */
    public function setPerPage($per_page) {

        $this->per_page = $per_page;
    }

    /**
     * @return int
     */
    public function getTotalRow() {

        return $this->total_row;
    }

    /**
     * @param int $total_row
     */
    public function setTotalRow($total_row) {

        $this->total_row = $total_row;
    }

    /**
     * @param ObjectCollection $data
     * @return mixed
     */
    public function getProductsCategories(ObjectCollection $data) {

        $ret = [];

        /** @var SProducts $obj */
        foreach ($data as $obj) {
            $id = $obj->getCategoryId();

            if (array_key_exists($id, $ret)) {
                $ret[$id]['count']++;
            } else {
                $ret[$id]['count'] = 1;
                $ret[$id]['category'] = $obj->getMainCategory();

            }
            unset($obj);
        }

        uasort(
            $ret,
            function ($a, $b) {

                return $a['category']->getPosition() > $b['category']->getPosition();
            }
        );
        $re = array_keys($ret);

        /** @var SProducts $item */
        foreach ($re as $item) {
            $parent = $this->setTotalCategories($item);
            $res[$parent['id']][$parent['name']][] = [
                                                      'id'    => $item,
                                                      'name'  => $ret[$item]['category']->getName(),
                                                      'count' => $ret[$item]['count'],
                                                     ];

            unset($item);
        }
        unset($re);

        return $res;
    }

    /**
     * @param int $category
     * @return array
     */
    public function setTotalCategories($category) {

        $test = SCategoryQuery::create()
            ->findById($category);

        foreach ($test as $item) {
            $parent_id = $item->getParentId();

            if (!$parent_id || $parent_id == 0) {
                $res = [
                        'id'   => $item->getId(),
                        'name' => $item->getName(),
                       ];
                unset($item);
                return $res;
            } else if ($parent_id > 0) {
                $te = $this->setTotalCategories($parent_id);
                unset($item);
                return $te;
            }
            unset($item);
        }
    }

    /**
     * @param array $get_param
     * @return void
     */
    public function setGet_Param($get_param) {

        $this->setCategory($get_param['category']);
        $get_param['order'] = ($get_param['order'] != 'none') ? $get_param['order'] : 'rel';
        $this->setOrderBy($get_param['order']);
        $this->setPerPage($get_param['user_per_page']);
        $this->setOffset($get_param['per_page']);

    }

    /**
     * INSERT unique name products in databases
     * @return void
     */
    public function indexationWordProduct() {

        $all_lang = $this->db->select('identif')
            ->get('languages')
            ->result_array();

        SProductsWordsQuery::create()
            ->deleteAll();

        foreach ($all_lang as $lang) {

            $this->setLocate($lang['identif']);

            $res = SProductsQuery::create()
                ->select(['shop_products_i18n.name', 'shop_product_variants_i18n.name'])
                ->distinct()
                ->joinI18n($this->getLocate())
                ->joinMainCategory()
                ->useMainCategoryQuery()
                ->filterByActive(1)
                ->endUse()
                ->joinProductVariant()
                ->useProductVariantQuery()
                ->joinI18n($this->getLocate())
                ->endUse()
                ->filterByActive(1)
                ->find()
                ->toArray();

            $all_word = [];
            $x = count($res);
            $i = 0;

            while ($i < $x) {
                $all_word[] = $res[$i]['shop_products_i18n.name'];
                $all_word[] = $res[$i]['shop_product_variants_i18n.name'];
                unset($res[$i]);
                ++$i;
            }

            $this->createNewProductWords(array_unique($all_word));
            unset($all_word, $lang);

        }
    }

    /**
     *
     * Create array with unique words , where str length >= 4
     * @param string $product
     * @return void
     */
    public function createNewProductWords($product) {

        $words = [];
        foreach ($product as $products) {
            $word = preg_split('/[\W_]/iu', $products);

            foreach ($word as $value) {
                if (mb_strlen($value) >= 4) {

                    $value = mb_strtolower($value);
                    $words[] = $value;
                }
                unset($value);
            }
            unset($products);
        }
        $unique_words = array_unique($words);
        unset($words);

        if ($unique_words) {
            $this->setNewWordsProduct($unique_words);
            unset($unique_words);

        }
    }

    /**
     * @param array|string $words
     * @return void
     */
    public function setNewWordsProduct($words) {
        $locate = $this->getLocate();
        if (is_array($words)) {
            foreach ($words as $item) {
                $sql = "INSERT IGNORE INTO `shop_products_words`(`name` , `local`) VALUES ('$item' ,'$locate')";
                $this->db->query($sql);
                unset($item);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getWordsTransit() {

        return $this->words_transit;
    }

    /**
     * @param mixed $words_transit
     */
    public function setWordsTransit($words_transit) {

        $this->words_transit = $words_transit;
    }

    /**
     * @return int
     */
    public function getLiveCacheScript() {

        return $this->live_cache_script;
    }

    /**
     * @return void
     */
    public function setLiveCacheScript() {

        $this->live_cache_script = config_item('cache_ttl') / 10;
    }

}

/* End of file search.php */