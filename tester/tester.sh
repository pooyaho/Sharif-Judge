#!/bin/bash
###########################################
# IN THE NAME OF ALLAH                    #
# @file tester.sh                         #
# NewJudge                                #
# Written by Mohammad Javad Naderi        #
###########################################

# Example run:
# tester.sh /home/mohammad/newjudge/homeworks/hw6/p1 mjnaderi tartib c 1 50000 7 -iw

################ Settings ###############
# if you want to use dietlibc instead of glibc, set path to diet executable file here
# dietlibc uses less system calls and therefore, it brings more security
DIET=""
#DIET="dietlibc/bin-i386/diet"
SECCOMP_ON=true # turn seccomp sandboxing for c/c++ on or off
SHIELD_ON=true # turn shield for c on or off
# shield is not enabled for c++. Because it causes some problems if the code includes cstdio

################### Initialization ####################
TIMEOUT_EXISTS=true
hash timeout 2>/dev/null || TIMEOUT_EXISTS=false

########### saving arguments ###########
PROBLEMPATH=$1 # problem dir
UN=$2 # username
FILENAME=$3 # file name without extension
EXT=$4 # file extension
TIMELIMIT=$5
MEMLIMIT=$6
HEADER=$7
DIFFPARAM=$8
if [ "$DIFFPARAM" = "" ]; then
	DIFFPARAM="-bB"
fi

#########################################
TST="$(ls $PROBLEMPATH/in | wc -l)"  # Number of Test Cases
LOG="$PROBLEMPATH/$UN/log"
JAIL=jail-$RANDOM
mkdir $JAIL

#TZ='Asia/Tehran' date >$LOG
date >$LOG
#echo -e "\nJAILPATH="$PROBLEMPATH/$UN/jail"\nEXT="$EXT"\nTIME LIMIT="$TIMELIMIT"\nMEM LIMIT="$MEMLIMIT"\nSECURITY HEADER="$HEADER"\nTEST CASES="$TST"\nDIFF PARAM="$DIFFPARAM"\n" >>$LOG

