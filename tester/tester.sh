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
# tester.sh /home/mohammad/judge/homeworks/hw6/p1 mjnaderi problem c 1 50000 7 diff -iw 

####################### Return Values #######################
# RETURN VALUE        PRINTED MESSAGE
#      0              score form 10000
#      1              Compilation Error
#      2              Syntax Error
#      3              Bad System Call
#      4              Special Judge Script is Invalid
#      5              File format not supported

######################## Settings #######################
# if you want to use dietlibc instead of glibc, set path to diet executable file here
# dietlibc uses less system calls and therefore, it brings more security
DIET=""
#DIET="dietlibc/bin-i386/diet"
SECCOMP_ON=false # turn seccomp sandboxing for c/c++ on or off
SHIELD_ON=true # turn shield for C/C++ on or off
JAVA_POLICY="-Djava.security.manager -Djava.security.policy=java.policy" # if you want to turn off java policy, leave this blank

#################### Initialization #####################
TIMEOUT_EXISTS=true
hash timeout 2>/dev/null || TIMEOUT_EXISTS=false

################### Getting Arguments ###################
PROBLEMPATH=$1 # problem dir
UN=$2 # username
FILENAME=$3 # file name without extension
EXT=$4 # file extension
TIMELIMIT=$5
MEMLIMIT=$6
HEADER=$7
DIFFTOOL=$8
DIFFPARAM=$9
if [ "$DIFFPARAM" = "" ]; then
	DIFFPARAM="-bB"
fi

#########################################
TST="$(ls $PROBLEMPATH/in | wc -l)"  # Number of Test Cases
LOG="$PROBLEMPATH/$UN/log"
JAIL=jail-$RANDOM
mkdir $JAIL
cd $JAIL

#TZ='Asia/Tehran' date >$LOG
date >$LOG
#echo -e "\nJAILPATH="$PROBLEMPATH/$UN/jail"\nEXT="$EXT"\nTIME LIMIT="$TIMELIMIT"\nMEM LIMIT="$MEMLIMIT"\nSECURITY HEADER="$HEADER"\nTEST CASES="$TST"\nDIFF PARAM="$DIFFPARAM"\n" >>$LOG

########################################################################################################
############################################ COMPILING JAVA ############################################
########################################################################################################
if [ "$EXT" = "java" ]; then
	cp $PROBLEMPATH/$UN/$FILENAME.$EXT $FILENAME.$EXT
	echo -e "Compiling as Java\n" >>$LOG
	javac $FILENAME.$EXT >/dev/null 2>cerr
	EXITCODE=$?
	if [ $EXITCODE -ne 0 ]; then
		echo -e "Compile Error\n" >>$LOG
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
	echo -e "Checking Python Syntax\n" >>$LOG
	python3 -O -m py_compile $FILENAME.$EXT >/dev/null 2>cerr
	EXITCODE=$?
	echo -e "Syntax checked. Exit Code="$EXITCODE"\n" >>$LOG
	if [ $EXITCODE -ne 0 ]; then
		echo -e "Syntax Error\n" >>$LOG
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
	echo -e "Compiling as $EXT\n" >>$LOG
	if $SECCOMP_ON; then
		echo -e "Using Seccomp\n" >>$LOG
		cp {../seccomp/shield.$EXT,../seccomp/config.h,../seccomp/seccomp-bpf.h,../seccomp/missing_syscalls.h,../seccomp/def.h} .
		if $SHIELD_ON; then #overwrite def.h
			echo -e "Using Shield\n" >>$LOG
			cp ../shield/def$EXT.h def.h
		fi
		cp $PROBLEMPATH/$UN/$FILENAME.$EXT code.c
		# adding define to beginning of code
		echo '#define main themainmainfunction' | cat - code.c > thetemp && mv thetemp code.c
		$DIET $COMPILER shield.$EXT -lm -O2 -o $FILENAME >/dev/null 2>cerr
	elif $SHIELD_ON; then
		echo -e "Using Shield\n" >>$LOG
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
	echo -e "Compiled. Exit Code="$EXITCODE"\n" >>$LOG
	if [ $EXITCODE -ne 0 ]; then
		echo -e "Compile Error\n" >>$LOG
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
echo -e "\nTesting..." >>$LOG

echo "" >$PROBLEMPATH/$UN/result.html

PASSEDTESTS=0

