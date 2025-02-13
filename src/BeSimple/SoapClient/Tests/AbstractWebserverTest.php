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

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function sprintf;
use function strlen;

/**
 * Abstract base class for web server tests
 *
 * @author francis.besset@gmail.com <francis.besset@gmail.com>
 */
abstract class AbstractWebServerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Web server
     *
     * @var Process
     */
    protected static $webserver;

    /**
     * Web server port length in characters
     *
     * @var int
     */
    protected static $webserverPortLength;

    /**
     * Setup method
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        $phpFinder = new PhpExecutableFinder();
        self::$webserver = new Process([
            $phpFinder->find(),
            '-S',
            sprintf('localhost:%d', WEBSERVER_PORT),
            '-t',
            __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures',
        ]);

        self::$webserver->start();
        usleep(200000);

        self::$webserverPortLength = strlen(WEBSERVER_PORT);
    }

    public static function tearDownAfterClass(): void
    {
        self::$webserver->stop(0);
        usleep(100000);
    }
}
