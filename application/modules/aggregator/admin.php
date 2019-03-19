<?php

use aggregator\src\IAggregator;
use CMSFactory\assetManager;
use CMSFactory\ModuleSettings;

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Image CMS
 * Sample Module Admin
 */
class Admin extends BaseAdminController
{

    /**
     * @var IAggregator[]
     */
    private $aggregators;

    /**
     * @var ModuleSettings
     */
    private $settings;

    public function __construct() {
        parent::__construct();
        $lang = new MY_Lang();
        $lang->load('aggregator');
        $this->settings = ModuleSettings::ofModule('aggregator');
        $this->aggregators = \aggregator\src\AggregatorFactory::getAggregatorContainer($this->settings->get());

    }

    /**
     * Render all
     */
    public function index() {
        $configsView = [];

        foreach ($this->aggregators->getAggregators() as $aggregator) {

            $configsView[$aggregator->getId()] = $this->getView($aggregator);
        }

        assetManager::create()->setData(['configsView' => $configsView, 'aggregators' => $this->aggregators])
            ->renderAdmin('main');
    }

    private function getView(IAggregator $aggregator) {
        $fields = '';
        foreach ($aggregator->getModuleViewFields() as $field) {
            $fields .= assetManager::create()
                ->setData($field)
                ->setData(['aggregator' => $aggregator])
                ->fetchAdminTemplate($field['type']);
        }

        return assetManager::create()->setData(['fields' => $fields, 'aggregator' => $aggregator])
            ->fetchAdminTemplate('block', false);

    }

    public function save() {

        $configs = [];
        foreach ($this->aggregators as $aggregator) {
            if ($aggregatorConfigs = $this->input->post($aggregator->getId())) {
                $configs[$aggregator->getId()] = $aggregatorConfigs;
            }
        }
        $this->settings->set($configs);
        $this->lib_admin->log(lang('Settings in the aggregator has been saved', 'aggregator'));

    }

}