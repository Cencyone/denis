<?php namespace aggregator\src\Systems;

use aggregator\src\Aggregator;
use aggregator\src\DataProvider;

class Hotline
{

    /**
     * Hotline constructor.
     * @param DataProvider $dataProvider
     */
    public function __construct(DataProvider $dataProvider) {
        parent::__construct($dataProvider);
        $this->id = 'hotline';
        $this->name = 'Hotline';
    }

    /**
     * @return array
     */
    public function getProductViewFields() {
        // TODO: Implement getProductConfig() method.
    }

    /**
     * @return array
     */
    public function getModuleViewFields() {
        return [
                'brands'     => [
                                 'name'     => 'brands',
                                 'multiple' => true,
                                 'label'    => lang('Brands', 'aggregator'),
                                 'type'     => 'select',
                                 'options'  => $this->dataProvider->getBrands(),

                                ],
                'categories' => [
                                 'name'     => 'categories',
                                 'multiple' => true,
                                 'label'    => lang('Categories', 'aggregator'),
                                 'type'     => 'select',
                                 'options'  => $this->dataProvider->getCategories(),

                                ],
               ];
    }
}