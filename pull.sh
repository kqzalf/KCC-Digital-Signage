#!/bin/bash

################
# Uncomment if you want the script to always use the scripts

#put this in var/www/html
#Crontab:  */15 * * * * cd /var/www/html && sudo bash pull.sh >> /var/www/html/output.log

# directory as the folder to look through
REPOSITORIES="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
REPOSITORIES=`pwd`

IFS=$'\n'

for REPO in `ls "$REPOSITORIES/"`
do
  if [ -d "$REPOSITORIES/$REPO" ]
  then
    echo "Updating $REPOSITORIES/$REPO at `date`"
    if [ -d "$REPOSITORIES/$REPO/.git" ]
    then
      cd "$REPOSITORIES/$REPO"
      echo "Cleaning"
      git reset --hard
      git clean -df
      echo "Fetching"
      git fetch
      echo "Pulling"
      git pull
      git status
      else
      echo "Skipping because it doesn't look like it has a .git folder."
    fi
    echo "Done at `date`"
    echo
  fi
done
