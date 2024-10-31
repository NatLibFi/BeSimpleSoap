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

use BeSimple\SoapClient\Curl;
use BeSimple\SoapClient\WsdlDownloader;
use BeSimple\SoapCommon\Cache;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Filesystem\Filesystem;

use function sprintf;

/**
 * WSDL downloader test
 *
 * @author Andreas Schamberger <mail@andreass.net>
 * @author Francis Besset <francis.bessset@gmail.com>
 */
class WsdlDownloaderTest extends AbstractWebserverTest
{
    protected static $filesystem;

    protected static $fixturesPath;

    /**
     * Test download to VFS
     *
     * @dataProvider provideDownload
     */
    public function testDownloadDownloadsToVfs($source, $regexp, $nbDownloads)
    {
        $wsdlCacheDir = vfsStream::setup('wsdl');
        $wsdlCacheUrl = $wsdlCacheDir->url('wsdl');

        Cache::setEnabled(Cache::ENABLED);
        Cache::setDirectory($wsdlCacheUrl);
        $cacheDirForRegExp = preg_quote($wsdlCacheUrl, '#');

        $wsdlDownloader = new WsdlDownloader(new Curl([
            'proxy_host' => false,
        ]));
        $this->assertCount(0, $wsdlCacheDir->getChildren());

        $cacheFileName = $wsdlDownloader->download($source);
        $this->assertCount($nbDownloads, $wsdlCacheDir->getChildren());

        $this->assertIsReadable($cacheFileName);

        //Test that the Cache filename is valid
        $regexp = '#' . sprintf($regexp, $cacheDirForRegExp) . '#';
        $this->assertMatchesRegularExpression($regexp, $cacheFileName);
    }

    public static function provideDownload()
    {
        return [
            [
                __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/build_include/xsdinctest_absolute.xml',
                '%s/wsdl_[a-f0-9]{32}\.cache',
                2,
            ],
            [
                __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/xsdinclude/xsdinctest_relative.xml',
                '%s/wsdl_[a-f0-9]{32}\.cache',
                1,
            ],
            [
                sprintf('http://localhost:%d/build_include/xsdinctest_absolute.xml', WEBSERVER_PORT),
                '%s/wsdl_[a-f0-9]{32}\.cache',
                2,
            ],
            [
                sprintf('http://localhost:%d/xsdinclude/xsdinctest_relative.xml', WEBSERVER_PORT),
                '%s/wsdl_[a-f0-9]{32}\.cache',
                2,
            ],
        ];
    }

    public function testIsRemoteFile()
    {
        $wsdlDownloader = new WsdlDownloader(new Curl());

        $r = new \ReflectionClass($wsdlDownloader);
        $m = $r->getMethod('isRemoteFile');
        $m->setAccessible(true);

        $this->assertTrue($m->invoke($wsdlDownloader, 'http://www.php.net/'));
        $this->assertTrue($m->invoke($wsdlDownloader, 'http://localhost/'));
        $this->assertTrue($m->invoke($wsdlDownloader, 'http://mylocaldomain/'));
        $this->assertTrue($m->invoke($wsdlDownloader, 'http://www.php.net/dir/test.html'));
        $this->assertTrue($m->invoke($wsdlDownloader, 'http://localhost/dir/test.html'));
        $this->assertTrue($m->invoke($wsdlDownloader, 'http://mylocaldomain/dir/test.html'));
        $this->assertTrue($m->invoke($wsdlDownloader, 'https://www.php.net/'));
        $this->assertTrue($m->invoke($wsdlDownloader, 'https://localhost/'));
        $this->assertTrue($m->invoke($wsdlDownloader, 'https://mylocaldomain/'));
        $this->assertTrue($m->invoke($wsdlDownloader, 'https://www.php.net/dir/test.html'));
        $this->assertTrue($m->invoke($wsdlDownloader, 'https://localhost/dir/test.html'));
        $this->assertTrue($m->invoke($wsdlDownloader, 'https://mylocaldomain/dir/test.html'));
        $this->assertFalse($m->invoke($wsdlDownloader, 'c:/dir/test.html'));
        $this->assertFalse($m->invoke($wsdlDownloader, '/dir/test.html'));
        $this->assertFalse($m->invoke($wsdlDownloader, '../dir/test.html'));
    }

    /**
     * Test resolving of WSDL includes
     *
     * @dataProvider provideResolveWsdlIncludes
     */
    public function testResolveWsdlIncludes($source, $cacheFile, $remoteParentUrl, $regexp, $nbDownloads)
    {
        $wsdlCacheDir = vfsStream::setup('wsdl');
        $wsdlCacheUrl = $wsdlCacheDir->url('wsdl');

        Cache::setEnabled(Cache::ENABLED);
        Cache::setDirectory($wsdlCacheUrl);
        $cacheDirForRegExp = preg_quote($wsdlCacheUrl, '#');

        $wsdlDownloader = new WsdlDownloader(new Curl([
            'proxy_host' => false,
        ]));
        $r = new \ReflectionClass($wsdlDownloader);
        $m = $r->getMethod('resolveRemoteIncludes');
        $m->setAccessible(true);

        $this->assertCount(0, $wsdlCacheDir->getChildren());

        $cacheFile = sprintf($cacheFile, $wsdlCacheUrl);
        $m->invoke($wsdlDownloader, file_get_contents($source), $cacheFile, $remoteParentUrl);
        $this->assertCount($nbDownloads, $wsdlCacheDir->getChildren());

        $this->assertMatchesRegularExpression(
            '#' . sprintf($regexp, $cacheDirForRegExp) . '#',
            file_get_contents($cacheFile)
        );
    }

