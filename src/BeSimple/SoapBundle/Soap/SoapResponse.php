<?php

/**
 * This file is part of the BeSimpleSoapBundle.
 *
 * (c) Christian Kerl <christian-kerl@web.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace BeSimple\SoapBundle\Soap;

use BeSimple\SoapBundle\Util\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * SoapResponse.
 *
 * @author Christian Kerl <christian-kerl@web.de>
 */
class SoapResponse extends Response
{
    /**
     * SOAP headers
     *
     * @var \BeSimple\SoapBundle\Util\Collection
     */
    protected $soapHeaders;

    /**
     * SOAP return value
     *
     * @var mixed
     */
    protected $soapReturnValue;

    public function __construct($returnValue = null)
    {
        parent::__construct();

        $this->soapHeaders = new Collection('getName', 'BeSimple\SoapBundle\Soap\SoapHeader');
        $this->setReturnValue($returnValue);
    }

    /**
     * Add a SOAP header
     *
     * @param SoapHeader $soapHeader
     */
    public function addSoapHeader(SoapHeader $soapHeader)
    {
        $this->soapHeaders->add($soapHeader);
    }

    /**
     * Get SOAP headers
     *
     * @return \BeSimple\SoapBundle\Util\Collection
     */
    public function getSoapHeaders()
    {
        return $this->soapHeaders;
    }

    public function setReturnValue($value)
    {
        $this->soapReturnValue = $value;

        return $this;
    }

    public function getReturnValue()
    {
        return $this->soapReturnValue;
    }
}
