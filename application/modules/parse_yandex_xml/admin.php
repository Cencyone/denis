<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');
use CMSFactory\ModuleSettings;
/**
 * Image CMS
 * Sample Module Admin
 */
class Admin extends BaseAdminController
{

    public function __construct() {
        parent::__construct();
        $lang = new MY_Lang();
        $lang->load('parse_yandex_xml');
    }

    public function index() {
        
        $categiries = ShopCore::app()->SCategoryTree->getTree_();
        \CMSFactory\assetManager::create()
                ->setData('settings', ModuleSettings::ofModule('parse_yandex_xml')->get())
            ->setData('categories', $categiries)
                ->registerScript('script', TRUE)
                ->registerStyle('style', TRUE)
                ->renderAdmin('main');
    }

    public function list_provider_files()
    {
        \CMSFactory\assetManager::create()
            ->setData('prov_settings', ModuleSettings::ofModule('parse_yandex_xml')->get()['list_provider_files'])
            ->registerScript('script_prov', TRUE)
            ->renderAdmin('list_provider_files');
    }


    public function addProvider(){

        if ($this->input->post('variable')) {

            $settings = ModuleSettings::ofModule('parse_yandex_xml')->get()['list_provider_files'];
            if(key_exists($this->input->post('variable'), $settings)){
                unset($settings[$this->input->post('variable')]);
                $settings[$this->input->post('variable')]=$this->input->post('variableValue');
            }else{
                $settings[$this->input->post('variable')]=$this->input->post('variableValue');
            }

            if (ModuleSettings::ofModule('parse_yandex_xml')->set('list_provider_files', $settings)) {
                return true;
                showMessage(lang('Settings saved', 'parse_yandex_xml'), lang('Message', 'parse_yandex_xml'));
            }
            $this->cache->delete_all();
            return true;
        }
    }

    public function updateProv(){

        if ($this->input->post('variable') && $this->input->post('oldVariable')) {

            $settings = ModuleSettings::ofModule('parse_yandex_xml')->get()['list_provider_files'];
            if(key_exists($this->input->post('oldVariable'), $settings)){
                unset($settings[$this->input->post('oldVariable')]);
                $settings[$this->input->post('variable')]=$this->input->post('variableValue');
            }else{
                $settings[$this->input->post('variable')]=$this->input->post('variableValue');
            }

            if (ModuleSettings::ofModule('parse_yandex_xml')->set('list_provider_files', $settings)) {
                return true;
                showMessage(lang('Settings saved', 'parse_yandex_xml'), lang('Message', 'parse_yandex_xml'));
            }
            $this->cache->delete_all();
            return true;
        }
    }

    public function deleteProvider(){

        if ($this->input->post('variable')) {

            $settings = ModuleSettings::ofModule('parse_yandex_xml')->get()['list_provider_files'];

            if(key_exists($this->input->post('variable'), $settings)){
                unset($settings[$this->input->post('variable')]);
                ModuleSettings::ofModule('parse_yandex_xml')->set('list_provider_files', $settings);
            }
            return true;

            $this->cache->delete_all();
        }
    }
    public function save() {
        // dd($this->input->post());
        if ($this->input->post('main_set')) {
            
            if (ModuleSettings::ofModule('parse_yandex_xml')->set(['main_set' =>$this->input->post('main_set'), 'cron' => $this->input->post('cron'),
                'list_provider_files' =>ModuleSettings::ofModule('parse_yandex_xml')->get()['list_provider_files']
                ])) {
                // ModuleSettings::ofModule('parse_yandex_xml')->set('cron', '');
                // ModuleSettings::ofModule('parse_yandex_xml')->set('cron',$this->input->post('cron'));
                showMessage(lang('Settings saved', 'parse_yandex_xml'), lang('Message', 'parse_yandex_xml'));
            }
            $this->cache->delete_all();
        }
    }

}