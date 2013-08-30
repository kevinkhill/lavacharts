#!/bin/bash

yui()
{
    echo "> Processing $FILE"
    FILENAME=$(basename "$1")
    EXT="${FILENAME##*.}"
    FN="${FILENAME%.*}"

    java -jar yuicompressor.jar "$FN.$EXT" -o "$NM.min.$EXT" --charset utf-8
}

echo "========== Compressing CSS =========="
cd public/css/
for FILE in *.css
do
    yui $FILE
done


echo "========== Compressing JS =========="
cd ../js/
for FILE in *.css
do
    yui $FILE
done

echo "========== Complete! =========="