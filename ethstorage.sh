#!/bin/bash

echo Stopping ethstorage service...
systemctl stop ethstorage.service

echo Starting new ethstorage service...
systemctl start ethstorage.service
echo Ready!
