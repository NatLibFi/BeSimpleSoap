<?php

namespace BeSimple\SoapCommon\Type\KeyValue;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use BeSimple\SoapCommon\Type\AbstractKeyValue;

class BooleanType extends AbstractKeyValue
{
    /**
     * @Soap\ComplexType("boolean")
     */
    protected $value;
}
