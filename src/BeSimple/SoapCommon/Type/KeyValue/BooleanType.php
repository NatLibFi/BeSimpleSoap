<?php

namespace BeSimple\SoapCommon\Type\KeyValue;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use BeSimple\SoapCommon\Type\AbstractKeyValue;

/**
 * Boolean
 */
class BooleanType extends AbstractKeyValue
{
    /**
     * Value
     *
     * @Soap\ComplexType("boolean")
     */
    protected $value;
}
