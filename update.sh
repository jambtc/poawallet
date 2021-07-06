#!/bin/bash
echo Updating...


git stash
git pull

### Folder rights
chgrp www-data web/assets
chmod g+w web/assets/

chgrp www-data runtime
chmod g+w runtime/

echo Versioning...
git rev-parse HEAD>version.txt

echo Calling eth-storage-service...
./ethstorage.sh

echo Done!