########################################################################################################
############################################ COMPILING JAVA ############################################
########################################################################################################
if [ "$EXT" = "java" ]; then
	cp $PROBLEMPATH/$UN/$FILENAME.$EXT $JAIL/$FILENAME.$EXT
	echo -e "Compiling as Java\n" >>$LOG
	javac $JAIL/$FILENAME.$EXT >/dev/null 2>$JAIL/cerr
	EXITCODE=$?
	if [ $EXITCODE -ne 0 ]; then
		echo -e "Compile Error\n" >>$LOG
		echo '<pre style="color:blue;">Compile Error</pre>' >$PROBLEMPATH/$UN/result.html
		echo '<pre style="color:red;">' >> $PROBLEMPATH/$UN/result.html
		#filepath="$(echo "${JAIL}/${FILENAME}.${EXT}" | sed 's/\//\\\//g')" #replacing / with \/
		filepath=$JAIL/$FILENAME.$EXT
		filepath=${s//\//\\\/} #replacing / with \/
		#echo "$filepath" >>$LOG
		(cat $JAIL/cerr | head -10 | sed "s/$filepath//g" | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		#(cat $JAIL/cerr) >> $PROBLEMPATH/$UN/result.html
		echo "</pre>" >> $PROBLEMPATH/$UN/result.html
		rm -r $JAIL >/dev/null 2>/dev/null
		echo "-1"
		exit 1
	fi
fi

########################################################################################################
########################################## COMPILING PYTHON 3 ##########################################
########################################################################################################
if [ "$EXT" = "py" ]; then
	cp $PROBLEMPATH/$UN/$FILENAME.$EXT $JAIL/$FILENAME.$EXT
	echo -e "Checking Python Syntax\n" >>$LOG
	python3 -O -m py_compile $JAIL/$FILENAME.$EXT >/dev/null 2>$JAIL/cerr
	EXITCODE=$?
	echo -e "Syntax checked. Exit Code="$EXITCODE"\n" >>$LOG
	if [ $EXITCODE -ne 0 ]; then
		echo -e "Syntax Error\n" >>$LOG
		echo '<pre style="color:blue">Syntax Error</pre>' >$PROBLEMPATH/$UN/result.html
		echo '<pre style="color: red;">' >> $PROBLEMPATH/$UN/result.html
		#filepath="$(echo "${JAIL}/${FILENAME}.${EXT}" | sed 's/\//\\\//g')" #replacing / with \/
		filepath=$JAIL/$FILENAME.$EXT
		filepath=${s//\//\\\/} #replacing / with \/
		#echo "$filepath" >>$LOG
		(cat $JAIL/cerr | head -10 | sed "s/$filepath//g" | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		echo "</pre>" >> $PROBLEMPATH/$UN/result.html
		rm -r $JAIL >/dev/null 2>/dev/null
		echo "-2"
		exit 1
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
	cp $PROBLEMPATH/$UN/$FILENAME.$EXT $JAIL/code.c
	echo -e "Compiling as $EXT\n" >>$LOG
	if $SECCOMP_ON; then
		echo -e "Using Seccomp\n" >>$LOG
		cp seccomp/* $JAIL/
		if $SHIELD_ON && [ "$EXT" = "c" ]; then #overwrite def.h
			echo -e "Using Shield\n" >>$LOG
			cp shield/def.h $JAIL/def.h
		fi
		cp $PROBLEMPATH/$UN/$FILENAME.$EXT $JAIL/code.c
		# adding define to beginning of code
		echo '#define main themainmainfunction' | cat - $JAIL/code.c > $JAIL/thetemp && mv $JAIL/thetemp $JAIL/code.c
		$DIET $COMPILER $JAIL/shield.$EXT -lm -O2 -o $JAIL/$FILENAME >/dev/null 2>$JAIL/cerr
	elif $SHIELD_ON && [ "$EXT" = "c" ]; then
		echo -e "Using Shield\n" >>$LOG
		cp shield/* $JAIL/
		cp $PROBLEMPATH/$UN/$FILENAME.$EXT $JAIL/code.c
		# adding define to beginning of code
		echo '#define main themainmainfunction' | cat - $JAIL/code.c > $JAIL/thetemp && mv $JAIL/thetemp $JAIL/code.c
		$DIET $COMPILER $JAIL/shield.$EXT -lm -O2 -o $JAIL/$FILENAME >/dev/null 2>$JAIL/cerr
	else
		$DIET $COMPILER $JAIL/code.$EXT -lm -O2 -o $JAIL/$FILENAME >/dev/null 2>$JAIL/cerr
	fi
	EXITCODE=$?
	echo -e "Compiled. Exit Code="$EXITCODE"\n" >>$LOG
	if [ $EXITCODE -ne 0 ]; then
		echo -e "Compile Error\n" >>$LOG
		echo '<pre style="color:blue">Compile Error</pre>' >$PROBLEMPATH/$UN/result.html
		echo '<pre style="color: red;">' >> $PROBLEMPATH/$UN/result.html
		SHIELD_ACT=false
		if $SHIELD_ON; then
			while read line; do
				if [ "`echo $line|cut -d" " -f1`" = "#define" ]; then
					if grep -wq $(echo $line|cut -d" " -f3) $JAIL/cerr; then
						echo `echo $line|cut -d"/" -f3` >> $PROBLEMPATH/$UN/result.html
						SHIELD_ACT=true
						break
					fi
				fi
			done <shield/def.h
		fi
		if ! $SHIELD_ACT; then
			(cat $JAIL/cerr | head -10 | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		fi
		echo "</pre>" >> $PROBLEMPATH/$UN/result.html
		rm -r $JAIL >/dev/null 2>/dev/null
		echo "-1"
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
		if $TIMEOUT_EXISTS; then
			timeout -s9 $TIMELIMIT java -cp $JAIL -Djava.security.manager -Djava.security.policy=java.policy $FILENAME  <$PROBLEMPATH/in/test$i.in >$JAIL/out 2>/dev/null
		else
			java -cp $JAIL -Djava.security.manager -Djava.security.policy=java.policy $FILENAME  <$PROBLEMPATH/in/test$i.in >$JAIL/out 2>/dev/null
		fi
		#echo "java -cp $PROBLEMPATH/$UN/jail $FILENAME <$PROBLEMPATH/in/test$i.in >$PROBLEMPATH/$UN/jail/$UN.out 2>$PROBLEMPATH/$UN/tmp" >>$LOG
		#java -cp $PROBLEMPATH/$UN/jail $FILENAME <$PROBLEMPATH/in/test$i.in >$PROBLEMPATH/$UN/jail/$UN.out 2>$PROBLEMPATH/$UN/tmp
		EXITCODE=$?
		echo "$EXITCODE" >>$LOG
	elif [ "$EXT" = "c" ]; then
		echo -e "Running as C" >>$LOG
		if $TIMEOUT_EXISTS; then
			timeout -s9 $TIMELIMIT $JAIL/$FILENAME <$PROBLEMPATH/in/test$i.in >$JAIL/out 2>/dev/null
		else
			$JAIL/$FILENAME <$PROBLEMPATH/in/test$i.in >$JAIL/out 2>/dev/null
		fi
		EXITCODE=$?
	elif [ "$EXT" = "cpp" ]; then
		echo -e "Running as C++" >>$LOG
		if $TIMEOUT_EXISTS; then
			timeout -s9 $TIMELIMIT $JAIL/$FILENAME <$PROBLEMPATH/in/test$i.in >$JAIL/out 2>/dev/null
		else
			$JAIL/$FILENAME <$PROBLEMPATH/in/test$i.in >$JAIL/out 2>/dev/null
		fi
		EXITCODE=$?
	elif [ "$EXT" = "py" ]; then
		echo -e "Running as python" >>$LOG
		if $TIMEOUT_EXISTS; then
			timeout -s9 $TIMELIMIT python3 -O $JAIL/$FILENAME.$EXT <$PROBLEMPATH/in/test$i.in >$JAIL/out 2>$JAIL/tmp
		else
			python3 -O $JAIL/$FILENAME.$EXT <$PROBLEMPATH/in/test$i.in >$JAIL/out 2>$JAIL/tmp
		fi
		EXITCODE=$?
		echo "<pre>" >>$PROBLEMPATH/$UN/result.html
		filepath=$JAIL/$FILENAME.$EXT
		filepath=${s//\//\\\/} #replacing / with \/
		(cat $JAIL/tmp | head -5 | sed "s/$filepath//g" | sed 's/&/\&amp;/g' | sed 's/</\&lt;/g' | sed 's/>/\&gt;/g' | sed 's/"/\&quot;/g') >> $PROBLEMPATH/$UN/result.html
		echo "</pre>" >>$PROBLEMPATH/$UN/result.html
		rm $JAIL/tmp
	else
		echo -e "EXT not supported" >>$LOG
		echo "-1"
		rm -r $JAIL >/dev/null 2>/dev/null
		exit 1
	fi

	echo -e "Exit Code="$EXITCODE >>$LOG

	if [ $EXITCODE -eq 137 ]; then
		echo -e "Time Limit Exceeded (Exit code=$EXITCODE)" >>$LOG
		echo "<pre style='color: orange;'>Time Limit Exceeded</pre>" >>$PROBLEMPATH/$UN/result.html
		continue
	fi
	
	if [ $EXITCODE -eq 159 ]; then
		echo -e "Bad System Call (Exit code=$EXITCODE)" >>$LOG
		echo "<pre style='color: red;'>Potentially Harmful Code</pre>" >>$PROBLEMPATH/$UN/result.html
		echo "-3"
		rm -r $JAIL >/dev/null 2>/dev/null
		exit 1
	fi

	if [ $EXITCODE -ne 0 ]; then
		echo -e "Runtime Error" >>$LOG
		echo "<pre style='color: orange;'>Runtime Error</pre>" >>$PROBLEMPATH/$UN/result.html
		continue
	fi
	
	if diff $JAIL/out $PROBLEMPATH/out/test$i.out $DIFFPARAM >/dev/null 2>/dev/null
	then
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

rm -r $JAIL >/dev/null 2>/dev/null