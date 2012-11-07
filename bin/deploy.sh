#!/bin/bash

cd /var/www/ppCommunity

GIT_TAG=$1

if [ -z "$ppInterface_env" ]; then 
  echo "Environment variable ppInterface_env not set. Exiting"
  exit 1
fi

echo "Using environment $ppInterface_env"

if [ $ppInterface_env == "prod" ]; then
  if [ -z "$GIT_TAG" ]; then 
    echo "No tag to be used found. Exiting"
    exit 1
  fi

  git reset --hard HEAD
  git fetch
  git checkout -f -B $GIT_TAG tags/$GIT_TAG

  chown -R admin:www-data app bin build doc src vendor web
  chmod -R 755 app bin build doc src vendor web
  chmod -R 775 app/cache app/logs
elif [ $ppInterface_env == "staging" ]; then
  if [ -z "$GIT_TAG" ]; then 
    GIT_TAG=master
  fi
  
  git reset --hard HEAD
  git fetch
  git checkout -f -B $GIT_TAG origin/$GIT_TAG

  python ./bin/build.py --env=staging --cleanup

  chown -R admin:www-data *
  chmod -R 755 *
  chmod -R 775 app/cache app/logs data
  chown -R ftp:ftp data/ftp
  chmod -R 777 data/ftp
fi

exit 0
