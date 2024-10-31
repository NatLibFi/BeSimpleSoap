<?php

/**
 * This file is part of the BeSimpleSoapClient.
 *
 * (c) Christian Kerl <christian-kerl@web.de>
 * (c) Francis Besset <francis.besset@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace BeSimple\SoapClient\Tests;

use BeSimple\SoapClient\SoapClient;
use BeSimple\SoapCommon\Cache;
use org\bovigo\vfs\vfsStream;

/**
 * Class SoapClientTest
 */
class SoapClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test that invalid WSDL files are not cached.
     *
     * @param string $wsdl WSDL
     *
     * @dataProvider provideInvalidWSDL
     */
    public function testInvalidWSDLCacheIsDeleted($wsdl)
    {
        $wsdlCacheDir = vfsStream::setup('wsdl');
        $wsdlCacheUrl = $wsdlCacheDir->url('wsdl');

        Cache::setEnabled(Cache::ENABLED);
        Cache::setDirectory($wsdlCacheUrl);
        $this->assertCount(0, $wsdlCacheDir->getChildren(), 'Unexpected amount of cached files before loading WSDL');

        // Must be wrapped in a try-catch and shut up because SoapFaults are pseudo-fatal errors that stop PHPUnit
        try {
            @new SoapClient($wsdl);
        } catch (\SoapFault $soapFault) {
            // noop
        }

        $this->assertCount(0, $wsdlCacheDir->getChildren(), 'Invalid WSDL was not deleted from cache');
    }

    /**
     * Test that SOAPFaults are thrown on invalid WSDL files
     *
     * Since SoapFaults are not "real exceptions", we just need to check class, message and other stuff.
     *
     * @dataProvider provideInvalidWSDL
     */
    public function testSoapFaultWhenPassingInvalidWSDLs($wsdl)
    {
        try {
            @new SoapClient($wsdl);
        } catch (\SoapFault $soapFault) {
            // noop
        }

        $this->assertInstanceOf('SoapFault', $soapFault, 'Invalid type of exception');
        $this->assertMatchesRegularExpression(
            '/SOAP-ERROR: Parsing WSDL: .*/',
            $soapFault->getMessage(),
            'Invalid or incorrect exception message'
        );
        $this->assertStringContainsString('WSDL', $soapFault->faultcode, 'Invalid type of faultcode');
    }

    /**
     * Return invalid WSDL
     *
     * @return array
     */
    public static function provideInvalidWSDL()
    {
        return [
            'HTML' => [
                __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/wsdlinclude/wsdl_invalid_html.xml',
            ],
            'Incomplete' => [
                __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/wsdlinclude/wsdl_invalid_incomplete.xml',
            ],
        ];
    }
}
