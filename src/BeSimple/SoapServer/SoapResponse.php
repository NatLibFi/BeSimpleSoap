<?php

/**
 * This file is part of the BeSimpleSoapClient.
 *
 * (c) Christian Kerl <christian-kerl@web.de>
 * (c) Francis Besset <francis.besset@gmail.com>
 * Copyright (C) University Of Helsinki (The National Library of Finland) 2024.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace BeSimple\SoapServer;

use BeSimple\SoapCommon\SoapMessage;
use BeSimple\SoapCommon\SoapResponse as CommonSoapResponse;

/**
 * SoapResponse class for SoapClient. Provides factory function for response object.
 *
 * @author Andreas Schamberger <mail@andreass.net>
 * @author Ere Maijala <ere.maijala@helsinki.fi>
 */
class SoapResponse extends CommonSoapResponse
{
    /**
     * Factory function for SoapResponse.
     *
     * @param string $content  Content
     * @param string $location Location
     * @param string $action   SOAP action
     * @param string $version  SOAP version
     *
     * @return static
     */
    public static function create($content, $location, $action, $version): static
    {
        $response = new SoapResponse();
        $response->setContent($content);
        $response->setLocation($location);
        $response->setAction($action);
        $response->setVersion($version);
        $contentType = SoapMessage::getContentTypeForVersion($version);
        $response->setContentType($contentType);

        return $response;
    }

    /**
     * Send SOAP response to client.
     */
    public function send()
    {
        // set Content-Type header
        header('Content-Type: ' . $this->getContentType());

        // send content to client
        echo $this->getContent();
    }
}
