<?php

/*
 * This file is part of the BeSimpleSoapClient.
 *
 * (c) Christian Kerl <christian-kerl@web.de>
 * (c) Francis Besset <francis.besset@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace BeSimple\SoapClient\Tests;

use BeSimple\SoapClient\Curl;

/**
 * @author Andreas Schamberger <mail@andreass.net>
 */
class CurlTest extends AbstractWebserverTest
{
    public function testExec()
    {
        $curl = new Curl(array(
            'proxy_host' => false,
        ));


        $this->assertTrue($curl->exec(sprintf('http://localhost:%d/curl.txt', WEBSERVER_PORT)));
        $this->assertTrue($curl->exec(sprintf('http://localhost:%d/404.txt', WEBSERVER_PORT)));
    }

    public function testGetErrorMessage()
    {
        $curl = new Curl(array(
            'proxy_host' => false,
        ));

        $curl->exec('http://unknown/curl.txt');
        $this->assertRegExp('/^Could not connect to host.*$/', $curl->getErrorMessage());

        $curl->exec(sprintf('xyz://localhost:%d/@404.txt', WEBSERVER_PORT));
        $this->assertRegExp('/^Unknown protocol. Only http and https are allowed.*$/', $curl->getErrorMessage());

        $curl->exec('');
        $this->assertRegExp('/^Unable to parse URL.*$/', $curl->getErrorMessage());
    }

    public function testGetRequestHeaders()
    {
        $curl = new Curl(array(
            'proxy_host' => false,
        ));

        $curl->exec(sprintf('http://localhost:%d/curl.txt', WEBSERVER_PORT));
        $this->assertCorrectRequestHeaders($curl->getRequestHeaders(), '/curl.txt');

        $curl->exec(sprintf('http://localhost:%s/404.txt', WEBSERVER_PORT));
        $this->assertCorrectRequestHeaders($curl->getRequestHeaders(), '/404.txt');
    }

    public function testGetResponse()
    {
        $curl = new Curl(array(
            'proxy_host' => false,
        ));

        $curl->exec(sprintf('http://localhost:%d/curl.txt', WEBSERVER_PORT));
        $this->assertSame('OK', $curl->getResponseStatusMessage());

        $response = $curl->getResponse();
        [$headers, $body] = explode("\r\n\r\n", $response, 2);
        $this->assertCorrectResponseHeaders($headers, '200 OK', 'text/plain', strlen($body));
        $this->assertSame('This is a testfile for cURL.', $body);

        $curl->exec(sprintf('http://localhost:%d/404.txt', WEBSERVER_PORT));
        $this->assertSame('Not Found', $curl->getResponseStatusMessage());
    }

    public function testGetResponseBody()
    {
        $curl = new Curl(array(
            'proxy_host' => false,
        ));

        $curl->exec(sprintf('http://localhost:%d/curl.txt', WEBSERVER_PORT));
        $this->assertEquals('This is a testfile for cURL.', $curl->getResponseBody());
    }

    public function testGetResponseContentType()
    {
        $curl = new Curl(array(
            'proxy_host' => false,
        ));

        $curl->exec(sprintf('http://localhost:%d/curl.txt', WEBSERVER_PORT));
        $this->assertEquals('text/plain; charset=UTF-8', $curl->getResponseContentType());

        $curl->exec(sprintf('http://localhost:%d/404.txt', WEBSERVER_PORT));
        $this->assertEquals('text/html; charset=UTF-8', $curl->getResponseContentType());
    }

    public function testGetResponseHeaders()
    {
        $curl = new Curl(array(
            'proxy_host' => false,
        ));

        $curl->exec(sprintf('http://localhost:%d/curl.txt', WEBSERVER_PORT));
        $headers = $curl->getResponseHeaders();
        $this->assertCorrectResponseHeaders($headers, '200 OK', 'text/plain');

        $curl->exec(sprintf('http://localhost:%d/404.txt', WEBSERVER_PORT));
        $headers = $curl->getResponseHeaders();
        $this->assertCorrectResponseHeaders($headers, '404 Not Found', 'text/html');
    }

    public function testGetResponseStatusCode()
    {
        $curl = new Curl(array(
            'proxy_host' => false,
        ));

        $curl->exec(sprintf('http://localhost:%d/curl.txt', WEBSERVER_PORT));
        $this->assertEquals(200, $curl->getResponseStatusCode());

        $curl->exec(sprintf('http://localhost:%d/404.txt', WEBSERVER_PORT));
        $this->assertEquals(404, $curl->getResponseStatusCode());
    }

    protected function assertCorrectRequestHeaders($headerStr, $requestPath)
    {
        $headers = explode("\r\n", $headerStr);
        $this->assertContains("GET $requestPath HTTP/1.1", $headers);
        $this->assertContains('User-Agent: PHP-SOAP/\BeSimple\SoapClient', $headers);
        $this->assertContains('Accept: */*', $headers);
    }

    protected function assertCorrectResponseHeaders($headerStr, $status, $contentType, $contentLen = null)
    {
        $headers = explode("\r\n", $headerStr);
        $this->assertContains("HTTP/1.1 $status", $headers);
        $this->assertContains("Content-Type: $contentType; charset=UTF-8", $headers);
        if (null !== $contentLen) {
            $this->assertContains("Content-Length: $contentLen", $headers);
        }
    }
}
