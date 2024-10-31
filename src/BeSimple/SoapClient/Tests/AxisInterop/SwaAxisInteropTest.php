<?php

/*
* Deploy "axis_services/besimple-swa.aar" to Apache Axis2 to get this
* example to work.
*
* Run ant to rebuild aar.
*
* Example based on:
* http://axis.apache.org/axis2/java/core/docs/mtom-guide.html#a3
* http://wso2.org/library/1675
*
* Doesn't work directly with ?wsdl served by Apache Axis!
*
*/

namespace BeSimple\SoapClient\Tests\AxisInterop;

use BeSimple\SoapClient\SoapClient as BeSimpleSoapClient;
use BeSimple\SoapClient\Tests\AxisInterop\Fixtures\DownloadFile;
use BeSimple\SoapClient\Tests\AxisInterop\Fixtures\UploadFile;
use BeSimple\SoapCommon\Helper as BeSimpleSoapHelper;

class SwaAxisInteropTest extends TestCase
{
    private $options = [
        'soap_version'    => SOAP_1_1,
        'features'        => SOAP_SINGLE_ELEMENT_ARRAYS, // make sure that result is array for size=1
        'attachment_type' => BeSimpleSoapHelper::ATTACHMENTS_TYPE_SWA,
        'cache_wsdl'      => WSDL_CACHE_NONE,
        'classmap'        => [
            'downloadFile'         => 'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\DownloadFile',
            'downloadFileResponse' => 'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\DownloadFileResponse',
            'uploadFile'           => 'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\UploadFile',
            'uploadFileResponse'   => 'BeSimple\SoapClient\Tests\AxisInterop\Fixtures\UploadFileResponse',
        ],
        'proxy_host' => false,
    ];

    public function testUploadDownloadText()
    {
        $sc = new BeSimpleSoapClient(__DIR__ . '/Fixtures/SwA.wsdl', $this->options);

        $upload = new UploadFile();
        $upload->name = 'upload.txt';
        $upload->data = 'This is a test. :)';
        $result = $sc->uploadFile($upload);

        $this->assertEquals('File saved succesfully.', $result->return);

        $download = new DownloadFile();
        $download->name = 'upload.txt';
        $result = $sc->downloadFile($download);

        $this->assertEquals($upload->data, $result->data);
    }

    public function testUploadDownloadImage()
    {
        $sc = new BeSimpleSoapClient(__DIR__ . '/Fixtures/SwA.wsdl', $this->options);

        $upload = new UploadFile();
        $upload->name = 'image.jpg';
        // source: http://www.freeimageslive.com/galleries/light/pics/swirl3768.jpg;
        $upload->data = file_get_contents(__DIR__ . '/Fixtures/image.jpg');
        $result = $sc->uploadFile($upload);

        $this->assertEquals('File saved succesfully.', $result->return);

        $download = new DownloadFile();
        $download->name = 'image.jpg';
        $result = $sc->downloadFile($download);

        $this->assertEquals($upload->data, $result->data);
    }
}
