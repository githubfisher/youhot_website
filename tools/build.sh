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
compiler_file='tools/compiler/compiler.jar';
if [ -f $compiler_file ]; then

    jsext='.js';
    jsfile1='static/js/ai';
    jsfile3='static/js/jquery.validate';

    for i in $jsfile1 $jsfile3
    do
        echo "File $i.js......."
        mv $i$jsext $i'.origin'$jsext
        echo "OK===Mv $i.js"
        java -jar $compiler_file --js $i'.origin'$jsext --js_output_file $i$jsext --charset utf-8
        if [ $? -eq 0 ]; then
            echo "OK====compile $i";
        else
            echo "FAIL--!!!--compile $i"
            exit;
        fi
    done
else
    echo '没有$compiler_file文件，请检查';
    exit;
fi
#tar -cfvz --exclude .svn ../bp-alpha.$today.tgz application config index.php .htaccess static system
tar  --exclude .svn -cvzf ../bp-alpha.$today.tgz application index.php .htaccess static system
echo "打包完成,copy到服务器neil......."
#copy到服务器
#scp ../bp-alpha.$today.tgz aifudao@neil.aifudao.com:apache/htdocs/.
#scp ../bp-alpha.$today.tgz aifudao@grissom.aifudao.com:apache/htdocs/.
#scp ../bp-alpha.$today.tgz aifudao@apollo.aifudao.com:
scp ../bp-alpha.$today.tgz apollo@apollo.aifudao.com:


echo "请登录neil,grissom操作";
