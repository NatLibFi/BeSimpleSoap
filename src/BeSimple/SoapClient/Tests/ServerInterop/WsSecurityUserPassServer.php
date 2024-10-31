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

use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\AddBook;
use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\AddBookResponse;

use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\BookInformation;
use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\GetBook;
use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\GetBookResponse;
use BeSimple\SoapServer\SoapServer as BeSimpleSoapServer;
use BeSimple\SoapServer\WsSecurityFilter as BeSimpleWsSecurityFilter;

$options = [
    'soap_version'    => SOAP_1_1,
    'features'        => SOAP_SINGLE_ELEMENT_ARRAYS, // make sure that result is array for size=1
    'cache_wsdl'      => WSDL_CACHE_NONE,
    'classmap'        => [
        'getBook'                => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\GetBook',
        'getBookResponse'        => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\GetBookResponse',
        'getBooksByType'         => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\GetBooksByType',
        'getBooksByTypeResponse' => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\GetBooksByTypeResponse',
        'addBook'                => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\AddBook',
        'addBookResponse'        => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\AddBookResponse',
        'BookInformation'        => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\BookInformation',
    ],
];

class Auth
{
    public static function usernamePasswordCallback($user)
    {
        if ($user == 'libuser') {
            return 'books';
        }

        return null;
    }
}

class WsSecurityUserPassServer
{
    public function getBook(GetBook $gb)
    {
        $bi = new BookInformation();
        $bi->isbn = $gb->isbn;
        $bi->title = 'title';
        $bi->author = 'author';
        $bi->type = 'scifi';

        $br = new GetBookResponse();
        $br->getBookReturn = $bi;

        return $br;
    }

    public function addBook(addBook $ab)
    {
        $abr = new addBookResponse();
        $abr->addBookReturn = true;

        return $abr;
    }
}

$ss = new BeSimpleSoapServer(__DIR__ . '/Fixtures/WsSecurityUserPass.wsdl', $options);

$wssFilter = new BeSimpleWsSecurityFilter();
$wssFilter->setUsernamePasswordCallback([Auth::class, 'usernamePasswordCallback']);

$soapKernel = $ss->getSoapKernel();
$soapKernel->registerFilter($wssFilter);

$ss->setClass(WsSecurityUserPassServer::class);
$ss->handle();
