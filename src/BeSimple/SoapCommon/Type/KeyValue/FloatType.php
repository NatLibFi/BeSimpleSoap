<?php

namespace BeSimple\SoapCommon\Type\KeyValue;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use BeSimple\SoapCommon\Type\AbstractKeyValue;

/**
 * Float
 */
class FloatType extends AbstractKeyValue
{
    /**
     * Value
     *
     * @Soap\ComplexType("float")
     */
    protected $value;
}
