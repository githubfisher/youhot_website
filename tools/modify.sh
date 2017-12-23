#!/bin/sh
#send files to apollo

p=`pwd`;
list=`svn st |grep -o '\(application/.*\)\|\(static/.*\)'`
for i in $list
do
    sh tools/update_files_to_apollo.sh  $i;
done

#替换脚本
ssh apollo@apollo.aifudao.com "
    cd ~/bp
    sh tools/replace.sh
"
