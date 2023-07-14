#!/bin/bash
find "files" -name "*.zip_" -print0 | while read -d $'\0' file
do
        filename_cut=${file:0:-1}
        mv $file $filename_cut
done