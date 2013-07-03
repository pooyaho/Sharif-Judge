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
# tester.sh /home/mohammad/judge/homeworks/hw6/p1 mjn problem c 1 50000 7 diff -iw
# In this example judge assumes that the file is located at:
# /home/mohammad/judge/homeworks/hw6/p1/mjn/problem.c
# And test cases are located at:
# /home/mohammad/judge/homeworks/hw6/p1/in/  {test1.in, test2.in, ...}
# /home/mohammad/judge/homeworks/hw6/p1/out/ {test1.out, test2.out, ...}

####################### Return Values #######################
# RETURN VALUE         PRINTED MESSAGE
#      0              score form 10000
#      1              Compilation Error
#      2              Syntax Error
#      3              Bad System Call
#      4              Special Judge Script is Invalid
#      5              File format not supported

######################## Settings #######################
# If you want to use "diet libc" instead of glibc, set path to diet executable file here.
# "diet libc" uses less system calls and therefore, it brings more security.
# But in my case, it caused compilation problems. So here is an option for turning it off.
DIET="" # Don't use diet libc
#DIET="dietlibc/bin-i386/diet" # Path to diet executable file
SECCOMP_ON=false # turn seccomp filter for c/c++ on/off
SHIELD_ON=false # turn Shield for C/C++ on/off
# If you want to turn off java policy, leave this blank:
JAVA_POLICY="-Djava.security.manager -Djava.security.policy=java.policy"
LOG_ON=true

################### Getting Arguments ###################
PROBLEMPATH=$1 # problem directory
UN=$2 # username
FILENAME=$3 # file name without extension
EXT=$4 # file extension
TIMELIMIT=$5
MEMLIMIT=$6
HEADER=$7
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
# Using 'timeout' command, Sharif Judge can detect
# "Time limit exceeded" error. Without it, submitted program
# still will be killed after TIMELIMIT (with ulimit), but
# it will be reported as "Runtime Error".
# So "timeout" is not necessary.
TIMEOUT="timeout -s9 $TIMELIMIT"
hash timeout 2>/dev/null || TIMEOUT=""

TST="$(ls $PROBLEMPATH/in | wc -l)"  # Number of Test Cases
JAIL=jail-$RANDOM
mkdir $JAIL
cd $JAIL

