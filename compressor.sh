#!/bin/bash

DIR=`pwd`

echo "=========================================="
echo "== Compressing CSS"
echo "=========================================="
for FILE in `ls -A "$DIR/public/css/"`
do
    FILENAME=$(basename "$FILE")
    FN="${FILENAME%.*}"
    
    echo "Processing $FILE"
        java -jar yuicompressor-2.4.8.jar --charset utf-8 --type css "./public/css/$FN.css" -o "./public/builds/css/$FN.min.css"
    echo "                                   ...Done"
done

echo "=========================================="
echo "== Compressing JS"
echo "=========================================="
for FILE in `ls -A "$DIR/public/js/" | grep -v ".swf"`
do
    FILENAME=$(basename "$FILE")
    FN="${FILENAME%.*}"
    
    echo "Processing $FILE"
        java -jar yuicompressor-2.4.8.jar --charset utf-8 --type js "./public/js/$FN.js" -o "./public/builds/js/$FN.min.js" 
    echo "                                   ...Done"
done





echo "=========================================="
echo "== Concatenating CSS"
echo "=========================================="
echo "Creating ./public/master.min.css"
for FILE in `ls -A "$DIR/public/builds/css/"`
do
    cat "./public/builds/css/$FILE" >> "./public/master.min.css"
done
echo "                                   ...Done"

echo "=========================================="
echo "== Concatenating JS"
echo "=========================================="
echo "Creating ./public/master.min.js"
for FILE in `ls -A "$DIR/public/builds/js/"`
do
    cat "./public/builds/js/$FILE" >> "./public/master.min.js"
done
echo "                                   ...Done"

echo "=========================================="
echo "== Complete!"
echo "=========================================="