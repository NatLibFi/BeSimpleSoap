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

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR

VERSION_AXIS=1.5.1
ZIP_AXIS=axis2-$VERSION_AXIS-bin.zip
if [[ "$VERSION_AXIS" == "1.5.1" ]]; then
    PATH_AXIS=https://archive.apache.org/dist/ws/axis2/1_5_1/axis2-1.5.1-bin.zip
elif [[ "$VERSION_AXIS" > "1.5.1" ]]; then
    PATH_AXIS=https://archive.apache.org/dist/axis/axis2/java/core/$VERSION_AXIS/$ZIP_AXIS
else
    PATH_AXIS=https://archive.apache.org/dist/ws/axis2/${VERSION_AXIS//./_}/$ZIP_AXIS
fi

if [ ! -f "$DIR/$ZIP_AXIS" ]; then
    echo "Downloading Axis"
    curl -O -s $PATH_AXIS
fi

# Note: Rampart 1.5 is the last one that doesn't cause a NullPointerException without a configured
# security policy (see https://stackoverflow.com/questions/36424217/wso2-rampartutil-throwing-null-pointer-execption)
VERSION_RAMPART=1.5
ZIP_RAMPART=rampart-dist-$VERSION_RAMPART-bin.zip
PATH_RAMPART=https://archive.apache.org/dist/axis/axis2/java/rampart/$VERSION_RAMPART/$ZIP_RAMPART

if [ ! -f "$DIR/$ZIP_RAMPART" ]; then
    echo "Downloading Rampart"
    curl -O -s $PATH_RAMPART
fi

echo "Extracting packages"
AXIS_DIR=$DIR/axis2-$VERSION_AXIS
rm -rf $AXIS_DIR
unzip -o -qq "$DIR/$ZIP_AXIS"


unzip -o -qq -j "$DIR/$ZIP_RAMPART" '*/lib/*.jar' -d $AXIS_DIR/lib
unzip -o -qq -j "$DIR/$ZIP_RAMPART" '*/modules/*.mar' -d $AXIS_DIR/repository/modules

cp -r $DIR/../AxisInterop/axis_services/* $AXIS_DIR/repository/services


if (( "$FG" == "1" ))
then
    echo "Starting Axis in foregroung"
    $AXIS_DIR/bin/axis2server.sh
    exit 0
else
    $AXIS_DIR/bin/axis2server.sh &
    AXIS2_PID=$!
fi
echo "Started process $AXIS2_PID"

echo "Waiting until Axis is ready on port 8080"
COUNT=1
while [[ -z `curl -s 'http://localhost:8080/axis2/services/' ` ]]
do
    echo -n "."
    sleep 1
    COUNT=$COUNT+1
    if (( "$COUNT" > 10 ))
    then
        echo "Timed out waiting for Axis to start up"
        exit 1
    fi
done

echo "Axis is up (pid $AXIS2_PID)"
