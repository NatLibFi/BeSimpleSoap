<?php

/*
 * This file is part of the BeSimpleSoapBundle.
 *
 * (c) Christian Kerl <christian-kerl@web.de>
 * (c) Francis Besset <francis.besset@gmail.com>
 * Copyright (C) University Of Helsinki (The National Library of Finland) 2024.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace BeSimple\SoapCommon;

use BeSimple\SoapCommon\Converter\TypeConverterCollection;
use BeSimple\SoapCommon\Converter\TypeConverterInterface;

/**
 * @author Christian Kerl <christian-kerl@web.de>
 * @author Francis Besset <francis.besset@gmail.com>
 * @author Ere Maijala <ere.maijala@helsinki.fi>
 */
abstract class AbstractSoapBuilder
{
    /**
     * WSDL
     *
     * @var string
     */
    protected $wsdl;

    /**
     * SOAP options
     *
     * @var array
     */
    protected $soapOptions = array();

    /**
     * Other options
     *
     * @var array
     */
    protected array $options = [];

    /**
     * @return static
     */
    static public function createWithDefaults(): static
    {
        $builder = new static();

        return $builder
            ->withSoapVersion12()
            ->withEncoding('UTF-8')
            ->withSingleElementArrays()
        ;
    }

    public function __construct()
    {
        $this->soapOptions['features'] = 0;
        $this->soapOptions['classmap'] = new Classmap();
        $this->soapOptions['typemap']  = new TypeConverterCollection();
    }

    public function getWsdl()
    {
        return $this->wsdl;
    }

    public function getSoapOptions()
    {
        $options = $this->soapOptions;

        $options['classmap'] = $this->soapOptions['classmap']->all();
        $options['typemap']  = $this->soapOptions['typemap']->getTypemap();

        return $options;
    }

    /**
     * @return static
     */
    public function withWsdl($wsdl): static
    {
        $this->wsdl = $wsdl;

        return $this;
    }

    /**
     * @return static
     */
    public function withSoapVersion11(): static
    {
        $this->soapOptions['soap_version'] = \SOAP_1_1;

        return $this;
    }

    /**
     * @return static
     */
    public function withSoapVersion12(): static
    {
        $this->soapOptions['soap_version'] = \SOAP_1_2;

        return $this;
    }

    public function withEncoding($encoding): static
    {
        $this->soapOptions['encoding'] = $encoding;

        return $this;
    }

    public function withWsdlCache($cache): static
    {
        if (!in_array($cache, Cache::getTypes(), true)) {
            throw new \InvalidArgumentException();
        }

        $this->soapOptions['cache_wsdl'] = $cache;

        return $this;
    }

    /**
     * @return static
     */
    public function withWsdlCacheNone(): static
    {
        $this->soapOptions['cache_wsdl'] = Cache::TYPE_NONE;

        return $this;
    }

    /**
     * @return static
     */
    public function withWsdlCacheDisk(): static
    {
        $this->soapOptions['cache_wsdl'] = Cache::TYPE_DISK;

        return $this;
    }

    /**
     * @return static
     */
    public function withWsdlCacheMemory(): static
    {
        $this->soapOptions['cache_wsdl'] = Cache::TYPE_MEMORY;

        return $this;
    }

    /**
     * @return static
     */
    public function withWsdlCacheDiskAndMemory(): static
    {
        $this->soapOptions['cache_wsdl'] = Cache::TYPE_DISK_MEMORY;

        return $this;
    }

    /**
     * Enables the SOAP_SINGLE_ELEMENT_ARRAYS feature.
     * If enabled arrays containing only one element will be passed as arrays otherwise the single element is extracted and directly passed.
     *
     * @return static
     */
    public function withSingleElementArrays(): static
    {
        $this->soapOptions['features'] |= \SOAP_SINGLE_ELEMENT_ARRAYS;

        return $this;
    }

    /**
     * Enables the SOAP_WAIT_ONE_WAY_CALLS feature.
     *
     * @return static
     */
    public function withWaitOneWayCalls(): static
    {
        $this->soapOptions['features'] |= \SOAP_WAIT_ONE_WAY_CALLS;

        return $this;
    }

    /**
     * Enables the SOAP_USE_XSI_ARRAY_TYPE feature.
     *
     * @return static
     */
    public function withUseXsiArrayType(): static
    {
        $this->soapOptions['features'] |= \SOAP_USE_XSI_ARRAY_TYPE;

        return $this;
    }

    public function withTypeConverter(TypeConverterInterface $converter): static
    {
        $this->soapOptions['typemap']->add($converter);

        return $this;
    }

    public function withTypeConverters(TypeConverterCollection $converters, $merge = true): static
    {
        if ($merge) {
            $this->soapOptions['typemap']->addCollection($converters);
        } else {
            $this->soapOptions['typemap']->set($converters->all());
        }

        return $this;
    }

    /**
     * Adds a class mapping to the classmap.
     *
     * @param string $xmlType
     * @param string $phpType
     *
     * @return static
     */
    public function withClassMapping($xmlType, $phpType): static
    {
        $this->soapOptions['classmap']->add($xmlType, $phpType);

        return $this;
    }

    /**
     * Sets the classmap.
     *
     * @param Classmap $classmap The classmap.
     * @param boolean  $merge    If true the given classmap is merged into the existing one, otherwise the existing one is overwritten.
     *
     * @return static
     */
    public function withClassmap(Classmap $classmap, $merge = true): static
    {
        if ($merge) {
            $this->soapOptions['classmap']->addClassmap($classmap);
        } else {
            $this->soapOptions['classmap']->set($classmap->all());
        }

        return $this;
    }

    protected function validateWsdl()
    {
        if (null === $this->wsdl) {
            throw new \InvalidArgumentException('The WSDL has to be configured!');
        }
    }
}
