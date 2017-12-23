#!/bin/sh
#send modified files to product env

p=`pwd`;
#svn log -v -r 6310  |grep -o '\(application/.*\)\|\(static/.*\)'
list=`git st | grep modified|grep -o '\(application/.*\)\|\(static/.*\)'`
#list=`git st |grep -o '\(application/.*\)\|\(static/.*\)'`
for i in $list
do
    ./tools/update_files.sh  $i $i;
done
