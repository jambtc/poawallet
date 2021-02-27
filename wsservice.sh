#!/bin/bash
# tmux ecc ecc.

echo Killing existing session of wsservice...
tmux kill-session -t wsservice

echo Starting new wsservice session...
tmux new-session -d -s "wsservice" ./yii server/start 7502
echo Ready!
