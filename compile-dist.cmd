@ECHO OFF

set RES_FILE="D:\Soft\Editor\JetBrains\PhpStorm-2017.1\lib\resources_en.jar"

REM cp /y %RES_FILE% resources_en.jar
REM unzip -o resources_en.jar -d resources_en

php make.php
REM cp messages/* resources_en/messages

REM pushd resources_en
REM zip -0 -r ../resources_en.jar *
REM popd

REM cp resources_en.jar %RES_FILE%


