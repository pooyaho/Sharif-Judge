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
# tester.sh /home/mohammad/judge/homeworks/hw6/p1 mjn problem problem c 1 50000 diff -iw
# In this example judge assumes that the file is located at:
# /home/mohammad/judge/homeworks/hw6/p1/mjn/problem.c
# And test cases are located at:
# /home/mohammad/judge/homeworks/hw6/p1/in/  {input1.txt, input2.txt, ...}
# /home/mohammad/judge/homeworks/hw6/p1/out/ {output1.txt, output2.txt, ...}


####################### Return Values #######################
# RETURN VALUE         PRINTED MESSAGE
#      0              score form 10000
#      1              Compilation Error
#      2              Syntax Error
#      3              Bad System Call
#      4              Special Judge Script is Invalid
#      5              File format not supported


######################## Settings #######################
SANDBOX_ON=true # turn EasySandbox for C/C++ on/off
# Run:
#    $ cd easysandbox
#    $ make runtests
# If you see "All tests passed!", EasySandbox can be enabled on your system
# For enabling EasySandbox, run:
#    $ cd easysandbox
#    $ make
# and set "SANDBOX_ON" option (above) to "true"

SHIELD_ON=true # turn Shield for C/C++ on/off
# If you want to turn off java policy, leave this blank:
JAVA_POLICY="-Djava.security.manager -Djava.security.policy=java.policy"
LOG_ON=true


################### Getting Arguments ###################
PROBLEMPATH=$1 # problem directory
UN=$2 # username
MAINFILENAME=$3 # used only for java
FILENAME=$4 # file name without extension
EXT=$5 # file extension
TIMELIMIT=$6
MEMLIMIT=$7
DIFFTOOL=$8
DIFFOPTION=$9
# DIFFOPTION can be "ignore_all_whitespace". In this case, before diff command,
# all newlines and whitespaces will be removed from both files.
if [ "$DIFFOPTION" = "" ]; then
	DIFFOPTION="-bB"
fi
if [ "$DIFFOPTION" != "ignore_all_whitespace" ]; then
	DIFFARGUMENT=$DIFFOPTION
fi


#################### Initialization #####################
TST="$(ls $PROBLEMPATH/in | wc -l)"  # Number of Test Cases
JAIL=jail-$RANDOM
if ! mkdir $JAIL; then
	exit
fi
cd $JAIL
cp ../timeout ./timeout
chmod +x timeout

LOG="$PROBLEMPATH/$UN/log"; echo "" >>$LOG
function judge_log {
	if $LOG_ON; then
		echo -e "$1" >>$LOG 
	fi
}

judge_log "$(date)"
#echo -e "\nJAILPATH="$PROBLEMPATH/$UN/jail"\nEXT="$EXT"\nTIME LIMIT="$TIMELIMIT"\nMEM LIMIT="$MEMLIMIT"\nSECURITY HEADER="$HEADER"\nTEST CASES="$TST"\nDIFF PARAM="$DIFFOPTION"\n" >>$LOG

