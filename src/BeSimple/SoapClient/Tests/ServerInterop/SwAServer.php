<?php

require '../../../../../vendor/autoload.php';

use BeSimple\SoapCommon\Helper as BeSimpleSoapHelper;
use BeSimple\SoapServer\SoapServer as BeSimpleSoapServer;

use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\UploadFile;
use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\UploadFileResponse;
use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\DownloadFile;
use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\DownloadFileResponse;

$options = array(
    'soap_version'    => SOAP_1_1,
    'features'        => SOAP_SINGLE_ELEMENT_ARRAYS, // make sure that result is array for size=1
    'attachment_type' => BeSimpleSoapHelper::ATTACHMENTS_TYPE_SWA,
    'cache_wsdl'      => WSDL_CACHE_NONE,
    'classmap'        => array(
        'downloadFile'         => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\DownloadFile',
        'downloadFileResponse' => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\DownloadFileResponse',
        'uploadFile'           => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\UploadFile',
        'uploadFileResponse'   => 'BeSimple\SoapClient\Tests\ServerInterop\Fixtures\UploadFileResponse',
    ),
);

class SwA
{
    public function uploadFile(uploadFile $uploadFile)
    {
        file_put_contents(__DIR__ . '/' . $uploadFile->name, $uploadFile->data);

        $ufr = new UploadFileResponse();
        $ufr->return = 'File saved succesfully.';

        return $ufr;
    }

    public function downloadFile(downloadFile $downloadFile)
    {
        $dfr = new DownloadFileResponse();
        $dfr->data = file_get_contents(__DIR__ . '/' . $downloadFile->name);

        return $dfr;
    }
}

$ss = new BeSimpleSoapServer(__DIR__ . '/Fixtures/SwA.wsdl', $options);
$ss->setClass('SwA');
$ss->handle();
