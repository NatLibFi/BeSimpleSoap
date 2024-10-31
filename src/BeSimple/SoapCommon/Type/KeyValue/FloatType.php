<?php

namespace BeSimple\SoapCommon\Type\KeyValue;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use BeSimple\SoapCommon\Type\AbstractKeyValue;

class FloatType extends AbstractKeyValue
{
    /**
     * @Soap\ComplexType("float")
     */
    protected $value;
}
