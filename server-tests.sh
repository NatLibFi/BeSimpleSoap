#!/bin/bash

./src/BeSimple/SoapClient/Tests/bin/phpwebserver.sh
./src/BeSimple/SoapClient/Tests/bin/axis.sh
./bin/simple-phpunit
