<?php

/**
 * This file is part of the BeSimpleSoap.
 *
 * (c) Christian Kerl <christian-kerl@web.de>
 * (c) Francis Besset <francis.besset@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace BeSimple\SoapCommon\Definition;

/**
 * Method
 *
 * @author Francis Besset <francis.besset@gmail.com>
 */
class Method
{
    private $name;

    private $headers;

    private $input;

    private $output;

    private $fault;

    public function __construct($name)
    {
        $this->name = $name;

        $this->headers = new Message($name . 'Header');
        $this->input = new Message($name . 'Request');
        $this->output = new Message($name . 'Response');
        $this->fault = new Message($name . 'Fault');
    }

    public function getName()
    {
        return $this->name;
    }

    public function getVersions()
    {
        return [\SOAP_1_1, \SOAP_1_2];
    }

    public function getUse()
    {
        return \SOAP_LITERAL;
    }

    public function addHeader($name, $type, $nillable = false)
    {
        $this->headers->add($name, $type, $nillable);
    }

    public function addInput($name, $type, $nillable = false)
    {
        $this->input->add($name, $type, $nillable);
    }

    public function setOutput($type)
    {
        $this->output->add('return', $type);
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($name, $default = null)
    {
        return $this->headers->get($name, $default);
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getFault()
    {
        return $this->fault;
    }
}
