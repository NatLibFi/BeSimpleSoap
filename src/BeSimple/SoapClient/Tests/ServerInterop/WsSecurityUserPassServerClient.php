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

require '../../../../../vendor/autoload.php';

use BeSimple\SoapClient\SoapClient as BeSimpleSoapClient;
use BeSimple\SoapClient\WsSecurityFilter as BeSimpleWsSecurityFilter;

use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\GetBook;
use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\AddBook;

$options = array(
    'soap_version' => SOAP_1_2,
    'features'     => SOAP_SINGLE_ELEMENT_ARRAYS, // make sure that result is array for size=1
    'trace'           => true, // enables use of the SoapClient->__getLast* methods
    'classmap'     => array(
        'getBook'                => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\GetBook',
        'getBookResponse'        => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\GetBookResponse',
        'getBooksByType'         => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\GetBooksByType',
        'getBooksByTypeResponse' => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\GetBooksByTypeResponse',
        'addBook'                => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\AddBook',
        'addBookResponse'        => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\AddBookResponse',
        'BookInformation'        => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\BookInformation',
    ),
);

$sc = new BeSimpleSoapClient(__DIR__ . '/Fixtures/WsSecurityUserPass.wsdl', $options);

//var_dump($sc->__getFunctions());
//var_dump($sc->__getTypes());

try {
    $wssFilter = new BeSimpleWsSecurityFilter(true, 600);
    $wssFilter->addUserData('libuser', 'books', BeSimpleWsSecurityFilter::PASSWORD_TYPE_DIGEST);

    $soapKernel = $sc->getSoapKernel();
    $soapKernel->registerFilter($wssFilter);

    $gb = new getBook();
    $gb->isbn = '0061020052';
    $result = $sc->getBook($gb);
    var_dump($result->getBookReturn);

    $ab = new addBook();
    $ab->isbn = '0445203498';
    $ab->title = 'The Dragon Never Sleeps';
    $ab->author = 'Cook, Glen';
    $ab->type = 'scifi';

    var_dump($sc->addBook($ab));
} catch (Exception $e) {
    var_dump($e);
}

// var_dump(
//     $sc->__getLastRequestHeaders(),
//     $sc->__getLastRequest(),
//     $sc->__getLastResponseHeaders(),
//     $sc->__getLastResponse()
// );
