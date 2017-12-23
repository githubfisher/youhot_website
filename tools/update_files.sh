#!/bin/sh
# 上传单个文件到所有服务器
if [ $# -lt 1 ]
then
echo "Error - 请用下面的用法"
echo "用法 : $0 本地文件 远程路径(相对于apache/htdocs)"
echo "例如：$0 application/controllers/teacher.php application/controllers/teacher.php "

exit 1
fi

relatePath='/phpstudy/www/'
#servers='aifudao@neil.aifudao.com apollo@apollo.aifudao.com'
servers='style@123.56.246.81'
file=$1
remoteAddr=$2
if [ $# -eq 1 ]
then
    remoteAddr=$1
fi
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
