#!/bin/bash

#    In the name of ALLAH
#    Sharif Judge
#    Copyright (C) 2013  Mohammad Javad Naderi <mjnaderi@gmail.com>
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.


##################### Example Usage #####################
# tester.sh /home/mohammad/judge/homeworks/hw6/p1 mjn problem problem c 1 1 50000 diff -iw 1 1 1 1
# In this example judge assumes that the file is located at:
# /home/mohammad/judge/homeworks/hw6/p1/mjn/problem.c
# And test cases are located at:
# /home/mohammad/judge/homeworks/hw6/p1/in/  {input1.txt, input2.txt, ...}
# /home/mohammad/judge/homeworks/hw6/p1/out/ {output1.txt, output2.txt, ...}


####################### Output #######################
#    Output              Meaning
#      >=0             score form 10000
#      -1              Compilation Error
#      -2              Syntax Error
#      -3              Bad System Call
#      -4              Special Judge Script is Invalid
#      -5              File format not supported
#      -6              Judge Error

################### Getting Arguments ###################
PROBLEMPATH=${1} # problem directory
UN=${2} # username
MAINFILENAME=${3} # used only for java
FILENAME=${4} # file name without extension
EXT=${5} # file extension
TIMELIMIT=${6}
TIMELIMITINT=${7}
MEMLIMIT=${8}
OUTLIMIT=${9}
DIFFTOOL=${10}
DIFFOPTION=${11}
if [ ${12} = "1" ]; then
	LOG_ON=true
else
	LOG_ON=false
fi
if [ ${13} = "1" ]; then
	SANDBOX_ON=true
else
	SANDBOX_ON=false
fi
if [ ${14} = "1" ]; then
	C_SHIELD_ON=true
else
	C_SHIELD_ON=false
fi
if [ ${15} = "1" ]; then
	JAVA_POLICY="-Djava.security.manager -Djava.security.policy=java.policy"
else
	JAVA_POLICY=""
fi
# DIFFOPTION can also be "ignore" of "exact".
# ignore: In this case, before diff command, all newlines and whitespaces will be removed from both files
# identical: diff will compare files without ignoring anything. files must be identical to be accepted
DIFFARGUMENT=""
if [[ "$DIFFOPTION" != "identical" && "$DIFFOPTION" != "ignore" ]]; then
	DIFFARGUMENT=$DIFFOPTION
fi


LOG="$PROBLEMPATH/$UN/log"; echo "" >>$LOG
function judge_log {
	#echo -e "$1"
	if $LOG_ON; then
		echo -e "$@" >>$LOG 
	fi
}

judge_log "Starting tester..."


#################### Initialization #####################
# detecting existence of perl
PERL_EXISTS=true
hash perl 2>/dev/null || PERL_EXISTS=false

TST="$(ls $PROBLEMPATH/in | wc -l)"  # Number of Test Cases
JAIL=jail-$RANDOM
if ! mkdir $JAIL; then
	judge_log "Error. Folder 'tester' is not writable! Exiting..."
	echo -6
	exit 0
fi
cd $JAIL
cp ../timeout ./timeout
chmod +x timeout

cp ../runcode.sh ./runcode.sh
chmod +x runcode.sh

judge_log "$(date)"
judge_log "Time Limit: $TIMELIMIT s"
judge_log "Memory Limit: $MEMLIMIT kB"
judge_log "Output size limit: $OUTLIMIT bytes"
judge_log "SANDBOX_ON: $SANDBOX_ON"
judge_log "C_SHIELD_ON: $C_SHIELD_ON"
judge_log "JAVA_POLICY: \"$JAVA_POLICY\""



