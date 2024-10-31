<?php

require '../../../../../vendor/autoload.php';

use BeSimple\SoapClient\SoapClient as BeSimpleSoapClient;
use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\AttachmentRequest;

use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\Base64Binary;
use BeSimple\SoapCommon\Helper as BeSimpleSoapHelper;

$options = [
    'soap_version'    => SOAP_1_1,
    'features'        => SOAP_SINGLE_ELEMENT_ARRAYS, // make sure that result is array for size=1
    'trace'           => true, // enables use of the SoapClient->__getLast* methods
    'attachment_type' => BeSimpleSoapHelper::ATTACHMENTS_TYPE_MTOM,
    'cache_wsdl'      => WSDL_CACHE_NONE,
    'classmap'        => [
        'base64Binary'      => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\Base64Binary',
        'AttachmentRequest' => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\AttachmentRequest',
    ],
    'connection_timeout' => 1,
];

$sc = new BeSimpleSoapClient('Fixtures/MTOM.wsdl', $options);

//var_dump($sc->__getFunctions());
//var_dump($sc->__getTypes());

try {
    $b64 = new Base64Binary();
    $b64->_ = 'This is a test. :)';
    $b64->contentType = 'text/plain';

    $attachment = new AttachmentRequest();
    $attachment->fileName = 'test123.txt';
    $attachment->binaryData = $b64;

    var_dump($sc->attachment($attachment));
} catch (Exception $e) {
    var_dump($e);
}

// var_dump(
//     $sc->__getLastRequestHeaders(),
//     $sc->__getLastRequest(),
//     $sc->__getLastResponseHeaders(),
//     $sc->__getLastResponse()
// );
