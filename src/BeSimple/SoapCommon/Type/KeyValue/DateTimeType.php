<?php

namespace BeSimple\SoapCommon\Type\KeyValue;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use BeSimple\SoapCommon\Type\AbstractKeyValue;

/**
 * DateTime
 */
class DateTimeType extends AbstractKeyValue
{
    /**
     * Value
     *
     * @Soap\ComplexType("dateTime")
     */
    protected $value;
}
