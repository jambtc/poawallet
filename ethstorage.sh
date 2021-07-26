#!/bin/bash
echo Stopping ethstorage service...
systemctl stop ethstorage.service

echo Starting new ethstorage service...
systemctl start ethstorage.service
echo Ready!
systemctl status ethstorage.service
echo
echo
echo To view ethstorage status digit: "systemctl status ethstorage.service"
echo To view journal digit: "journalctl -fu ethstorage"
