<?php

namespace BeSimple\SoapCommon\Tests\Fixtures;

use BeSimple\SoapCommon\AbstractSoapBuilder;

class SoapBuilder extends AbstractSoapBuilder
{
    /**
     * Create new instance with default options.
     *
     * @return static
     */
    public static function createWithDefaults(): static
    {
        return parent::configureWithDefaults(new self());
    }
}
