<?php namespace aggregator\src;

/**
 * Class AggregatorFactory
 * Responsive for creation Aggregator container
 * @package aggregator\src
 */
class AggregatorFactory
{

    const AGGREGATORS_NAMESPACE = 'aggregator\src\Systems\\';
    const AGGREGATORS_PATH = 'modules/aggregator/src/Systems/*.php';

    public static function getAggregatorContainer(array $config) {

        $aggregatorContainer = new AggregatorContainer($config);
        $files = glob(APPPATH . self::AGGREGATORS_PATH);
        $dataProvider = new DataProvider();

        foreach ($files as $file) {
            $class = self::AGGREGATORS_NAMESPACE . str_replace('.php', '', array_pop(explode('/', $file)));

            if (class_exists($class) && is_a($class, IAggregator::class, true)) {
                $aggregatorContainer->addAggregator(new $class($dataProvider));
            }
        }

        return $aggregatorContainer;

    }

}