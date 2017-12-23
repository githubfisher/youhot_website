#!/bin/sh
# 上传单个文件到s.aifudao.com
if [ $# -lt 2 ]
then
echo "Error - 请用下面的用法"
echo "用法 : $0 本地文件 远程路径(相对于apache/htdocs)"
echo "例如：$0 application/controllers/teacher.php application/controllers/teacher.php "

exit 1
fi

relatePath='apache/htdocs_s_aifudao/'
#servers='aifudao@neil.aifudao.com apollo@apollo.aifudao.com'
#servers='apollo@apollo.aifudao.com'
servers='aifudao@neil.aifudao.com aifudao@grissom.aifudao.com'
file=$1
remoteAddr=$2
for i in $servers
do
    scp $file $i:$relatePath$remoteAddr
    if [ $? -eq 0 ]
    then
        echo "Update $file to $i:$relatePath$remoteAddr ok ."
    fi
done

if [ $? -eq 0 ]
then
echo "Update ok ."
exit 0
fi
echo "Error:Update got wrong."
