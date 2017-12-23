#!/bin/sh
today=`date '+%Y%m%d%H%I%S'`;
svn export http://apollo.aifudao.com/svn/fudao/bp/web/trunk/ bp_$today;
cd bp_$today;
sh tools/build.sh;
