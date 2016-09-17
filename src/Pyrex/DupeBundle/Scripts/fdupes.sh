#!/bin/bash
#apt-get update
#apt-get install -y fdupes
FILE=fdupe_log.txt
fdupes -r1nA /volume4/Pool/ /volume1/_archives/ /volume1/archives/ > $FILE
sed -i '/@eaDir/d' $FILE
sed -i '/\.jpg/d' $FILE
sed -i '/\.sfv/d' $FILE
sed -i '/\.m3u/d' $FILE