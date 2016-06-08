#/bin/bash
FOLDER="/home/william/ping"
TARGET="197.231.197.4"
NOW=$(date +"%Y-%m-%d")
LOGFILE="log-$NOW.log"

p=$( ping -c 1 -W 4 $TARGET | awk -F '/' 'END {print $5}' )
echo $p 

if [ $p  ]
then

	date +"%Y/%m/%d %T	$p" >> $FOLDER/$LOGFILE
    result="success"

else

    date +"%Y/%m/%d %T	-" >> $FOLDER/$LOGFILE
    date +"%Y/%m/%d %T Ping Failure to "$TARGET >> $FOLDER/error.log

fi

