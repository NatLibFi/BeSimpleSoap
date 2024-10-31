<?php

error_reporting(0);

require '../../../../../vendor/autoload.php';

use ass\XmlSecurity\Key as XmlSecurityKey;

use BeSimple\SoapClient\SoapClient as BeSimpleSoapClient;
use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\AddBook;
use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\GetBook;

use BeSimple\SoapClient\WsSecurityFilter as BeSimpleWsSecurityFilter;
use BeSimple\SoapCommon\WsSecurityKey as BeSimpleWsSecurityKey;

$options = [
    'soap_version' => SOAP_1_2,
    'features'     => SOAP_SINGLE_ELEMENT_ARRAYS, // make sure that result is array for size=1
    'trace'           => true, // enables use of the SoapClient->__getLast* methods
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

$sc = new BeSimpleSoapClient(__DIR__ . '/Fixtures/WsSecuritySigEnc.wsdl', $options);

//var_dump($sc->__getFunctions());
//var_dump($sc->__getTypes());

try {
    $wssFilter = new BeSimpleWsSecurityFilter();
    // user key for signature and encryption
    $securityKeyUser = new BeSimpleWsSecurityKey();
    $securityKeyUser->addPrivateKey(XmlSecurityKey::RSA_SHA1, __DIR__ . '/Fixtures/clientkey.pem', true);
    $securityKeyUser->addPublicKey(XmlSecurityKey::RSA_SHA1, __DIR__ . '/Fixtures/clientcert.pem', true);
    $wssFilter->setUserSecurityKeyObject($securityKeyUser);
    // service key for encryption
    $securityKeyService = new BeSimpleWsSecurityKey();
    $securityKeyService->addPrivateKey(XmlSecurityKey::TRIPLEDES_CBC);
    $securityKeyService->addPublicKey(XmlSecurityKey::RSA_1_5, __DIR__ . '/Fixtures/servercert.pem', true);
    $wssFilter->setServiceSecurityKeyObject($securityKeyService);
    // TOKEN_REFERENCE_SUBJECT_KEY_IDENTIFIER | TOKEN_REFERENCE_SECURITY_TOKEN | TOKEN_REFERENCE_THUMBPRINT_SHA1
    $wssFilter->setSecurityOptionsSignature(BeSimpleWsSecurityFilter::TOKEN_REFERENCE_SECURITY_TOKEN);
    $wssFilter->setSecurityOptionsEncryption(BeSimpleWsSecurityFilter::TOKEN_REFERENCE_THUMBPRINT_SHA1);

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