########################################################################################################
############################################ COMPILING JAVA ############################################
########################################################################################################
if [ "$EXT" = "java" ]; then
	cp $PROBLEMPATH/$UN/$FILENAME.$EXT $MAINFILENAME.$EXT
	judge_log "Compiling as Java"
	javac $MAINFILENAME.$EXT >/dev/null 2>cerr
	EXITCODE=$?
	if [ $EXITCODE -ne 0 ]; then
		judge_log "Compile Error"
		judge_log "$(cat cerr|head -10)"
		echo '<span style="color:blue;">Compile Error</span>' >$PROBLEMPATH/$UN/result.html
		echo '<span style="color:red;">' >> $PROBLEMPATH/$UN/result.html
		#filepath="$(echo "${JAIL}/${FILENAME}.${EXT}" | sed 's/\//\\\//g')" #replacing / with \/
		(cat cerr | head -10 | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		#(cat $JAIL/cerr) >> $PROBLEMPATH/$UN/result.html
		echo "</span>" >> $PROBLEMPATH/$UN/result.html
		cd ..
		rm -r $JAIL >/dev/null 2>/dev/null
		echo "Compilation Error"
		exit 1
	fi
fi

########################################################################################################
########################################## COMPILING PYTHON 3 ##########################################
########################################################################################################
if [ "$EXT" = "py" ]; then
	cp $PROBLEMPATH/$UN/$FILENAME.$EXT $FILENAME.$EXT
	judge_log "Checking Python Syntax"
	python3 -O -m py_compile $FILENAME.$EXT >/dev/null 2>cerr
	EXITCODE=$?
	judge_log "Syntax checked. Exit Code=$EXITCODE"
	if [ $EXITCODE -ne 0 ]; then
		judge_log "Syntax Error"
		judge_log "$(cat cerr | head -10)"
		echo '<span style="color:blue">Syntax Error</span>' >$PROBLEMPATH/$UN/result.html
		echo '<span style="color: red;">' >> $PROBLEMPATH/$UN/result.html
		(cat cerr | head -10 | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		echo "</span>" >> $PROBLEMPATH/$UN/result.html
		cd ..
		rm -r $JAIL >/dev/null 2>/dev/null
		echo "Syntax Error"
		exit 2
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
		judge_log "Using EasySandbox\n"
		cp ../easysandbox/EasySandbox.so EasySandbox.so
		chmod +x EasySandbox.so
	fi
	if $SHIELD_ON; then
		judge_log "Using Shield"
		cp ../shield/shield.$EXT shield.$EXT
		cp ../shield/def$EXT.h def.h
		# adding define to beginning of code
		echo '#define main themainmainfunction' | cat - code.c > thetemp && mv thetemp code.c
		$COMPILER shield.$EXT -lm -O2 -o $FILENAME >/dev/null 2>cerr
	else
		$COMPILER code.$EXT -lm -O2 -o $FILENAME >/dev/null 2>cerr
	fi
	EXITCODE=$?
	judge_log "Compiled. Exit Code=$EXITCODE"
	if [ $EXITCODE -ne 0 ]; then
		judge_log "Compile Error"
		judge_log "$(cat cerr | head -10)"
		echo '<span style="color:blue">Compile Error<br>Error Messages: (line numbers are not correct)</span>' >$PROBLEMPATH/$UN/result.html
		echo '<span style="color: red;">' >> $PROBLEMPATH/$UN/result.html
		SHIELD_ACT=false
		if $SHIELD_ON; then
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
		echo "Compilation Error"
		exit 1
	fi
fi

########################################################################################################
################################################ TESTING ###############################################
########################################################################################################
judge_log "Testing..."

echo "" >$PROBLEMPATH/$UN/result.html

PASSEDTESTS=0

for((i=1;i<=TST;i++)); do
	judge_log "TEST$i"
	sleep 0.05
	echo "<span style='color : blue;'>Test $i </span>" >>$PROBLEMPATH/$UN/result.html
	if [ "$EXT" != "java" ]; then # TODO memory limit for java
		ulimit -v $((MEMLIMIT+10000))
		ulimit -m $((MEMLIMIT+10000))
		ulimit -s $((MEMLIMIT+10000))
	fi
	ulimit -t $((TIMELIMIT+1)) # kar az mohkamkari eyb nemikone!
	
	touch err
	
	if [ "$EXT" = "java" ]; then
		cp ../java.policy java.policy
		./timeout -nosandbox -t $TIMELIMIT java $JAVA_POLICY $MAINFILENAME  <$PROBLEMPATH/in/input$i.txt >out 2>err
		EXITCODE=$?
	elif [ "$EXT" = "c" ] || [ "$EXT" = "cpp" ]; then
		#$TIMEOUT ./$FILENAME <$PROBLEMPATH/in/input$i.txt >out 2>/dev/null
		if $SANDBOX_ON; then
			#LD_PRELOAD=./EasySandbox.so ./$FILENAME <$PROBLEMPATH/in/input$i.txt >out 2>/dev/null
			./timeout --sandbox -t $TIMELIMIT -m $MEMLIMIT ./$FILENAME <$PROBLEMPATH/in/input$i.txt >out 2>err
			EXITCODE=$?
			# remove <<entering SECCOMP mode>> from beginning of output:
			tail -n +2 out >thetemp && mv thetemp out
		else
			#./$FILENAME <$PROBLEMPATH/in/input$i.txt >out 2>/dev/null
			./timeout -nosandbox -t $TIMELIMIT -m $MEMLIMIT ./$FILENAME <$PROBLEMPATH/in/input$i.txt >out 2>err
			EXITCODE=$?
		fi
	elif [ "$EXT" = "py" ]; then
		./timeout -nosandbox -t $TIMELIMIT -m $MEMLIMIT python3 -O $FILENAME.$EXT <$PROBLEMPATH/in/input$i.txt >out 2>err
		EXITCODE=$?
		echo "<span>" >>$PROBLEMPATH/$UN/result.html
		(cat err | head -5 | sed "s/$FILENAME.$EXT//g" | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		echo "</span>" >>$PROBLEMPATH/$UN/result.html
	else
		judge_log "File format not supported."
		cd ..
		rm -r $JAIL >/dev/null 2>/dev/null
		echo "File format not supported"
		exit 5
	fi

	judge_log "Exit Code=$EXITCODE"

	if ! grep -q "FINISHED" err; then
		if grep -q "TIMEOUT CPU" err; then
			judge_log "Time Limit Exceeded (Exit code=$EXITCODE)"
			echo "<span style='color: orange;'>Time Limit Exceeded</span>" >>$PROBLEMPATH/$UN/result.html
			continue
		elif grep -q "MEM CPU" err; then
			judge_log "Memory Limit Exceeded (Exit code=$EXITCODE)"
			echo "<span style='color: orange;'>Memory Limit Exceeded</span>" >>$PROBLEMPATH/$UN/result.html
			continue
		fi
	fi
	
	if [ $EXITCODE -eq 137 ]; then
		#judge_log "Time Limit Exceeded (Exit code=$EXITCODE)"
		#echo "<span style='color: orange;'>Time Limit Exceeded</span>" >>$PROBLEMPATH/$UN/result.html
		judge_log "Killed (Exit code=$EXITCODE)"
		echo "<span style='color: orange;'>Killed</span>" >>$PROBLEMPATH/$UN/result.html
		continue
	fi


	if [ $EXITCODE -ne 0 ]; then
		judge_log "Runtime Error"
		echo "<span style='color: orange;'>Runtime Error</span>" >>$PROBLEMPATH/$UN/result.html
		continue
	fi
	
	# checking correctness of output
	ACCEPTED=false
	if [ -e "$PROBLEMPATH/tester.cpp" ]; then
		cp $PROBLEMPATH/tester.cpp tester.cpp
		g++ tester.cpp -otester
		EC=$?
		if [ $EC -ne 0 ]; then
			echo "Special Judge Script is Invalid"
			cd ..
			rm -r $JAIL >/dev/null 2>/dev/null
			exit 4
		fi
		./tester $PROBLEMPATH/in/input$i.txt out
		EC=$?
		if [ $EC -eq 0 ]; then
			ACCEPTED=true
		fi
	else
		cp $PROBLEMPATH/out/output$i.txt correctout
		if [ "$DIFFOPTION" = "ignore_all_whitespace" ];then #removing all newlines and whitespaces before diff
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
		echo "<span style='color: green;'>ACCEPT</span>" >>$PROBLEMPATH/$UN/result.html
		((PASSEDTESTS=$PASSEDTESTS+1))
	else
		judge_log "WRONG"
		echo "<span style='color: red;'>WRONG</span>" >>$PROBLEMPATH/$UN/result.html
	fi
done

((SCORE=PASSEDTESTS*10000/TST)) # give score from 10,000
echo $SCORE
cd ..
rm -r $JAIL >/dev/null 2>/dev/null # removing files
exit 0
