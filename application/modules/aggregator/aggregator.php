<?php

use aggregator\src\AggregatorFactory;
use aggregator\src\IAggregator;
use CMSFactory\assetManager;
use CMSFactory\Events;
use CMSFactory\ModuleSettings;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Image CMS
 * Module Frame
 */
class Aggregator extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $lang = new MY_Lang();
        $lang->load('aggregator');

    }

    /**
     * render xml for service
     * @param $name
     */
    public function service($name) {
        $aggregatorContainer = AggregatorFactory::getAggregatorContainer(ModuleSettings::ofModule('aggregator')->get());
        $aggregator = $aggregatorContainer->getAggregator($name);

        if ($aggregator) {
            $aggregator->generateXml();
        } else {
            $this->core->error_404();
        }

    }

    public static function adminAutoload() {

        Events::create()
            ->onShopProductPreUpdate()
            ->setListener('_extendProduct');

        Events::create()
            ->onShopProductUpdate()
            ->setListener('_saveSettings');
    }

    public static function _saveSettings($data) {

        /** @var SProducts $model */
        $model = $data['model'];

        if ($model) {

            $aggregatorContainer = AggregatorFactory::getAggregatorContainer(ModuleSettings::ofModule('aggregator')->get());

            $ci = \CI::$APP;

            $dbData = [];

            /** @var IAggregator $aggregator */
            foreach ($aggregatorContainer as $aggregator) {
                if ($data = $ci->input->post($aggregator->getId())) {
                    foreach ($data as $field => $value) {
                        $dbData[] = [
                                     'aggregator_id' => $aggregator->getId(),
                                     'product_id'    => $model->getId(),
                                     'field'         => $field,
                                     'value'         => $value,
                                    ];
                    }
                }

                /** @var CI_DB_active_record $db */
                $db = $ci->db;
                $db->delete('aggregator', ['product_id' => $model->getId()]);
                $db->insert_batch('aggregator', $dbData);
            }
        }
    }

    /**
     * @param array $data
     */
    public static function _extendProduct($data) {

        /** @var SProducts $model */
        $model = $data['model'];

        $aggregatorContainer = AggregatorFactory::getAggregatorContainer(ModuleSettings::ofModule('aggregator')->get());

        $view = '';
        /** @var IAggregator $aggregator */
        foreach ($aggregatorContainer as $aggregator) {
            $view .= self::getProductView($aggregator, $model->getId());
        }

        assetManager::create()
            ->appendData('moduleAdditions', $view);
    }

    private static function getProductView(IAggregator $aggregator, $productId) {
        $fields = '';
        $productConfigs = $aggregator->getProductConfigs($productId);
        foreach ($aggregator->getProductViewFields() as $name => $field) {

            $fields .= assetManager::create()
                ->setData($field)
                ->setData(['aggregator' => $aggregator])
                ->setData(['productValue' => $productConfigs[$name]])
                ->setData(['productId' => $productId])
                ->fetchAdminTemplate($field['type']);
        }

        return assetManager::create()->setData(['fields' => $fields, 'aggregator' => $aggregator])
            ->fetchAdminTemplate('product_block', false);

    }

    public function _install() {
        $this->load->dbforge();

        $fields = [
                   'id'            => [
                                       'type'           => 'INT',
                                       'constraint'     => 11,
                                       'auto_increment' => TRUE,
                                      ],
                   'product_id'    => [
                                       'type'       => 'INT',
                                       'constraint' => 11,
                                      ],
                   'aggregator_id' => [
                                       'type'       => 'VARCHAR',
                                       'constraint' => 255,
                                       'null'       => true,
                                      ],
                   'field'         => [
                                       'type'       => 'VARCHAR',
                                       'constraint' => 255,
                                       'null'       => true,
                                       'default'    => 'false',
                                      ],
                   'value'         => [
                                       'type'       => 'VARCHAR',
                                       'constraint' => 255,
                                       'null'       => true,
                                       'default'    => 'false',
                                      ],
                  ];
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table('aggregator', TRUE);

        $this->db->where('name', 'aggregator')
            ->update('components', ['enabled' => '1', 'autoload' => '1']);

        $this->db->query('ALTER TABLE `aggregator` ADD UNIQUE INDEX (`product_id`, `aggregator_id`,`field`)');
    }

    public function _deinstall() {
        $this->load->dbforge();
        $this->dbforge->drop_table('aggregator');
    }

}

/* End of file sample_module.php */