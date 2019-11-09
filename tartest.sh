#!/bin/bash

# a simple bash script

function getdir {
	local DIR=""

  # repeat until a directory is entered
	while [[ "$DIR" == "" ]]; do
		read -p "Directory to backup: " DIR
	done

	echo "$DIR"
}

function getfname {
	local FNAME=""
	
  # repeat until ...
	while [[ "$FNAME" == "" ]]; do
		read -p "Backup file name: " FNAME
	done

	echo "$FNAME"
}


DIR=$(getdir)
FNAME=$(getfname)
FNAME="$FNAME"-$(date +%Y%m%d).tgz

tar -cf $FNAME $DIR

if [[ $? == 0 ]]; then
	echo "tar file created"
else
	echo "an error occurred while creating tar file"
fi
