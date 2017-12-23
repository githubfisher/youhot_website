#!/bin/sh
#文件打包
#在tools目录下打包
correct_dir='tools';
CUR_DIR=`dirname $0` ;
if [ "$correct_dir" != "$CUR_DIR" ]
then
echo "目录不正确..，请sh tools/build.sh";
exit;
fi

temp=".temp"

if [ -f $temp ]
then
    echo ".temp exsisted"
    rm -rf $temp
fi
    mkdir $temp

    cp -r application system static index.php .htaccess $temp
    cp -r tools $temp

cd $temp



today=`date '+%Y%m%d%H%I%S'`;
#js压缩
echo "=======js compiling========="

jsfiles='admin.js ai.js jquery.validate.js page/course_list.js jquery.jeditable.conf.js';

for i in $jsfiles
do
    echo "File $i.js......."
    uglifyjs --overwrite static/js/$i
    if [ $? -eq 0 ]; then
        echo "OK====compile $i";
    else
        echo "FAIL--!!!--compile $i"
        exit;
    fi
done

echo "=======css compiling========="

cssfiles='ai.css page/course_list.css btn.css admin.css';
for i in $cssfiles
do
    echo "File $i......."
    cleancss -o static/css/$i static/css/$i

    if [ $? -eq 0 ]; then
        echo "OK====compile $i";
    else
        echo "FAIL--!!!--compile $i"
        exit;
    fi
done

#tar -cfvz --exclude .svn ../bp-alpha.$today.tgz application config index.php .htaccess static system
tar  --exclude .svn -cvzf ../bp-alpha.$today.tgz application index.php .htaccess static system
echo "打包完成,copy到服务器neil......."
#copy到服务器
scp ../bp-alpha.$today.tgz aifudao@neil.aifudao.com:apache/htdocs/.
scp ../bp-alpha.$today.tgz aifudao@grissom.aifudao.com:apache/htdocs/.
scp ../bp-alpha.$today.tgz aifudao@apollo.aifudao.com:


echo "请登录neil,grissom操作";