########################################################################################################
############################################ COMPILING JAVA ############################################
########################################################################################################
if [ "$EXT" = "java" ]; then
	cp $PROBLEMPATH/$UN/$FILENAME.java $MAINFILENAME.java
	judge_log "Compiling as Java"
	javac $MAINFILENAME.java >/dev/null 2>cerr
	EXITCODE=$?
	if [ $EXITCODE -ne 0 ]; then
		judge_log "Compile Error"
		judge_log "$(cat cerr|head -10)"
		echo '<span class="shj_b">Compile Error</span>' >$PROBLEMPATH/$UN/result.html
		echo '<span class="shj_r">' >> $PROBLEMPATH/$UN/result.html
		#filepath="$(echo "${JAIL}/${FILENAME}.${EXT}" | sed 's/\//\\\//g')" #replacing / with \/
		(cat cerr | head -10 | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		#(cat $JAIL/cerr) >> $PROBLEMPATH/$UN/result.html
		echo "</span>" >> $PROBLEMPATH/$UN/result.html
		cd ..
		rm -r $JAIL >/dev/null 2>/dev/null
		echo -1
		exit 0
	fi
fi



########################################################################################################
########################################## COMPILING PYTHON 2 ##########################################
########################################################################################################
if [ "$EXT" = "py2" ]; then
	cp $PROBLEMPATH/$UN/$FILENAME.py $FILENAME.py
	judge_log "Checking Python Syntax"
	python -O -m py_compile $FILENAME.py >/dev/null 2>cerr
	EXITCODE=$?
	judge_log "Syntax checked. Exit Code=$EXITCODE"
	if [ $EXITCODE -ne 0 ]; then
		judge_log "Syntax Error"
		judge_log "$(cat cerr | head -10)"
		echo '<span class="shj_b">Syntax Error</span>' >$PROBLEMPATH/$UN/result.html
		echo '<span class="shj_r">' >> $PROBLEMPATH/$UN/result.html
		(cat cerr | head -10 | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		echo "</span>" >> $PROBLEMPATH/$UN/result.html
		cd ..
		rm -r $JAIL >/dev/null 2>/dev/null
		echo -2
		exit 0
	fi
fi



########################################################################################################
########################################## COMPILING PYTHON 3 ##########################################
########################################################################################################
if [ "$EXT" = "py3" ]; then
	cp $PROBLEMPATH/$UN/$FILENAME.py $FILENAME.py
	judge_log "Checking Python Syntax"
	python3 -O -m py_compile $FILENAME.py >/dev/null 2>cerr
	EXITCODE=$?
	judge_log "Syntax checked. Exit Code=$EXITCODE"
	if [ $EXITCODE -ne 0 ]; then
		judge_log "Syntax Error"
		judge_log "$(cat cerr | head -10)"
		echo '<span class="shj_b">Syntax Error</span>' >$PROBLEMPATH/$UN/result.html
		echo '<span class="shj_r">' >> $PROBLEMPATH/$UN/result.html
		(cat cerr | head -10 | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		echo "</span>" >> $PROBLEMPATH/$UN/result.html
		cd ..
		rm -r $JAIL >/dev/null 2>/dev/null
		echo -2
		exit 0
	fi
fi



########################################################################################################
############################################ COMPILING C/C++ ###########################################
########################################################################################################
if [ "$EXT" = "c" ] || [ "$EXT" = "cpp" ]; then
	COMPILER="gcc"
	if [ "$EXT" = "cpp" ]; then
		COMPILER="g++"
	fi
	cp $PROBLEMPATH/$UN/$FILENAME.$EXT code.c
	judge_log "Compiling as $EXT"
	if $SANDBOX_ON; then
		judge_log "Enabling EasySandbox"
		if cp ../easysandbox/EasySandbox.so EasySandbox.so; then
			chmod +x EasySandbox.so
		else
			judge_log 'EasySandbox is not built. Disabling EasySandbox...'
			SANDBOX_ON=false
		fi
	fi
	if $C_SHIELD_ON; then
		judge_log "Enabling Shield"
		cp ../shield/shield.$EXT shield.$EXT
		cp ../shield/def$EXT.h def.h
		# adding define to beginning of code
		echo '#define main themainmainfunction' | cat - code.c > thetemp && mv thetemp code.c
		$COMPILER shield.$EXT -lm -O2 -o $FILENAME >/dev/null 2>cerr
	else
		mv code.c code.$EXT
		$COMPILER code.$EXT -lm -O2 -o $FILENAME >/dev/null 2>cerr
	fi
	EXITCODE=$?
	judge_log "Compiled. Exit Code=$EXITCODE"
	if [ $EXITCODE -ne 0 ]; then
		judge_log "Compile Error"
		judge_log "$(cat cerr | head -10)"
		echo '<span class="shj_b">Compile Error<br>Error Messages: (line numbers are not correct)</span>' >$PROBLEMPATH/$UN/result.html
		echo '<span class="shj_r">' >> $PROBLEMPATH/$UN/result.html
		SHIELD_ACT=false
		if $C_SHIELD_ON; then
			while read line; do
				if [ "`echo $line|cut -d" " -f1`" = "#define" ]; then
					if grep -wq $(echo $line|cut -d" " -f3) cerr; then
						echo `echo $line|cut -d"/" -f3` >> $PROBLEMPATH/$UN/result.html
						SHIELD_ACT=true
						break
					fi
				fi
			done <def.h
		fi
		if ! $SHIELD_ACT; then
			echo -e "\n" >> cerr
			echo "" > cerr2
			while read line; do
				if [ "`echo $line|cut -d: -f1`" = "code.c" ]; then
					echo ${line#code.c:} >>cerr2
				fi
			done <cerr
			(cat cerr2 | head -10 | sed 's/themainmainfunction/main/g' ) > cerr;
			(cat cerr | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		fi
		echo "</span>" >> $PROBLEMPATH/$UN/result.html
		cd ..
		rm -r $JAIL >/dev/null 2>/dev/null
		echo -1
		exit 0
	fi
fi



########################################################################################################
################################################ TESTING ###############################################
########################################################################################################
judge_log "\nTesting..."
judge_log "using ulimit -t $TIMELIMITINT"

echo "" >$PROBLEMPATH/$UN/result.html

PASSEDTESTS=0

for((i=1;i<=TST;i++)); do
	judge_log "\n=== TEST $i ==="
	echo "<span class=\"shj_b\">Test $i </span>" >>$PROBLEMPATH/$UN/result.html
	
	touch err
	
	if [ "$EXT" = "java" ]; then
		cp ../java.policy java.policy
		if $PERL_EXISTS; then
			./runcode.sh $EXT $MEMLIMIT $TIMELIMIT $TIMELIMITINT ./timeout --just-kill -nosandbox -l $OUTLIMIT -t $TIMELIMIT java -mx"$MEMLIMIT"k $JAVA_POLICY $MAINFILENAME  <$PROBLEMPATH/in/input$i.txt >out 2>err
		else
			./runcode.sh $EXT $MEMLIMIT $TIMELIMIT $TIMELIMITINT java -mx"$MEMLIMIT"k $JAVA_POLICY $MAINFILENAME  <$PROBLEMPATH/in/input$i.txt >out 2>err
		fi
		EXITCODE=$?
		if grep -iq "Too small initial heap" out; then
			judge_log "Memory Limit Exceeded"
			echo "<span class=\"shj_o\">Memory Limit Exceeded</span>" >>$PROBLEMPATH/$UN/result.html
			continue
		fi
	elif [ "$EXT" = "c" ] || [ "$EXT" = "cpp" ]; then
		#$TIMEOUT ./$FILENAME <$PROBLEMPATH/in/input$i.txt >out 2>/dev/null
		if $SANDBOX_ON; then
			#LD_PRELOAD=./EasySandbox.so ./$FILENAME <$PROBLEMPATH/in/input$i.txt >out 2>/dev/null
			if $PERL_EXISTS; then
				./runcode.sh $EXT $MEMLIMIT $TIMELIMIT $TIMELIMITINT ./timeout --just-kill --sandbox -l $OUTLIMIT -t $TIMELIMIT -m $MEMLIMIT ./$FILENAME <$PROBLEMPATH/in/input$i.txt >out 2>err
			else
				./runcode.sh $EXT $MEMLIMIT $TIMELIMIT $TIMELIMITINT LD_PRELOAD=./EasySandbox.so ./$FILENAME <$PROBLEMPATH/in/input$i.txt >out 2>err
			fi
			EXITCODE=$?
			# remove <<entering SECCOMP mode>> from beginning of output:
			tail -n +2 out >thetemp && mv thetemp out
		else
			#./$FILENAME <$PROBLEMPATH/in/input$i.txt >out 2>/dev/null
			if $PERL_EXISTS; then
				./runcode.sh $EXT $MEMLIMIT $TIMELIMIT $TIMELIMITINT ./timeout --just-kill -nosandbox -l $OUTLIMIT -t $TIMELIMIT -m $MEMLIMIT ./$FILENAME <$PROBLEMPATH/in/input$i.txt >out 2>err
			else
				./runcode.sh $EXT $MEMLIMIT $TIMELIMIT $TIMELIMITINT ./$FILENAME <$PROBLEMPATH/in/input$i.txt >out 2>err
			fi
			EXITCODE=$?
		fi

	elif [ "$EXT" = "py2" ]; then
		if $PERL_EXISTS; then
			./runcode.sh $EXT $MEMLIMIT $TIMELIMIT $TIMELIMITINT ./timeout --just-kill -nosandbox -l $OUTLIMIT -t $TIMELIMIT -m $MEMLIMIT python -O $FILENAME.py <$PROBLEMPATH/in/input$i.txt >out 2>err
		else
			./runcode.sh $EXT $MEMLIMIT $TIMELIMIT $TIMELIMITINT python -O $FILENAME.py <$PROBLEMPATH/in/input$i.txt >out 2>err
		fi
		EXITCODE=$?

	elif [ "$EXT" = "py3" ]; then
		if $PERL_EXISTS; then
			./runcode.sh $EXT $MEMLIMIT $TIMELIMIT $TIMELIMITINT ./timeout --just-kill -nosandbox -l $OUTLIMIT -t $TIMELIMIT -m $MEMLIMIT python3 -O $FILENAME.py <$PROBLEMPATH/in/input$i.txt >out 2>err
		else
			./runcode.sh $EXT $MEMLIMIT $TIMELIMIT $TIMELIMITINT python3 -O $FILENAME.py <$PROBLEMPATH/in/input$i.txt >out 2>err
		fi
		EXITCODE=$?

	else
		judge_log "File format not supported."
		cd ..
		rm -r $JAIL >/dev/null 2>/dev/null
		echo -5
		exit 0
	fi

	judge_log "Exit Code = $EXITCODE"

	if ! grep -q "FINISHED" err; then
		if grep -q "SHJ_TIME" err; then
			t=`grep "SHJ_TIME" err|cut -d" " -f3`
			judge_log "Time Limit Exceeded ($t s)"
			echo "<span class=\"shj_o\">Time Limit Exceeded ($t s)</span>" >>$PROBLEMPATH/$UN/result.html
			continue
		elif grep -q "SHJ_MEM" err; then
			judge_log "Memory Limit Exceeded"
			echo "<span class=\"shj_o\">Memory Limit Exceeded</span>" >>$PROBLEMPATH/$UN/result.html
			continue
		elif grep -q "SHJ_HANGUP" err; then
			judge_log "Hang Up"
			echo "<span class=\"shj_o\">Process hanged up</span>" >>$PROBLEMPATH/$UN/result.html
			continue
		elif grep -q "SHJ_SIGNAL" err; then
			judge_log "Killed by a signal"
			echo "<span class=\"shj_o\">Killed by a signal</span>" >>$PROBLEMPATH/$UN/result.html
			continue
		elif grep -q "SHJ_OUTSIZE" err; then
			judge_log "Output size limit exceeded"
			echo "<span class=\"shj_o\">Output size limit exceeded</span>" >>$PROBLEMPATH/$UN/result.html
			continue
		fi
	else
		t=`grep "FINISHED" err|cut -d" " -f3`
		judge_log "Time: $t s"
	fi
	
	if [ $EXITCODE -eq 137 ]; then
		#judge_log "Time Limit Exceeded (Exit code=$EXITCODE)"
		#echo "<span style='color: orange;'>Time Limit Exceeded</span>" >>$PROBLEMPATH/$UN/result.html
		judge_log "Killed"
		echo "<span class=\"shj_o\">Killed</span>" >>$PROBLEMPATH/$UN/result.html
		continue
	fi


	if [ $EXITCODE -ne 0 ]; then
		judge_log "Runtime Error"
		echo "<span class=\"shj_o\">Runtime Error</span>" >>$PROBLEMPATH/$UN/result.html
		continue
	fi
	
	# checking correctness of output
	ACCEPTED=false
	if [ -e "$PROBLEMPATH/tester.cpp" ]; then
		cp $PROBLEMPATH/tester.cpp tester.cpp
		g++ tester.cpp -otester
		EC=$?
		if [ $EC -ne 0 ]; then
			echo -4
			cd ..
			rm -r $JAIL >/dev/null 2>/dev/null
			exit 0
		fi
		./tester $PROBLEMPATH/in/input$i.txt $PROBLEMPATH/out/output$i.txt out
		EC=$?
		if [ $EC -eq 0 ]; then
			ACCEPTED=true
		fi
	else
		cp $PROBLEMPATH/out/output$i.txt correctout
		if [ "$DIFFOPTION" = "ignore" ];then #removing all newlines and whitespaces before diff
			tr -d ' \t\n\r\f' <out >tmp1 && mv tmp1 out;
			tr -d ' \t\n\r\f' <correctout >tmp1 && mv tmp1 correctout;
		fi
		if $DIFFTOOL out correctout $DIFFARGUMENT >/dev/null 2>/dev/null
		then
			ACCEPTED=true
		fi
	fi

	if $ACCEPTED; then
		judge_log "ACCEPTED"
		echo "<span class=\"shj_g\">ACCEPT</span>" >>$PROBLEMPATH/$UN/result.html
		((PASSEDTESTS=$PASSEDTESTS+1))
	else
		judge_log "WRONG"
		echo "<span class=\"shj_r\">WRONG</span>" >>$PROBLEMPATH/$UN/result.html
	fi
done

cd ..
rm -r $JAIL >/dev/null 2>/dev/null # removing files
((SCORE=PASSEDTESTS*10000/TST)) # give score from 10,000
judge_log "\nScore from 10000: $SCORE"
echo $SCORE
exit 0
