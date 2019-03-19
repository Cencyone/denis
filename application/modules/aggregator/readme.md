# Price aggregator

This module provides interface for price aggregator systems
such as yandex.market price.ua hotline.ua

## Create system
To create your own aggregator, just create new class in
/application/modules/aggregator/src/Systems and implement IAggregator interface.
You can extend abstract Aggregator class where implemented base functionality 
which is same for most cases.

## Loading System
Each system are loaded automatically by AggregatorFactory if IAggregator interface is implemented

## Data provider
DataProvider provides data as categories products etc. for you system
and available in each aggregator ```$this->dataProvider```

## Configuration

```->getProductViewFields()``` should return array of fields that will be displayed in
product tab

```->getModuleViewFields()``` should return array of fields that will be displayed in
module configuration

each field template placed /application/modules/aggregator/assets/admin/$field['type']

## Xml
```->generateXml()``` method should display xml data for price aggregator







