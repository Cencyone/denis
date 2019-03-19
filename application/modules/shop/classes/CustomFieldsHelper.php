<?php

/**
 * CustomFieldsHelper
 *
 * @package ImageCMS.Shop.CustomFields
 * @version 0.2
 * @copyright ImageCMS©
 * @author koloda, <dev@imagecms.net>
 * @license
 */
class CustomFieldsHelper
{
    const DEFAULT_TEXTAREA_FIELD_CLASSES = ' elRTE';

    /**
     * @var CustomFieldsHelper
     */
    protected static $_BehaviorInstance;

    /**
     * @var string
     */
    private $adminPattern = '<div class="control-group">
                                            <label class="control-label" for="{$inputId}"> {$label}: </label>
                                            <div class="controls">{$input}</div>
                                        </div>';

    /**
     * @var string
     */
    private $adminPatternDesc = '
<div class="control-group">
    <label class="control-label" for="{$inputId}">
        {$description}
        {$label}:
        {$requiredHtml}
    </label>
    <div class="controls">
        {btn_elfinder}
        <div class="o_h">
            {$input}
        </div>
    </div>
</div>
';

    /**
     * @var string
     */
    private $baseWidgetName = 'custom_field';

    /**
     * @var string
     */
    private $btn_elfinder;

    /**
     * @var array
     */
    private $customData = [];

    /**
     * @var string
     */
    private $descHTML = '<span class="popover_ref" data-original-title="">
                                                    <i class="icon-info-sign"></i>
                                                </span>
                                                <div class="d_n">
                                                    {$description}
                                    </div>';

    /**
     * @var array
     */
    private $entities = [
                         'user',
                         'order',
                         'product',
                         'category',
                         'brand',
                        ];

    /**
     * @var array
     */
    private $entities_locale = [
                                'product',
                                'category',
                                'brand',
                               ];

    /**
     * @var string
     */
    private $formWidgetPattern = '<label for="{$inputId}">{$label}</label>{$input}';

    /**
     * @var array
     */
    private $patternVariables = [
                                 '{$input}',
                                 '{$label}',
                                 '{$inputId}',
                                ];

    /**
     * @var string
     */
    public  $requiredHtml = '<span class="must">*</span>';

    private $entityId;

    /**
     * CustomFieldsHelper constructor.
     */
    public function __construct() {
        $this->btn_elfinder = '<div class="group_icon pull-right"><a href='
            . site_url('application/third_party/filemanager/dialog.php?type=2&field_id={Id}')
            . ' class="btn  iframe-btn" type="button"><i class="icon-picture"></i>'
            . lang('Select file', 'admin') . '</a></div>';
    }

    //public methods

    /**
     * Render part of form with custom fields for Admin-area
     * @return string
     */
    public function asAdminHtml() {
        $this->formWidgetPattern = &$this->adminPattern;
        return $this->asHtml();
    }

    /**
     * Return string with rendered collected (using getCustomField() or getCustomFields()) custom field(s)
     * @param bool $usePattern If call after getCustomField() - render field with pattern (if TRUE) or clean widget (if FALSE). For multiple fields (after getCustomFields()) widgets always render with pattern.
     * @return string
     */
    public function asHtml($usePattern = true) {
        if (count($this->customData) == 1) {
            return $this->renderWidget($this->customData[0], $usePattern);
        }

        $outputHtml = '';
        foreach ($this->customData as $customField) {
            $outputHtml .= $this->renderWidget($customField);
        }

        return $outputHtml;
    }

