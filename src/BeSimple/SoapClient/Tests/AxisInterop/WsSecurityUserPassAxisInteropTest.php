<?php

/*
* Deploy "axis_services/library-username-digest.aar" to Apache Axis2 to get
* this example to work.
*
* Using code from axis example:
* http://www.ibm.com/developerworks/java/library/j-jws4/index.html
*
* build.properties:
* server-policy=hash-policy-server.xml
*
* allows both text and digest!
*/

namespace BeSimple\SoapClient\Tests\AxisInterop;

use BeSimple\SoapClient\SoapClient as BeSimpleSoapClient;
use BeSimple\SoapClient\Tests\AxisInterop\Fixtures\AddBook;
use BeSimple\SoapClient\Tests\AxisInterop\Fixtures\GetBook;
use BeSimple\SoapClient\WsSecurityFilter as BeSimpleWsSecurityFilter;

/**
 * Test class
 */
class WsSecurityUserPassAxisInteropTest extends TestCase
{
    private $options = [
        'soap_version' => SOAP_1_2,
        'features'     => SOAP_SINGLE_ELEMENT_ARRAYS, // make sure that result is array for size=1
        'classmap'     => [
            'getBook'                => 'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\GetBook',
            'getBookResponse'        => 'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\GetBookResponse',
            'getBooksByType'         => 'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\GetBooksByType',
            'getBooksByTypeResponse' => 'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\GetBooksByTypeResponse',
            'addBook'                => 'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\AddBook',
            'addBookResponse'        => 'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\AddBookResponse',
            'BookInformation'        => 'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\BookInformation',
        ],
        'proxy_host' => false,
    ];

    public function testUserPassText()
    {
        $sc = new BeSimpleSoapClient(__DIR__ . '/Fixtures/WsSecurityUserPass.wsdl', $this->options);

        $wssFilter = new BeSimpleWsSecurityFilter(true, 600);
        $wssFilter->addUserData('libuser', 'books', BeSimpleWsSecurityFilter::PASSWORD_TYPE_TEXT);

        $soapKernel = $sc->getSoapKernel();
        $soapKernel->registerFilter($wssFilter);

        $gb = new GetBook();
        $gb->isbn = '0061020052';
        $result = $sc->getBook($gb);
        $this->assertInstanceOf(
            'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\BookInformation',
            $result->getBookReturn
        );

        $ab = new addBook();
        $ab->isbn = '0445203498';
        $ab->title = 'The Dragon Never Sleeps';
        $ab->author = 'Cook, Glen';
        $ab->type = 'scifi';

        $this->assertTrue((bool)$sc->addBook($ab));

        // getBooksByType("scifi");
    }

    public function testUserPassDigest()
    {
        $sc = new BeSimpleSoapClient(__DIR__ . '/Fixtures/WsSecurityUserPass.wsdl', $this->options);

        $wssFilter = new BeSimpleWsSecurityFilter(true, 600);
        $wssFilter->addUserData('libuser', 'books', BeSimpleWsSecurityFilter::PASSWORD_TYPE_DIGEST);

        $soapKernel = $sc->getSoapKernel();
        $soapKernel->registerFilter($wssFilter);

        $gb = new GetBook();
        $gb->isbn = '0061020052';
        $result = $sc->getBook($gb);
        $this->assertInstanceOf(
            'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\BookInformation',
            $result->getBookReturn
        );

        $ab = new addBook();
        $ab->isbn = '0445203498';
        $ab->title = 'The Dragon Never Sleeps';
        $ab->author = 'Cook, Glen';
        $ab->type = 'scifi';

        $this->assertTrue((bool)$sc->addBook($ab));

        // getBooksByType("scifi");
    }
}
