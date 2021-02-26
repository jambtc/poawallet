#!/bin/bash
echo Updating...


git stash
git pull

echo Versioning...
git rev-parse HEAD>version.txt

echo Calling webservice...
./wsservice.sh

echo Done!
