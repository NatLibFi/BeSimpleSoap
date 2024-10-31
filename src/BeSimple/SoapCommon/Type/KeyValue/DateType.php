<?php

namespace BeSimple\SoapCommon\Type\KeyValue;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use BeSimple\SoapCommon\Type\AbstractKeyValue;

/**
 * Date
 */
class DateType extends AbstractKeyValue
{
    /**
     * Value
     *
     * @Soap\ComplexType("date")
     */
    protected $value;
}
