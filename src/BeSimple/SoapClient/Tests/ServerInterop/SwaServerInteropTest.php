<?php

namespace BeSimple\SoapClient\Tests\ServerInterop;

use BeSimple\SoapCommon\Helper as BeSimpleSoapHelper;
use BeSimple\SoapClient\SoapClient as BeSimpleSoapClient;
use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\UploadFile;
use BeSimple\SoapClient\Tests\ServerInterop\Fixtures\DownloadFile;
use BeSimple\SoapClient\Tests\ServerInterop\TestCase;

class SwaServerInteropTest extends TestCase
{
    private $options = array(
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
        'proxy_host' => false,
    );

    public function testUploadDownloadText()
    {
        $sc = new BeSimpleSoapClient(__DIR__ . '/Fixtures/SwA.wsdl', $this->options);

        $upload = new uploadFile();
        $upload->name = 'upload.txt';
        $upload->data = 'This is a test. :)';
        $result = $sc->uploadFile($upload);

        $this->assertEquals('File saved succesfully.', $result->return);

        $download = new downloadFile();
        $download->name = 'upload.txt';
        $result = $sc->downloadFile($download);

        $this->assertEquals($upload->data, $result->data);

        unlink(__DIR__ . '/../ServerInterop/' . $download->name);
    }

    public function testUploadDownloadImage()
    {
        $sc = new BeSimpleSoapClient(__DIR__ . '/Fixtures/SwA.wsdl', $this->options);

        $upload = new uploadFile();
        $upload->name = 'image.jpg';
        // source: http://www.freeimageslive.com/galleries/light/pics/swirl3768.jpg;
        $upload->data = file_get_contents(__DIR__ . '/Fixtures/image.jpg');
        $result = $sc->uploadFile($upload);

        $this->assertEquals('File saved succesfully.', $result->return);

        $download = new downloadFile();
        $download->name = 'image.jpg';
        $result = $sc->downloadFile($download);

        $this->assertEquals($upload->data, $result->data);

        unlink(__DIR__ . '/../ServerInterop/' . $download->name);
    }
}
