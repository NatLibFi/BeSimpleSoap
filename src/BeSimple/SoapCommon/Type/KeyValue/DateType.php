<?php

namespace BeSimple\SoapCommon\Type\KeyValue;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use BeSimple\SoapCommon\Type\AbstractKeyValue;

class DateType extends AbstractKeyValue
{
    /**
     * @Soap\ComplexType("date")
     */
    protected $value;
}
