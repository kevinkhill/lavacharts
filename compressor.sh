#!/bin/bash

echo "=========================================="
echo "== Compressing CSS"
echo "=========================================="
for FILE in `ls -A "./public/css/"`
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
for FILE in `ls -A "./public/js/" | grep -v ".swf"`
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
MASTERCSS="./public/master.min.css"

echo "Creating $MASTERCSS"
if [[ -e $MASTERCSS ]]
then
    rm $MASTERCSS
fi

for FILE in `ls -A "./public/builds/css/"`
do
    cat "./public/builds/css/$FILE" >> $MASTERCSS
done
echo "                                   ...Done"



echo "=========================================="
echo "== Concatenating JS"
echo "=========================================="
MASTERJS="./public/master.min.js"

echo "Creating $MASTERJS"
if [[ -e $MASTERJS ]]
then
    rm $MASTERJS
fi

for FILE in `ls -A "./public/builds/js/"`
do
    cat "./public/builds/js/$FILE" >> "./public/master.min.js"
done
echo "                                   ...Done"



echo "=========================================="
echo "== Complete!"
echo "=========================================="