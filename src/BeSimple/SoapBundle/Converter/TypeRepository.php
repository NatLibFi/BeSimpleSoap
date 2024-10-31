<?php

/**
 * This file is part of the BeSimpleSoapBundle.
 *
 * (c) Christian Kerl <christian-kerl@web.de>
 * Copyright (C) University Of Helsinki (The National Library of Finland) 2024.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace BeSimple\SoapBundle\Converter;

use BeSimple\SoapBundle\Util\Assert;

/**
 * Type repository
 *
 * @author Christian Kerl <christian-kerl@web.de>
 * @author Ere Maijala <ere.maijala@helsinki.fi>
 */
class TypeRepository
{
    public const ARRAY_SUFFIX = '[]';

    private $xmlNamespaces  = [];
    private $defaultTypeMap = [];

    public function addXmlNamespace($prefix, $url)
    {
        $this->xmlNamespaces[$prefix] = $url;
    }

    public function getXmlNamespace($prefix)
    {
        return $this->xmlNamespaces[$prefix];
    }

    public function addDefaultTypeMapping($phpType, $xmlType)
    {
        Assert::thatArgumentNotNull('phpType', $phpType);
        Assert::thatArgumentNotNull('xmlType', $xmlType);

        $this->defaultTypeMap[$phpType] = $xmlType;
    }

    public function getXmlTypeMapping($phpType)
    {
        return isset($this->defaultTypeMap[$phpType]) ? $this->defaultTypeMap[$phpType] : null;
    }
}
