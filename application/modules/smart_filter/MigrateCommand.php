<?php namespace smart_filter;

use CI_DB_active_record;
use Selenium\Pattern;
use smart_filter\models\SFilterPattern;
use smart_filter\src\Admin\PatternHandler;
use Symfony\Component\Config\Definition\Exception\Exception;

class MigrateCommand
{

    /**
     * Variables to replace
     */
    private $fromTo = [
                       '%ID%'         => '{{category.id}}',
                       '%name%'       => '{{category.name}}',
                       '%desc%'       => '{{category.description}}',
                       '%brands%'     => '',
                       '%maxPrice%'   => '{{maxPrice}}',
                       '%minPrice%'   => '{{minPrice}}',
                       '%name[1]%'    => '{{category.name|morphy(1)}}',
                       '%name[2]%'    => '{{category.name|morphy(2)}}',
                       '%name[3]%'    => '{{category.name|morphy(3)}}',
                       '%name[4]%'    => '{{category.name|morphy(4)}}',
                       '%name[5]%'    => '{{category.name|morphy(5)}}',
                       '%name[6]%'    => '{{category.name|morphy(6)}}',

                       '%name[1][t]%' => '{{category.name|morphy(1)|translit}}',
                       '%name[2][t]%' => '{{category.name|morphy(2)|translit}}',
                       '%name[3][t]%' => '{{category.name|morphy(3)|translit}}',
                       '%name[4][t]%' => '{{category.name|morphy(4)|translit}}',
                       '%name[5][t]%' => '{{category.name|morphy(5)|translit}}',
                       '%name[6][t]%' => '{{category.name|morphy(6)|translit}}',

                      ];

    /**
     * @var CI_DB_active_record
     */
    private $db;

    /**
     * @var PatternHandler
     */
    private $patternHandler;

    public function __construct(CI_DB_active_record $db, PatternHandler $patternHandler) {

        $this->db = $db;
        $this->patternHandler = $patternHandler;
    }

    public function run() {

        $fields = $this->selectOldPatterns();
        if ($fields) {
            foreach ($fields as $field) {
                $data = $this->prepareData($field);
                $locale = $field['locale'];

                try {
                    $pattern = $this->patternHandler->fillPattern(new SFilterPattern(), $data, $locale);
                    $pattern->save();
                } catch (\Exception $e) {

                }
            }
        }

    }

    private function selectOldPatterns() {

        $query = $this->db->where('locale', \MY_Controller::defaultLocale())->get('smart_filter_semantic_urls');
        /** @var \CI_DB_mysqli_result $query */
        if ($query->num_rows() > 0) {
            $fields = $query->result_array();
            return $fields;
        }
    }

    private function prepareData($field) {

        $data = [];

        if ($field['type'] == 'property') {
            $data['property_id'] = $field['entity_id'];
        } elseif ($field['type'] == 'brand') {
            $data['brand_id'] = $field['entity_id'];
        }
        $data['category_id'] = $field['category_id'];
        $data['active'] = $field['active'];

        $data['h1'] = $this->transform($field['h1']);
        $data['meta_title'] = $this->transform($field['meta_title']);
        $data['meta_keywords'] = $this->transform($field['meta_keywords']);
        $data['meta_description'] = $this->transform($field['meta_description']);
        $data['seo_text'] = $this->transform($field['seo_text']);

        return $data;
    }

    private function transform($text) {

        return strtr($text, $this->fromTo);
    }

}