    /**
     * Data provider for testResolveWsdlIncludes
     */
    public static function provideResolveWsdlIncludes()
    {
        $remoteUrlAbsolute = sprintf('http://localhost:%d/build_include/wsdlinctest_absolute.xml', WEBSERVER_PORT);
        $remoteUrlRelative = sprintf('http://localhost:%d/wsdlinclude/wsdlinctest_relative.xml', WEBSERVER_PORT);
        $wsdlIncludeMd5 = md5('http://' . sprintf('localhost:%d', WEBSERVER_PORT) . '/wsdl_include.wsdl');
        $expectedWsdl = 'wsdl_' . $wsdlIncludeMd5 . '.cache';

        return [
            [
                __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/build_include/wsdlinctest_absolute.xml',
                '%s/cache_local_absolute.xml',
                null,
                $expectedWsdl,
                2,
            ],
            [
                __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/wsdlinclude/wsdlinctest_relative.xml',
                '%s/cache_local_relative.xml',
                null,
                '\.\./wsdl_include\.wsdl',
                1,
            ],
            [
                $remoteUrlAbsolute,
                '%s/cache_remote_absolute.xml',
                $remoteUrlAbsolute,
                $expectedWsdl,
                2,
            ],
            [
                $remoteUrlRelative,
                '%s/cache_remote_relative.xml',
                $remoteUrlRelative,
                $expectedWsdl,
                2,
            ],
        ];
    }

    /**
     * Test resolving of XSD includes
     *
     * @dataProvider provideResolveXsdIncludes
     */
    public function testResolveXsdIncludes($source, $cacheFile, $remoteParentUrl, $regexp, $nbDownloads)
    {
        $wsdlCacheDir = vfsStream::setup('wsdl');
        $wsdlCacheUrl = $wsdlCacheDir->url('wsdl');

        Cache::setEnabled(Cache::ENABLED);
        Cache::setDirectory($wsdlCacheUrl);
        $cacheDirForRegExp = preg_quote($wsdlCacheUrl, '#');

        $wsdlDownloader = new WsdlDownloader(new Curl([
            'proxy_host' => false,
        ]));
        $r = new \ReflectionClass($wsdlDownloader);
        $m = $r->getMethod('resolveRemoteIncludes');
        $m->setAccessible(true);

        $this->assertCount(0, $wsdlCacheDir->getChildren());

        $cacheFile = sprintf($cacheFile, $wsdlCacheUrl);
        $m->invoke($wsdlDownloader, file_get_contents($source), $cacheFile, $remoteParentUrl);
        $this->assertCount($nbDownloads, $wsdlCacheDir->getChildren());

        $this->assertMatchesRegularExpression(
            '#' . sprintf($regexp, $cacheDirForRegExp) . '#',
            file_get_contents($cacheFile)
        );
    }

    /**
     * Data provider for testResolveXsdIncludes
     */
    public static function provideResolveXsdIncludes()
    {
        $remoteUrlAbsolute = sprintf('http://localhost:%d/build_include/xsdinctest_absolute.xml', WEBSERVER_PORT);
        $remoteUrlRelative = sprintf('http://localhost:%d/xsdinclude/xsdinctest_relative.xml', WEBSERVER_PORT);
        $xsdIncludeMd5 = md5('http://' . sprintf('localhost:%d', WEBSERVER_PORT) . '/type_include.xsd');
        $expectedXsd = 'wsdl_' . $xsdIncludeMd5 . '.cache';

        return [
            [
                __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/build_include/xsdinctest_absolute.xml',
                '%s/cache_local_absolute.xml',
                null,
                $expectedXsd,
                2,
            ],
            [
                __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/xsdinclude/xsdinctest_relative.xml',
                '%s/cache_local_relative.xml',
                null,
                '\.\./type_include\.xsd',
                1,
            ],
            [
                $remoteUrlAbsolute,
                '%s/cache_remote_absolute.xml',
                $remoteUrlAbsolute,
                $expectedXsd,
                2,
            ],
            [
                $remoteUrlRelative,
                '%s/cache_remote_relative.xml',
                $remoteUrlRelative,
                $expectedXsd,
                2,
            ],
        ];
    }

