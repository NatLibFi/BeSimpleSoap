# BeSimpleSoap

Forked from https://github.com/smartboxgroup/BeSimpleSoap.

Build SOAP and WSDL based web services

[![Latest Stable Version](https://img.shields.io/packagist/v/natlibfi/besimple-soap.svg?style=flat-square)](https://packagist.org/packages/natlibfi/besimple-soap)
[![Minimum PHP Version](https://img.shields.io/badge/php-~%207.4-8892BF.svg?style=flat-square)](https://php.net/)
[![CI](https://github.com/NatLibFi/BeSimpleSoap/actions/workflows/ci.yaml/badge.svg)](https://github.com/NatLibFi/BeSimpleSoap/actions/workflows/ci.yaml)

# Components

BeSimpleSoap consists of four components as described below.


## BeSimple\SoapClient

The BeSimpleSoapClient is a component that extends the native PHP SoapClient with further features like SwA, MTOM and WS-Security.

### Features (only subsets of the linked specs implemented)

* SwA: SOAP Messages with Attachments [Spec](http://www.w3.org/TR/SOAP-attachments)
* MTOM: SOAP Message Transmission Optimization Mechanism [Spec](http://www.w3.org/TR/soap12-mtom/)
* WS-Security [Spec1](http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0.pdf), [Spec2](http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0.pdf)
* WS-Adressing [Spec](http://www.w3.org/2002/ws/addr/)

### Usage

See [PHP documentation](https://www.php.net/manual/en/class.soapclient.php) for more information on how to use the library.


## BeSimple\SoapServer

The BeSimpleSoapServer is a component that extends the native PHP SoapServer with further features like SwA, MTOM and WS-Security.

### Features (only subsets of the linked specs implemented)

* SwA: SOAP Messages with Attachments [Spec](http://www.w3.org/TR/SOAP-attachments)
* MTOM: SOAP Message Transmission Optimization Mechanism [Spec](http://www.w3.org/TR/soap12-mtom/)
* WS-Security [Spec1](http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0.pdf), [Spec2](http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0.pdf)

### Usage

See [PHP documentation](https://www.php.net/manual/en/class.soapserver.php) for more information on how to use the library.


## BeSimple\SoapCommon

The BeSimpleSoapCommon component contains functionality shared by both the server and client implementations.

### Features

* Common interfaces for SoapClient and SoapServer input/output processing flow
* MIME parser for SwA and MTOM implementation
* Soap type converters


## BeSimple\SoapWsdl

WSDL support classes.


# Information for Developers

Makefile contains different commands for running tests e.g. in a Docker container. Examples:

`make start`

`make qa`

## Running tests locally

Tests that don't require a running server can be run locally with the following command:

`COMPOSER_BINARY=/path/to/composer.phar bin/simple-phpunit`

To start the servers and run all tests, run the following commands first, then the one above:

`src/BeSimple/SoapClient/Tests/bin/axis.sh`
`src/BeSimple/SoapClient/Tests/bin/phpwebserver.sh`
