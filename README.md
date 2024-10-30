# BeSimpleSoap

Forked from https://github.com/smartboxgroup/BeSimpleSoap.

Build SOAP and WSDL based web services

[![Latest Stable Version](https://img.shields.io/packagist/v/natlibfi/besimple-soap.svg?style=flat-square)](https://packagist.org/packages/natlibfi/besimple-soap)
[![Minimum PHP Version](https://img.shields.io/badge/php-~%207.4-8892BF.svg?style=flat-square)](https://php.net/)
[![CI](https://github.com/NatLibFi/BeSimpleSoap/actions/workflows/ci.yaml/badge.svg)](https://github.com/NatLibFi/BeSimpleSoap/actions/workflows/ci.yaml)

# Components

BeSimpleSoap consists of five components as described below.

## BeSimpleSoapBundle

The BeSimpleSoapBundle is a Symfony2 bundle to build WSDL and SOAP based web services.
For further information see the [README](https://github.com/NatLibFi/BeSimpleSoap/blob/dev/src/BeSimple/SoapBundle/README.md).

## BeSimpleSoapClient

The BeSimpleSoapClient is a component that extends the native PHP SoapClient with further features like SwA, MTOM and WS-Security.
For further information see the [README](https://github.com/NatLibFi/BeSimpleSoap/blob/dev/src/BeSimple/SoapClient/README.md).

## BeSimpleSoapCommon

The BeSimpleSoapCommon component contains functionylity shared by both the server and client implementations.
For further information see the [README](https://github.com/NatLibFi/BeSimpleSoap/blob/dev/src/BeSimple/SoapCommon/README.md).


## BeSimpleSoapServer

The BeSimpleSoapServer is a component that extends the native PHP SoapServer with further features like SwA, MTOM and WS-Security.
For further information see the [README](https://github.com/NatLibFi/BeSimpleSoap/blob/dev/src/BeSimple/SoapServer/README.md).

## BeSimpleSoapWsdl

For further information see the [README](https://github.com/NatLibFi/BeSimpleSoap/blob/dev/src/BeSimple/SoapWsdl/README.md).

# Installation

If you do not yet have composer, install it like this:

```sh
curl -s http://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin
```

Create a `composer.json` file:

```json
{
    "require": {
        "natlibfi/besimple-soap": "dev-main"
    }
}
```

Now you are ready to install the library:

```sh
php /usr/local/bin/composer.phar install
```

# Information for Developers

Makefile contains different commands for running tests in a Docker container. Example:

`make start`

## Running tests locally

Tests that don't require a running server can be run locally with the following command:

`COMPOSER_BINARY=/path/to/composer.phar bin/simple-phpunit`

To start the servers and run all tests, run the following commands first, then the one above:

`src/BeSimple/SoapClient/Tests/bin/axis.sh`
`src/BeSimple/SoapClient/Tests/bin/phpwebserver.sh`