    /**
     * Test relative path resolution
     */
    public function testResolveRelativePathInUrl()
    {
        $wsdlDownloader = new WsdlDownloader(new Curl());

        $r = new \ReflectionClass($wsdlDownloader);
        $m = $r->getMethod('resolveRelativePathInUrl');
        $m->setAccessible(true);

        $this->assertEquals(
            'http://localhost:8080/test',
            $m->invoke($wsdlDownloader, 'http://localhost:8080/sub', '/test')
        );
        $this->assertEquals(
            'http://localhost:8080/test',
            $m->invoke($wsdlDownloader, 'http://localhost:8080/sub/', '/test')
        );

        $this->assertEquals('http://localhost/test', $m->invoke($wsdlDownloader, 'http://localhost/sub', '/test'));
        $this->assertEquals('http://localhost/test', $m->invoke($wsdlDownloader, 'http://localhost/sub/', '/test'));

        $this->assertEquals('http://localhost/test', $m->invoke($wsdlDownloader, 'http://localhost', './test'));
        $this->assertEquals('http://localhost/test', $m->invoke($wsdlDownloader, 'http://localhost/', './test'));

        $this->assertEquals(
            'http://localhost/sub/test',
            $m->invoke($wsdlDownloader, 'http://localhost/sub/sub', './test')
        );
        $this->assertEquals(
            'http://localhost/sub/sub/test',
            $m->invoke($wsdlDownloader, 'http://localhost/sub/sub/', './test')
        );

        $this->assertEquals(
            'http://localhost/test',
            $m->invoke($wsdlDownloader, 'http://localhost/sub/sub', '../test')
        );
        $this->assertEquals(
            'http://localhost/sub/test',
            $m->invoke($wsdlDownloader, 'http://localhost/sub/sub/', '../test')
        );

        $this->assertEquals(
            'http://localhost/test',
            $m->invoke($wsdlDownloader, 'http://localhost/sub/sub/sub', '../../test')
        );
        $this->assertEquals(
            'http://localhost/sub/test',
            $m->invoke($wsdlDownloader, 'http://localhost/sub/sub/sub/', '../../test')
        );

        $this->assertEquals(
            'http://localhost/test',
            $m->invoke($wsdlDownloader, 'http://localhost/sub/sub/sub/sub', '../../../test')
        );
        $this->assertEquals(
            'http://localhost/sub/test',
            $m->invoke($wsdlDownloader, 'http://localhost/sub/sub/sub/sub/', '../../../test')
        );

        $this->assertEquals(
            'http://localhost/test',
            $m->invoke($wsdlDownloader, 'http://localhost/sub/sub/sub', '../../../test')
        );
        $this->assertEquals(
            'http://localhost/test',
            $m->invoke($wsdlDownloader, 'http://localhost/sub/sub/sub/', '../../../test')
        );
    }

    /**
     * Test that non HTTP 200 responses throw an exception
     *
     * @dataProvider invalidResponseCodesDataProvider
     *
     * @throws \ErrorException
     */
    public function testInvalidResponseCodes($responseCode)
    {
        $this->expectException('ErrorException');
        $this->expectExceptionMessage(
            'SOAP-ERROR: Parsing WSDL: Unexpected response code received from \'http://somefake.url/wsdl\', '
            . 'response code: ' . $responseCode
        );

        $curlMock = $this->createMock('BeSimple\SoapClient\Curl');
        $curlMock->expects($this->any())
            ->method('getResponseStatusCode')
            ->willReturn($responseCode);

        $wsdlDownloader = new WsdlDownloader($curlMock);

        $wsdlDownloader->download('http://somefake.url/wsdl');
    }

    public static function invalidResponseCodesDataProvider()
    {
        return [
            'No Content' => [204],
            'Moved Permanently' => [301],
            'Found' => [302],
            'Unathorized' => [401],
            'Not Found' => [404],
            'Internal Server Error' => [500],
        ];
    }

    /**
     * Test that HTTP 200 responses downloads and stores the WSDL correctly
     */
    public function testValidResponseCode()
    {
        $curlMock = $this->createMock('BeSimple\SoapClient\Curl');
        $curlMock->expects($this->any())
            ->method('getResponseStatusCode')
            ->willReturn(200);
        $curlMock->expects($this->once())
            ->method('getResponseBody')
            ->willReturn(
                '<?xml version="1.0"?><wsdl:types xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" '
                . 'xmlns:xs="http://www.w3.org/2001/XMLSchema"></wsdl:types>'
            );

        $wsdlDownloader = new WsdlDownloader($curlMock);

        $result = $wsdlDownloader->download('http://somefake.url/wsdl');

        $this->assertMatchesRegularExpression('/.*wsdl_[a-f0-9]{32}\.cache/', $result);
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$filesystem = new Filesystem();
        self::$fixturesPath = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR;
        self::$filesystem->mkdir(self::$fixturesPath . 'build_include');

        foreach (['wsdlinclude/wsdlinctest_absolute.xml', 'xsdinclude/xsdinctest_absolute.xml'] as $file) {
            $content = file_get_contents(self::$fixturesPath . $file);
            $content = preg_replace(
                '#' . preg_quote('%location%') . '#',
                sprintf('localhost:%d', WEBSERVER_PORT),
                $content
            );

            self::$filesystem->dumpFile(
                self::$fixturesPath . 'build_include' . DIRECTORY_SEPARATOR . pathinfo($file, PATHINFO_BASENAME),
                $content
            );
        }
    }

    /**
     * Cleanup
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        self::$filesystem->remove(self::$fixturesPath . 'build_include');
    }
}
