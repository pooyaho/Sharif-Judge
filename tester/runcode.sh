#!/bin/bash

EXT=$1
shift

MEMLIMIT=$1
shift

TIMELIMIT=$1
shift

TIMELIMITINT=$1
shift

CMD=$@

#echo $CMD

if [ "$EXT" != "java" ]; then # TODO memory limit for java
	ulimit -v $((MEMLIMIT+10000))
	ulimit -m $((MEMLIMIT+10000))
	#ulimit -s $((MEMLIMIT+10000))
fi

ulimit -t $TIMELIMITINT # kar az mohkamkari eyb nemikone!

$CMD

exit $?