for((i=1;i<=TST;i++)); do
	echo -e "\nTEST"$i >>$LOG
	sleep 0.05
	echo "<pre style='color : blue;'>Test $i </pre>" >>$PROBLEMPATH/$UN/result.html
	if [ "$EXT" != "java" ]; then
		ulimit -v $MEMLIMIT
		ulimit -m $MEMLIMIT
	fi
	ulimit -t $TIMELIMIT # kar az mohkamkari eyb nemikone!
	if [ "$EXT" = "java" ]; then
		echo -e "Running as java" >>$LOG
		cp ../java.policy java.policy
		if $TIMEOUT_EXISTS; then
			timeout -s9 $TIMELIMIT java $JAVA_POLICY $FILENAME  <$PROBLEMPATH/in/test$i.in >out 2>/dev/null
		else
			java $JAVA_POLICY $FILENAME  <$PROBLEMPATH/in/test$i.in >out 2>/dev/null
		fi
		#echo "java -cp $PROBLEMPATH/$UN/jail $FILENAME <$PROBLEMPATH/in/test$i.in >$PROBLEMPATH/$UN/jail/$UN.out 2>$PROBLEMPATH/$UN/tmp" >>$LOG
		#java -cp $PROBLEMPATH/$UN/jail $FILENAME <$PROBLEMPATH/in/test$i.in >$PROBLEMPATH/$UN/jail/$UN.out 2>$PROBLEMPATH/$UN/tmp
		EXITCODE=$?
	elif [ "$EXT" = "c" ]; then
		echo -e "Running as C" >>$LOG
		if $TIMEOUT_EXISTS; then
			timeout -s9 $TIMELIMIT ./$FILENAME <$PROBLEMPATH/in/test$i.in >out 2>/dev/null
		else
			./$FILENAME <$PROBLEMPATH/in/test$i.in >out 2>/dev/null
		fi
		EXITCODE=$?
	elif [ "$EXT" = "cpp" ]; then
		echo -e "Running as C++" >>$LOG
		if $TIMEOUT_EXISTS; then
			timeout -s9 $TIMELIMIT ./$FILENAME <$PROBLEMPATH/in/test$i.in >out 2>/dev/null
		else
			./$FILENAME <$PROBLEMPATH/in/test$i.in >out 2>/dev/null
		fi
		EXITCODE=$?
	elif [ "$EXT" = "py" ]; then
		echo -e "Running as python" >>$LOG
		if $TIMEOUT_EXISTS; then
			timeout -s9 $TIMELIMIT python3 -O $FILENAME.$EXT <$PROBLEMPATH/in/test$i.in >out 2>tmp
		else
			python3 -O $FILENAME.$EXT <$PROBLEMPATH/in/test$i.in >out 2>tmp
		fi
		EXITCODE=$?
		echo "<pre>" >>$PROBLEMPATH/$UN/result.html
		(cat tmp | head -5 | sed "s/$FILENAME.$EXT//g" | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		echo "</pre>" >>$PROBLEMPATH/$UN/result.html
		rm tmp
	else
		echo -e "File format not supported." >>$LOG
		cd ..
		rm -r $JAIL >/dev/null 2>/dev/null
		echo "File format not supported"
		exit 5
	fi

	echo -e "Exit Code="$EXITCODE >>$LOG

	if [ $EXITCODE -eq 137 ]; then
		echo -e "Time Limit Exceeded (Exit code=$EXITCODE)" >>$LOG
		echo "<pre style='color: orange;'>Time Limit Exceeded</pre>" >>$PROBLEMPATH/$UN/result.html
		continue
	fi
	
	if [ $EXITCODE -eq 159 ]; then
		echo -e "Bad System Call (Exit code=$EXITCODE)" >>$LOG
		echo "<pre style='color: red;'>Potentially Harmful Code. Process terminated.</pre>" >>$PROBLEMPATH/$UN/result.html
		echo "Bad System Call"
		cd ..
		rm -r $JAIL >/dev/null 2>/dev/null
		exit 3
	fi

	if [ $EXITCODE -ne 0 ]; then
		echo -e "Runtime Error" >>$LOG
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
	elif $DIFFTOOL out $PROBLEMPATH/out/test$i.out $DIFFPARAM >/dev/null 2>/dev/null
	then
		ACCEPTED=true
	fi

	if $ACCEPTED; then
		echo -e "ACCEPTED" >>$LOG
		echo "<pre style='color: green;'>ACCEPT</pre>" >>$PROBLEMPATH/$UN/result.html
		((PASSEDTESTS=$PASSEDTESTS+1))
	else
		echo -e "WRONG" >>$LOG
		echo "<pre style='color: red;'>WRONG</pre>" >>$PROBLEMPATH/$UN/result.html
	fi
done

((SCORE=PASSEDTESTS*10000/TST))
echo $SCORE
cd ..
rm -r $JAIL >/dev/null 2>/dev/null
exit 0