    /**
     * @param array $widget
     * @param bool $usePattern
     * @return mixed|string
     */
    private function renderWidget($widget, $usePattern = true) {

        $this->initPatternVariables();

        $inputHtml = '';
        switch ($widget['field_type_id']) {
            //render text input
            case 0:
            case 3:
                $inputHtml = form_input(
                    [
                     'name'  => $this->baseWidgetName . '[' . $widget['id'] . ']',
                     'id'    => $this->baseWidgetName . '_' . $widget['id'],
                     'value' => $widget['field_data'],
                     'class' => $widget['classes'],
                    ]
                );
                break;

            //render textarea
            case 1:
                $inputHtml = form_textarea(
                    [
                     'name'  => $this->baseWidgetName . '[' . $widget['id'] . ']',
                     'id'    => $this->baseWidgetName . '_' . $widget['id'],
                     'value' => $widget['field_data'],
                     'class' => $widget['classes'] . self::DEFAULT_TEXTAREA_FIELD_CLASSES,
                    ]
                );
                break;

            //render select
            case 2:
                if ($widget['options'] == 'multiple') {

                    $inputHtml = form_multiselect($this->baseWidgetName . '[' . $widget['id'] . ']', $widget['possible_values'], $widget['field_data'], 'id="' . $this->baseWidgetName . '_' . $widget['id'] . '" class="' . $widget['user_classes'] . '"');
                } else {

                    $inputHtml = form_dropdown($this->baseWidgetName . '[' . $widget['id'] . ']', $widget['possible_values'], $widget['field_data'] ?: '', 'id="' . $this->baseWidgetName . '_' . $widget['id'] . '" class="' . $widget['user_classes'] . '"');
                }
                break;

            //render checkbox
            case 4:
                $inputHtml = form_button(
                    [
                     'name'    => $this->baseWidgetName . '[' . $widget['id'] . ']',
                     'id'      => $this->baseWidgetName . '_' . $widget['id'],
                     'checked' => $widget['field_data'] ? true : false,
                     'class'   => 'btn btn-small actions-value setCustomField '. ($widget['field_data'] ?'btn-primary':''),
                     'data-id'=>$this->entityId,
                     'data-cf-id'=>$widget['id'],
                    ],'<i class="fa fa-asterisk"></i>'
                );
                break;
        }

        //place input and label into pattern str. (or return clean input html)
        $data = [
                 $inputHtml,
                 $widget['field_label'],
                 $this->baseWidgetName . '_' . $widget['id'],
                ];
        if (!$usePattern) {
            return $inputHtml;
        }

        if ($widget['field_description']) {
            $this->adminPattern = $this->adminPatternDesc;
            array_push($this->patternVariables, '{$description}');
            $desc = str_replace('{$description}', $widget['field_description'], $this->descHTML);
            array_push($data, $desc);
        }

        if ($widget['is_required']) {
            array_push($data, $this->requiredHtml);
            array_push($this->patternVariables, '{$requiredHtml}');
        } else {
            array_push($data, '');
        }

        $inp = str_replace($this->patternVariables, $data, $this->adminPatternDesc);

        // deleting template variables that was not set
        $inp = preg_replace('/\{\$[a-zA-Z]+\}/', '', $inp);

        if ($widget['field_type_id'] == 3) {
            return str_replace(['{btn_elfinder}', '{Id}'], [$this->btn_elfinder, $this->baseWidgetName . '_' . $widget['id']], $inp);
        } else {
            return str_replace(['{btn_elfinder}', '{Id}'], ['', $this->baseWidgetName . '_' . $widget['id']], $inp);
        }
    }

    private function initPatternVariables() {

        $this->patternVariables = [
                                   '{$input}',
                                   '{$label}',
                                   '{$inputId}',
                                  ];
    }

    /**
     * Return array of collected (using getCustomField() or getCustomFields()) custom fields
     * @param bool $nameKey =false If FALSE, return array, where keys are field_id's, else - field_namme's
     * @return array
     */
    public function asArray($nameKey = false) {
        $result = [];
        foreach ($this->customData as $customField) {
            if (!$nameKey) {
                $result[$customField['field_name']] = $customField;
            } else {
                $result[$customField['id']] = $customField;
            }
        }

        return $result;
    }

    /**
     * @param string $template
     */
    public function asTemplate($template) {

        $ci = &get_instance();
        $ci->template->assign('fields', $this->customData);
        $ci->template->display('file:' . ShopCore::$template_path . '../customFields/' . $template);
    }

