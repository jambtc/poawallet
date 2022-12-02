#!/bin/bash
clear
echo Updating...


git stash
git pull

### Folder rights
chgrp www-data web/assets
chmod g+w web/assets/

chgrp www-data runtime
chmod g+w runtime/

echo Version
git rev-parse HEAD

echo To call eth-storage-service please launch ethstorage.sh
#./ethstorage.sh

echo Done!
