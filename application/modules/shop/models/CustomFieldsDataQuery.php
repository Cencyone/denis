<?php

 use Base\CustomFieldsDataQuery as BaseCustomFieldsDataQuery;
 use Propel\Runtime\ActiveQuery\Criteria;

/**
 * Skeleton subclass for performing query and update operations on the 'custom_fields_data' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.Shop
 */
class CustomFieldsDataQuery extends BaseCustomFieldsDataQuery
{

    /**
     * @param int $entity_id
     * @param string $entity
     * @param bool $getPrivateData
     * @return array
     */
    public function getCustomFieldsData($entity_id, $entity = 'user', $getPrivateData = true) {
        $names = CustomFieldsQuery::create()->setComment(__METHOD__)->filterByIsActive(true)->find()->toArray($keyColumn = 'id');

        $c = new Criteria();
        $c->add('is_active', true);
        $c->add('entity', $entity);
        if (!$getPrivateData) {
            $c->add('is_private', false);
        }
        $c->addJoin('id', 'field_id', Criteria::LEFT_JOIN);
        $c->add('entity_id', $entity_id);

        $fieldsData = CustomFieldsDataQuery::create()->setComment(__METHOD__)->find($c);

        $data = [];

        foreach ($fieldsData as $field) {
            $data['custom_field_' . $names[$field->field_id]['name']] = $field->field_data;
        }

        return $data;
    }

}

// CustomFieldsDataQuery