    /**
     * Coping custom-fields data from one product to another
     * @param integer $productIdSource
     * @param integer $productIdDest
     * @return boolean
     */
    public function copyProductCustomFieldsData($productIdSource, $productIdDest) {
        $productCSData = \CI::$APP->db
            ->select(['field_id', 'field_data', 'custom_fields_data.locale'])
            ->join('custom_fields', 'custom_fields.id=custom_fields_data.field_id AND custom_fields.entity="product"')
            ->where(['custom_fields_data.entity_id' => $productIdSource])
            ->get('custom_fields_data')
            ->result_array();

        if (count($productCSData) == 0) {
            return;
        }

        $countProductCSData = count($productCSData);
        for ($i = 0; $i < $countProductCSData; $i++) {
            $productCSData[$i]['entity_id'] = $productIdDest;
        }

        \CI::$APP->db->insert_batch('custom_fields_data', $productCSData);
        $error = \CI::$APP->db->_error_message();
        return !empty($error) ? false : true;
    }

    /**
     * @return CustomFieldsHelper
     */
    public static function create() {
        (null !== self::$_BehaviorInstance) OR self::$_BehaviorInstance = new self();
        return self::$_BehaviorInstance;
    }

    /**
     * Collect custom fields of entity and return \CustomFieldsHelper object
     * @param string $entity Name of entity type - now support 'order' and 'user'
     * @param integer $entityId Id of entity, related wit custom field
     * @param null $lookup
     * @return CustomFieldsHelper
     */
    public function getCustomFields($entity, $entityId = -1, $lookup = null) {
        $this->entityId = $entityId;
        return $this->_getCustomFields($entity, $entityId, $lookup)->prepareOptions();
    }

    private function prepareOptions() {
        if ($this->customData) {

            foreach ($this->customData as $key => $customField) {

                if ($customField['field_type_id'] == 2 && $customField['possible_values'] && is_array(unserialize($customField['possible_values']))) {
                    $this->customData[$key]['possible_values'] = array_combine(unserialize($customField['possible_values']), unserialize($customField['possible_values']));
                    if ($customField['options'] != 'multiple') {
                        $this->customData[$key]['possible_values'][''] = 'none';
                    }
                } else {

                    $this->customData[$key]['possible_values'] = ['' => 'none'];
                }
            }
        }

        return $this;
    }

    /**
     * Collect custom fields of entity and return \CustomFieldsHelper object
     * @param string $entity Name of entity type - now support 'order' and 'user'
     * @param integer $entityId Id of entity, related wit custom field
     * @return $this
     */
    public function _getCustomFields($entity, $entityId = -1, $lookup = null) {

        $CI = &get_instance();
        //$locale = chose_language();
        $locale = chose_language();
        $entityId = (int) $entityId;
        if ($lookup === null) {
            $lookup = $entity;
        }
        if (!in_array($entity, $this->entities) || !is_numeric($entityId)) {
            return $this;
        }

        if ($entityId > 0) {
            try {
                $sql = "SELECT
                    `custom_fields`.*, null as field_data,
                    custom_fields_i18n.field_label as field_label,
                    custom_fields_i18n.field_description as field_description,
                    custom_fields_i18n.possible_values as possible_values
                    FROM custom_fields
                    left join `custom_fields_i18n` on custom_fields_i18n.id = custom_fields.id
                    WHERE `custom_fields`.`is_active` = 1 AND `custom_fields`.`entity` = '$entity'
                    and custom_fields_i18n.locale = '$locale' ORDER BY position";

                $fields = $CI->db->query($sql)->result_array();

                if (!in_array($entity, $this->entities_locale)) {
                    $locale = MY_Controller::getDefaultLanguage()['identif'];
                }

                $custom_aux = $CI->db->select('*')
                    ->distinct()
                    ->join('custom_fields_data', 'custom_fields_data.field_id = custom_fields.id', 'left')
                    ->where("`custom_fields.entity` = '" . $lookup . "' ")
                    ->where("`custom_fields_data.entity_id` = '$entityId'")
                    ->where("`custom_fields_data.locale` = '$locale'")
                    ->get('custom_fields')
                    ->result_array();

                foreach ($fields as $key => $val) {
                    foreach ($custom_aux as $custom_one) {
                        if ($custom_one['field_name'] == $val['field_name']) {
                            if (!empty($custom_one['field_data'])) {
                                $fields[$key]['field_data'] = $custom_one['field_data'];
                            } else {
                                $fields[$key]['field_data'] = '';
                            }
                        }
                    }
                }
            } catch (Exception $exc) {

                $fields = $CI->db->select('*')
                    ->join('custom_fields_i18n', 'custom_fields_i18n.id = custom_fields.id', 'left')
                    ->where("`custom_fields.entity` = '$entity' ")
                    ->where("`custom_fields_i18n.locale` = '$locale'")
                    ->get('custom_fields')->result_array();
            }

            $this->customData = $fields;
        } else {
            $this->customData = $CI->db->select('*')
                ->join('custom_fields_i18n', 'custom_fields_i18n.id = custom_fields.id', 'left')
                ->where("`custom_fields.entity` = '$entity' ")
                ->where('`custom_fields.is_active` = 1')
                ->where("`custom_fields_i18n.locale` = '$locale'")
                ->order_by('position')
                ->get('custom_fields')
                ->result_array();
        }

        return $this;
    }

