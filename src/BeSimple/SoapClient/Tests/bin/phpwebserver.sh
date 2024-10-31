#!/bin/bash -e

FG=0
while getopts ":f" option; do
   case $option in
      f)
         FG=1
         ;;
     \?) # Invalid option
         echo "Error: Invalid option $option"
         exit;;
   esac
done

DIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR

if (( "$FG" == "1" ))
then
    echo "Starting PHP webserver in foregroung"
    php -S localhost:8081 -t "$DIR/.."
    exit 0
else
    php -S localhost:8081 -t "$DIR/.." &
fi
PHP_PID=$!
echo "Started process $PHP_PID"

echo "Waiting until PHP webserver is ready on port 8081"
while [[ -z `curl -s 'http://localhost:8081/' ` ]]
do
    echo -n "."
    sleep 1
done

echo "PHP webserver is up (pid $PHP_PID)"
