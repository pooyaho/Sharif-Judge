#!/bin/bash

# This file runs a command with given limits
# usage: ./runcode.sh extension memorylimit timelimit timelimit_int command

EXT=$1
shift

MEMLIMIT=$1
shift

TIMELIMIT=$1
shift

TIMELIMITINT=$1
shift

CMD=$@

if [ "$EXT" != "java" ]; then
	ulimit -v $((MEMLIMIT+10000))
	ulimit -m $((MEMLIMIT+10000))
	#ulimit -s $((MEMLIMIT+10000))
fi

ulimit -t $TIMELIMITINT

$CMD

exit $?
