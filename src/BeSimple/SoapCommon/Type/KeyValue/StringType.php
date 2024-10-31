<?php

namespace BeSimple\SoapCommon\Type\KeyValue;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use BeSimple\SoapCommon\Type\AbstractKeyValue;

/**
 * String
 */
class StringType extends AbstractKeyValue
{
    /**
     * Value
     *
     * @Soap\ComplexType("string")
     */
    protected $value;
}
