#!/bin/bash

DIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR

echo "Start PHP server"
php -S localhost:8081 -t "$DIR/.."
