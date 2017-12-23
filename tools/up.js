/**
 * Created by conzi
 * Date: 2012-11-13
 * Desc: 上传修改过的文件到服务器
*/


var exec = require('child_process').exec,
    fs = require('fs'),
    path = require('path'),
    temp = '.temp' ,
    rJs = /(admin.js|ai.js|page\/.*.js)$/
    rCss = /(admin.css|btn.css|ai.css|page\/.*.css)$/

exec("svn st |grep -o '\\(application\\/.*\\)\\|\\(static\/.*\\)'", function(error, stdout, stderr){

    if(fs.existsSync(temp)){
        exec('rm -rf '+temp);
    }

    exec('mkdir '+temp);

    stdout.split('\n').forEach(function(item, idx){
            item = item.trim();
            if(item === '' ){
                return ;
            }
            console.log('item',item);
            var e = fs.existsSync(temp+'/'+path.dirname(item));
            if(!e){
                exec("mkdir -p "+temp+'/'+path.dirname(item));
            }
            exec('cp '+ item +' '+temp+'/'+item);
        if(item.match(rJs)){
            console.log('minify js:', item);
            exec("uglifyjs --overwrite "+temp+'/'+item);
        }
        if(item.match(rCss)){
            console.log('minify css:', item);
            var p = temp+'/'+item;
            exec("cleancss -o "+p+' '+p);
        }
    })

    var count =0,
        servers = ['neil', 'grissom', 'apollo'];


    servers.forEach(function(server,i){
        console.log('send file to:'+ server);
        var path = 'apache/htdocs/';
        if(server == 'apollo'){
            path = 'bp/'
        }
        var cmd = 'scp -r '+temp+'/* aifudao@' +server+".aifudao.com:"+path;
        exec(cmd, function(e,out, err){
            console.log('done!', out, err);
            if(++count == servers.length){//都执行完了
                exec('rm -rf '+temp);
            }
        });
    })





});