LOG="$PROBLEMPATH/$UN/log"; echo "" >$LOG
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
	cp $PROBLEMPATH/$UN/$FILENAME.$EXT $FILENAME.$EXT
	judge_log "Compiling as Java"
	javac $FILENAME.$EXT >/dev/null 2>cerr
	EXITCODE=$?
	if [ $EXITCODE -ne 0 ]; then
		judge_log "Compile Error"
		judge_log "$(cat cerr|head -10)"
		echo '<pre style="color:blue;">Compile Error</pre>' >$PROBLEMPATH/$UN/result.html
		echo '<pre style="color:red;">' >> $PROBLEMPATH/$UN/result.html
		#filepath="$(echo "${JAIL}/${FILENAME}.${EXT}" | sed 's/\//\\\//g')" #replacing / with \/
		(cat cerr | head -10 | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		#(cat $JAIL/cerr) >> $PROBLEMPATH/$UN/result.html
		echo "</pre>" >> $PROBLEMPATH/$UN/result.html
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
		echo '<pre style="color:blue">Syntax Error</pre>' >$PROBLEMPATH/$UN/result.html
		echo '<pre style="color: red;">' >> $PROBLEMPATH/$UN/result.html
		(cat cerr | head -10 | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		echo "</pre>" >> $PROBLEMPATH/$UN/result.html
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
	if $SECCOMP_ON; then
		judge_log "Using Seccomp\n"
		cp {../seccomp/shield.$EXT,../seccomp/config.h,../seccomp/seccomp-bpf.h,../seccomp/missing_syscalls.h,../seccomp/def.h} .
		if $SHIELD_ON; then #overwrite def.h
			judge_log "Using Shield\n"
			cp ../shield/def$EXT.h def.h
		fi
		cp $PROBLEMPATH/$UN/$FILENAME.$EXT code.c
		# adding define to beginning of code
		echo '#define main themainmainfunction' | cat - code.c > thetemp && mv thetemp code.c
		$DIET $COMPILER shield.$EXT -lm -O2 -o $FILENAME >/dev/null 2>cerr
	elif $SHIELD_ON; then
		judge_log "Using Shield"
		cp ../shield/shield.$EXT shield.$EXT
		cp ../shield/def$EXT.h def.h
		cp $PROBLEMPATH/$UN/$FILENAME.$EXT code.c
		# adding define to beginning of code
		echo '#define main themainmainfunction' | cat - code.c > thetemp && mv thetemp code.c
		$DIET $COMPILER shield.$EXT -lm -O2 -o $FILENAME >/dev/null 2>cerr
	else
		$DIET $COMPILER code.$EXT -lm -O2 -o $FILENAME >/dev/null 2>cerr
	fi
	EXITCODE=$?
	judge_log "Compiled. Exit Code=$EXITCODE"
	if [ $EXITCODE -ne 0 ]; then
		judge_log "Compile Error"
		judge_log "$(cat cerr | head -10)"
		echo '<pre style="color:blue">Compile Error<br><br>Error Messages: (line numbers are not correct)</pre>' >$PROBLEMPATH/$UN/result.html
		echo '<pre style="color: red;">' >> $PROBLEMPATH/$UN/result.html
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
		echo "</pre>" >> $PROBLEMPATH/$UN/result.html
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
	echo "<pre style='color : blue;'>Test $i </pre>" >>$PROBLEMPATH/$UN/result.html
	if [ "$EXT" != "java" ]; then
		ulimit -v $MEMLIMIT
		ulimit -m $MEMLIMIT
	fi
	ulimit -t $TIMELIMIT # kar az mohkamkari eyb nemikone!
	if [ "$EXT" = "java" ]; then
		cp ../java.policy java.policy
		$TIMEOUT java $JAVA_POLICY $FILENAME  <$PROBLEMPATH/in/test$i.in >out 2>/dev/null
		#echo "java -cp $PROBLEMPATH/$UN/jail $FILENAME <$PROBLEMPATH/in/test$i.in >$PROBLEMPATH/$UN/jail/$UN.out 2>$PROBLEMPATH/$UN/tmp" >>$LOG
		#java -cp $PROBLEMPATH/$UN/jail $FILENAME <$PROBLEMPATH/in/test$i.in >$PROBLEMPATH/$UN/jail/$UN.out 2>$PROBLEMPATH/$UN/tmp
		EXITCODE=$?
	elif [ "$EXT" = "c" ]; then
		$TIMEOUT ./$FILENAME <$PROBLEMPATH/in/test$i.in >out 2>/dev/null
		EXITCODE=$?
	elif [ "$EXT" = "cpp" ]; then
		$TIMEOUT ./$FILENAME <$PROBLEMPATH/in/test$i.in >out 2>/dev/null
		EXITCODE=$?
	elif [ "$EXT" = "py" ]; then
		$TIMEOUT python3 -O $FILENAME.$EXT <$PROBLEMPATH/in/test$i.in >out 2>tmp
		EXITCODE=$?
		echo "<pre>" >>$PROBLEMPATH/$UN/result.html
		(cat tmp | head -5 | sed "s/$FILENAME.$EXT//g" | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		echo "</pre>" >>$PROBLEMPATH/$UN/result.html
		rm tmp
	else
		judge_log "File format not supported."
		cd ..
		rm -r $JAIL >/dev/null 2>/dev/null
		echo "File format not supported"
		exit 5
	fi

	judge_log "Exit Code=$EXITCODE"

	if [ $EXITCODE -eq 137 ]; then
		judge_log "Time Limit Exceeded (Exit code=$EXITCODE)"
		echo "<pre style='color: orange;'>Time Limit Exceeded</pre>" >>$PROBLEMPATH/$UN/result.html
		continue
	fi
	
	if [ $EXITCODE -eq 159 ]; then
		judge_log "Bad System Call (Exit code=$EXITCODE)"
		echo "<pre style='color: red;'>Potentially Harmful Code. Process terminated.</pre>" >>$PROBLEMPATH/$UN/result.html
		echo "Bad System Call"
		cd ..
		rm -r $JAIL >/dev/null 2>/dev/null
		exit 3
	fi

	if [ $EXITCODE -ne 0 ]; then
		judge_log "Runtime Error"
		echo "<pre style='color: orange;'>Runtime Error</pre>" >>$PROBLEMPATH/$UN/result.html
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
		./tester $PROBLEMPATH/in/test$i.in out
		EC=$?
		if [ $EC -eq 0 ]; then
			ACCEPTED=true
		fi
	else
		cp $PROBLEMPATH/out/test$i.out correctout
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
		echo "<pre style='color: green;'>ACCEPT</pre>" >>$PROBLEMPATH/$UN/result.html
		((PASSEDTESTS=$PASSEDTESTS+1))
	else
		judge_log "WRONG"
		echo "<pre style='color: red;'>WRONG</pre>" >>$PROBLEMPATH/$UN/result.html
	fi
done

((SCORE=PASSEDTESTS*10000/TST)) # give score from 10,000
echo $SCORE
cd ..
rm -r $JAIL >/dev/null 2>/dev/null # removing files
exit 0