    /**
     * Get custom Fields by field_id and entity_name
     * @param string $entity Name of entity type - now support 'order' and 'user'
     * @param integer $entityId Id of entity, related wit custom field
     * @param null $lookup
     * @return array of fields
     */
    public function getCustomFielsdAsArray($entity, $entityId = -1, $lookup = null) {

        $this->_getCustomFields($entity, $entityId, $lookup);

        return $this->customData;
    }

    /**
     * @param string $fieldName
     * @param string $entity
     * @param int $entityId
     * @param null|int $lookup
     * @return CustomFieldsHelper
     */
    public function getOneCustomFieldsByName($fieldName, $entity, $entityId = -1, $lookup = null) {
        $field = $this->_getOneCustomFieldsByName($fieldName, $entity, $entityId, $lookup);
        $field['field_label'] = $field['field_label'] . ':';
        if ($field) {
            $this->customData = [$field];
        } else {
            $this->customData = null;
        }

        return $this->prepareOptions();
    }

    //private methods

    /**
     * @param string $fieldName
     * @param string $entity
     * @param int $entityId
     * @param null $lookup
     * @return mixed
     */
    public function _getOneCustomFieldsByName($fieldName, $entity, $entityId = -1, $lookup = null) {

        $this->_getCustomFields($entity, $entityId, $lookup);

        foreach ($this->customData as $key => $val) {
            if ($val['field_name'] == $fieldName) {
                $field = $this->customData[$key];
                break;
            }
        }
        return $field;
    }

    /**
     * @param string $fieldName
     * @param string $entity
     * @param int $entityId
     * @param null $lookup
     * @return mixed
     */
    public function getOneCustomFieldsByNameArray($fieldName, $entity, $entityId = -1, $lookup = null) {

        return $this->_getOneCustomFieldsByName($fieldName, $entity, $entityId, $lookup);
    }

    /**
     * Set up pattern to render widgets and return itself
     * @param bool|string $pattern String pattern
     * @return CustomFieldsHelper
     */
    public function setPattern($pattern = false) {
        if ($pattern) {
            $this->formWidgetPattern = $pattern;
        }
        return $this;
    }

    /**
     * Set up pattern from fileto render widgets and return itself
     * @param bool|string $pattern String pattern
     * @return CustomFieldsHelper
     */
    public function setPatternMain($pattern = false) {

        if ($pattern) {
            $ci = &get_instance();
            ob_start();
            include realpath('templates/' . $ci->config->item('template') . '/shop') . '/' . $pattern . '.php';
            $pattern = ob_get_clean();
            $this->adminPatternDesc = $pattern;
        }

        return $this;
    }

    /**
     * Set up html (or text), available while required widget render, and return itself
     * @param string $html
     * @return CustomFieldsHelper
     */
    public function setRequiredHtml($html = '') {
        if ($html) {
            $this->requiredHtml = $html;
        }
        return $this;
    }

}