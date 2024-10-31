<?php

namespace BeSimple\SoapCommon\Type\KeyValue;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use BeSimple\SoapCommon\Type\AbstractKeyValue;

/**
 * Int
 */
class IntType extends AbstractKeyValue
{
    /**
     * Value
     *
     * @Soap\ComplexType("int")
     */
    protected $value;
}
