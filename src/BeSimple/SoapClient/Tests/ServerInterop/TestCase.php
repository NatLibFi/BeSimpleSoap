<?php

namespace BeSimple\SoapClient\Tests\ServerInterop;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $ch = curl_init('http://localhost:8081/');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (curl_exec($ch) === false) {
            $this->markTestSkipped(
                'The PHP webserver is not started on port 8081.'
            );
        }

        curl_close($ch);
    }
}
