#!/bin/bash -e

DIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR

php -S localhost:8081 -t "$DIR/.." &
PHP_PID=$!
echo "Started process $PHP_PID"

echo "Waiting until PHP webserver is ready on port 8081"
while [[ -z `curl -s 'http://localhost:8081/' ` ]]
do
    echo -n "."
    sleep 1
done

echo "PHP webserver is up (pid $PHP_PID)"
