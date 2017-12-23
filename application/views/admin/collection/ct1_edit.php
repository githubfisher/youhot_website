<style>
    .form-group {
        margin-bottom: 23px;
        padding-bottom: 27px;
        border-bottom: 1px solid #f2f2f2;
    }

    .desc-del {
        cursor: pointer;
    }


    .biaoqian input {
        height: 36px;
        line-height: 36px;
    }

    .biaoqian-left {
        width: 5%;
        height: auto;
        line-height: 36px;
        float: left;
        text-align: center;
        margin-left: 15px;
        font-size: 14px;
        color: #111;
        font-weight: bold;
    }

    .biaoqian-right {
        width: 95%;
        height: auto;
        line-height: 36px;
        float: left
    }

    .biaoqian-right .biaoqian-small {
        width: 140px;
        height: 36px;
        line-height: 36px;
        float: left;
        color: #111;
    }

    .biaoqian-small input[type=checkbox] {
        margin-top: 10px;
        width: 15px;
        height: 15px;
        float: left
    }

    .controls ul, li {
        list-style-type: none
    }

    .pull-box {
        width: 100%;
        height: 40px;
    }

    .pull-box-left {
        width: 65px;
        height: 40px;
        float: left;
        line-height: 40px;
    }

    .cropit-image-preview {
        background-color: #f8f8f8;
        background-size: cover;
        border: 1px solid #ccc;
        border-radius: 3px;
        margin-top: 7px;
        width: 752px;
        height: 802px;
        cursor: move;
    }

    .cropit-image-background {
        opacity: .2;
        cursor: auto;
    }

    .image-size-label {
        margin-top: 10px;
    }

    button[type="submit"] {
        margin-top: 10px;
    }

    /*
     * If the slider or anything else is covered by the background image,
     * use relative or absolute position on it
     */
    input.cropit-image-zoom-input {
        position: relative;
        display: inline;
        width: 60%
    }

    /* Show load indicator when image is being loaded */
    .cropit-image-preview.cropit-image-loading .spinner {
        opacity: 1;
    }

    /* Show move cursor when image has been loaded */
    .cropit-image-preview.cropit-image-loaded {
        cursor: move;
    }

    /* Gray out zoom slider when the image cannot be zoomed */
    .cropit-image-zoom-input[disabled] {
        opacity: .2;
    }

    /* Hide default file input button if you want to use a custom button */
    input.cropit-image-input {
        visibility: hidden;
    }

    /* The following styles are only relevant to when background image is enabled */

    /* Translucent background image */
    .cropit-image-background {
        opacity: .1;
    }

    /* Style the background image differently when preview area is hovered */
    .cropit-image-background.cropit-preview-hovered {
        opacity: .2;
    }


    .btn-default {
        width: 72px;
        height: 42px;
        margin-top: 10px;
        line-height: 26px;
    }

    .zidingyi li input {
        width: auto;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
    }

    .zidingyi li {
        margin-bottom: 23px;
        display: inline-block;
    }

    .zidingyi li span {
        line-height: 34px;
    }


    .control-label {
        text-align: right;
    }

    .album-del {
        position: absolute;
        right: 0em;
    }

    div.nav {
        background-color: rgb(233, 233, 233);
        height: 35px;
        line-height: 35px;
    }

    div.nav span {
        background-color: #fff;
        height: 32px;
        margin: 10px auto auto 10px;
        padding: 0 1em;
        width: 6em;
        display: block;
    }
    .album-area {
        background-color: #F8F8F8;
        border-top: 1px solid #9D9D9D;
    }
    .mail-add input{
        width:3em;
    }
    .edit2{
        display: none;
    }
    /**/
    .modal-selector li{
        list-style: none;
        padding-left:15px;
    }
    .sec-selector li, ul[class=sec-selector],ul[class=first-selector],label[class=first-selector],label[class=sec-selector],.pro-selector li,ul[class=pro-selector],label[class=pro-selector]{
        float:left;
    }
    label[class=sec-selector]{
        width:30px;
    }
    .modal-selector{
        padding-bottom: 40px;
        padding-left: 20px;
        /*padding-top: 30px;*/
        /*display:none;*/
    }
    .clear{
        clear:both;
    }
    .selector,.btn-selector{
        float: right;
        margin-right: 10px;
        margin-top: -2px;
        color: black;
    }
    #brand-more,#store-more{
        float: right;
        padding-right: 20px;

    }
    .br-more,.st-more{
        display: none;
    }
    #selected li{
        padding:2px;
        float:left;
        border:1px solid red;
        margin-left: 2px;
        margin-bottom: 2px;
    }
    #selected li label{
        margin-bottom: 0;
    }
    #selected li a{
        background: red;
        border: 1px solid red;
        border-radius: 100%;
        width: 8px;
        height:8px;
        color: white;
        padding-left: 1px;
        padding-right: 3px;
    }
    #page-btn1,#page-btn2{
        width:80px;
        height:30px;
    }
    #page-btn3{
        width:50px;
        height:30px;
    }
</style>
<div class="m-center-right">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>编辑折扣区</h3></div>
    </div>
    <div class="m-content" style="">
        <section class="wrapper" style='width:100%;'>
            <div class="col-lg-12">
                <section class="box ">
                    <div class=" tab-pane fade in active" id="home-2">
                        <form id="product_form" name="product_form" class="form-horizontal">
                            <input type="hidden" name="collection_id" id="collection_id" value="<?= $collection['id'] ?>"/>
                            <input type="hidden" name="rt" value="json"/>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">标题</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="检索名称" style="" class="form-control" name="title"
                                               id="title" value="<?= $collection['title'] ?>" required>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint"><?= 128 - strlen($collection['title']) ?></span>个字
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <span class="error"><?= form_error('title') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

			    <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">副标题</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="副标题" style="" class="form-control" name="subhead"
                                               id="subhead" value="<?= $collection['subhead'] ?>" required>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px"> 
                                        还能输入 <span style="color:#EC1379" id="num-hint"><?= 128 - strlen($collection['subhead']) ?></span>个字
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <span class="error"><?= form_error('subhead') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">描述/备注</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="官网地址" style="" class="form-control" name="description"
                                               id="description" value="<?= $collection['description'] ?>" required>
                                    </div>
                                    <div class="col-sm-2 col-md-2 col-xs-12" style="padding-top:7px">
                                        还能输入 <span style="color:#EC1379" id="num-hint"><?= 256 - strlen($collection['description']) ?></span>个字
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <span class="error"><?= form_error('description') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                             <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">状态</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="status" id="status" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="0">停止使用</option>
                                            <option value="1">启用</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <div class="col-md-12 col-md-offset-01">
                                        <div class="modal-selector">
                                            <div>
                                                <label for="clothing" class="first-selector">品类</label>
                                                
                                                <ul name="clothing" class="first-selector">
                                                    <?php $cnum = 0; ?>
                                                    <?php foreach($cbpd['categorys']['list'] as $item): ?>
                                                    <li>
                                                        <label for="clothing" class="sec-selector"><?= $item['chinese_name']?></label>
                                                        <ul class="sec-selector">
                                                            <?php if($cnum > 0){ ?>
                                                                <li style="color:rgba(255,255,255,0)">不限</li>
                                                            <?php }else{ ?>
                                                                <li><a href="javascript:void(0);" onclick="cancelAll('c');">不限</a></li>
                                                            <?php } ?>
                                                            <?php foreach($item['sub_category'] as $sub): ?>
                                                                <li><input name="<?= $sub['chinese_name']?>" type="checkbox" value="c<?= $sub['id'] ?>" /><?= $sub['chinese_name']?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </li>
                                                    <div class="clear"></div>
                                                    <?php $cnum++; ?>
                                                    <?php endforeach; ?>
                                                </ul>
                                                <div class="clear"></div>
                                                <label for="clothing" class="pro-selector">品牌</label>
                                                <a href="javascript:void(0);" onclick="more();" id="brand-more">更多</a>
                                                <ul name="clothing" class="pro-selector">
                                                    <li><a href="javascript:void(0);" onclick="cancelAll('b');">不限</a></li>
                                                    <?php $i=0; ?>
                                                    <?php foreach($cbpd['brands']['list'] as $item): ?>
                                                        <?php $name = !empty($item['chinese_name'])?$item['chinese_name']:$item['english_name'];?>
                                                        <?php if ($i<8) {?>
                                                            <li><input name="<?= $name;?>" type="checkbox" value="b<?= $item['brandid'] ?>" /><?= $name;?></li>
                                                        <?php } else { ?>
                                                            <li class="br-more"><input name="<?= $name;?>" type="checkbox" value="b<?= $item['brandid'] ?>"/><?= $name;?></li>
                                                        <?php } ?>
                                                    <?php $i++; ?>
                                                    <?php endforeach; ?>
                                                </ul>
                                                <div class="clear"></div>
                                                <label for="clothing" class="pro-selector">价格</label>
                                                <ul name="clothing" class="pro-selector">
                                                    <li><a href="javascript:void(0);" onclick="cancelAll('p');">不限</a></li>
                                                    <?php foreach($cbpd['prices']['list'] as $item): ?>
                                                        <li><input name="<?= $item['name'] ?>" type="checkbox" value="p<?= $item['id'] ?>" /><?= $item['name'] ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                                <div class="clear"></div>
                                                <label for="clothing" class="pro-selector">折扣</label>
                                                <ul name="clothing" class="pro-selector">
                                                    <li><a href="javascript:void(0);" onclick="cancelAll('d');">不限</a></li>
                                                    <?php foreach($cbpd['discounts']['list'] as $item): ?>
                                                        <li><input name="<?= $item['name'] ?>" type="checkbox" value="d<?= $item['id'] ?>" /><?= $item['name'] ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
						<div class="clear"></div>
                    <label for="clothing" class="pro-selector">商城</label>
                    <a href="javascript:void(0);" onclick="more2();" id="store-more"  class="">更多</a>
                    <ul name="clothing" class="pro-selector">
                        <li><a href="javascript:void(0);" onclick="cancelAll('s');">不限</a></li>
                        <?php $i=0; ?>
                        <?php foreach($cbpd['stores']['list'] as $item): ?>
                            <?php if ($i<8) {?>
                                <li><input name="<?=$item['name'];?>" type="checkbox" value="s<?= $item['id'] ?>" /><?=$item['name'];?></li>
                            <?php } else { ?>
                                <li class="st-more"><input name="<?=$item['name'];?>" type="checkbox" value="s<?= $item['id'] ?>"/><?=$item['name'];?></li>
                            <?php } ?>
                        <?php $i++; ?>
                        <?php endforeach; ?>
                    </ul>
                    <div class="clear"></div>
                    <label for="clothing" class="pro-selector st-more">排序</label>
                    <ul name="clothing" class="pro-selector st-more">
                        <li><a href="javascript:void(0);" onclick="cancelAll('o');">不限</a></li>
                        <?php foreach($cbpd['sorts']['list'] as $item): ?>
                            <li><input name="<?= $item['name'] ?>" type="checkbox" value="o<?= $item['type'].'_'.$item['sort'] ?>" /><?= $item['name'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                                            </div>
                                            <div class="clear"></div>
                                            <div>
                                                <label>已选条件：</label>
                                                <ul id="selected">
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                            <div class="btn-selector">
                                                <button type="button" onclick="clearAll('c','b','p','d','s','o');">清空</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group  clearfix">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <button type="button" class="btn btn-purple" style="width:8em" onclick="sub();">保存</button>
                                    <button type="button" class="btn btn-purple" style="width:8em" onclick="bak();">返回</button>
                                </div>
                            </div>
                            <input type="hidden" name="st" id="st" value="<?= $collection['status'] ?>">
                            <input type="hidden" name="cd" id="cd" value='<?= $collection['background_image'] ?>'>
                        </form>
                    </div>
                </section>
            </div>
        </section>
    </div>
</div>
<script>
    function bak()
    {
        window.location.href="/admin/collection/ct1_list";
    }
    function selectAll()
    {
        var obj = $(".modal-selector").find("input");
        var str = '';
        for(var i=0;i<obj.length;i++){
            if(obj[i].checked == true){
                str += obj[i].value+"#";
            }
        }
        // alert(str);
        ss = str.split("#");
        var cate = new Array();
        var br = new Array();
        var dr = new Array();
        var pr = new Array();
	var st = new Array();
        var sr = new Array();
        for(var i=0;i<ss.length;i++){
            var prefix = ss[i].substring(0,1);
            var id = ss[i].substr(1,ss[i].length-1);
            switch(prefix){
                case 'c':
                    cate.push(id);
                    break;
                case 'b':
                    br.push(id);
                    break;
                case 'p':
                    pr.push(id);
                    break;
                case 'd':
                    dr.push(id);
                    break;
		case 's':
                    st.push(id);
                    break;
                case 'o':
                    var arr = id.split('_'); 
                    sr.push(arr);
                    break;
            }
        }
        var filter = new Array();
        filter[0] = JSON.stringify(cate);
        filter[1] = JSON.stringify(br);
        filter[2] = JSON.stringify(pr);
        filter[3] = JSON.stringify(dr);
	filter[4] = JSON.stringify(st);
        filter[5] = JSON.stringify(sr);
        var str = '{"category":'+filter[0]+',"brand":'+filter[1]+',"price":'+filter[2]+',"discount":'+filter[3]+',"store":'+filter[4]+',"sort":'+filter[5]+'}';
        return str;
    }
    function more()
    {
        var status = $(".br-more").css('display');
        if ( status == 'none') {
            $(".br-more").show();
        } else {
            $(".br-more").hide();
        }
    }
    function more2()
    {
        var status = $(".st-more").css('display');
        if ( status == 'none') {
            $(".st-more").show();
        } else {
            $(".st-more").hide();
        }
    }
    function seletor()
    {
        var status = $(".modal-selector").css('display');
        if ( status == 'none') {
            $(".modal-selector").show();
        } else {
            $(".modal-selector").hide();
        }
    }
    function cancelAll(tag)
    {
        $("input:checkbox[value^='"+tag+"']").attr('checked',false);
        $("li[id^='"+tag+"']").remove();
    }
    function cancel(id)
    {
        $("#"+id).remove();
        $("input:checkbox[value='"+id+"']").attr('checked',false);
    }
    function clearAll(c,b,p,d,s,o)
    {
        $("input:checkbox[value^='"+c+"']").attr('checked',false);
        $("input:checkbox[value^='"+b+"']").attr('checked',false);
        $("input:checkbox[value^='"+p+"']").attr('checked',false);
        $("input:checkbox[value^='"+d+"']").attr('checked',false);
        $("li[id^='"+c+"']").remove();
        $("li[id^='"+b+"']").remove();
        $("li[id^='"+p+"']").remove();
        $("li[id^='"+d+"']").remove();
	$("input:checkbox[value^='"+s+"']").attr('checked',false);
        $("input:checkbox[value^='"+o+"']").attr('checked',false);
        $("li[id^='"+s+"']").remove();
        $("li[id^='"+o+"']").remove();
    }
    function sub()
    {
        var id = $("#collection_id").val();
        var title = $("#title").val();
        var description = $("#description").val();
        var status = $("#status").val();
        var str = selectAll();
	var subhead = $("#subhead").val();
        var url = '/admin/collection/ct1_update';
	console.log(title);
        $.post(url,{id,title,description,status,str,subhead},function (data) {
            if (data.res == 0) {
                showSuccess();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
    $(function () {
        var status = $("#st").val();
        $("#status").val(status);
        $("#layout-modal").on('change', 'select[name=author]', function () {
            console.log($(':selected', this).val());
            $("a.product-add").attr('data-author', $(':selected', this).val());
        });

        $(':checkbox').click(function(){
            console.log($("#"+$(this).attr("value")));
            if ($("#"+$(this).attr("value")).length > 0) {
                cancel($(this).attr("value"));
            } else {
                $str = "<li id=\""+$(this).attr("value")+"\"><label>"+$(this).attr("name")+"</label><a href=\"javascript:void(0);\" id=\""+$(this).attr("value")+"\" onclick=\"cancel(this.id);\"> x </a></li>";
                $("#selected").append($str);
            }
        });

        var cd = $("#cd").val();
        cd = eval("("+cd+")");
        var a = new Array();
        var c = cd.category;
        for(var i=0;i<c.length;i++){
            a.push("c"+c[i]);
        }
        var b = cd.brand;
        for(var i=0;i<b.length;i++){
            a.push("b"+b[i]);
        }
        var d = cd.discount;
        for(var i=0;i<d.length;i++){
            a.push("d"+d[i]);
        }
        var p = cd.price;
        for(var i=0;i<p.length;i++){
            a.push("p"+p[i]);
        }
	var s = cd.store;
        for(var i=0;i<s.length;i++){
            a.push("s"+s[i]);
        }
	var o = cd.sort;
        for(var i=0;i<o.length;i++){
            a.push("o"+o[i]);
        }
        var obj = $(".modal-selector").find("input");
        for(var i=0;i<obj.length;i++){
            for(var n=0;n<a.length;n++){
                if(obj[i].value == a[n]){
                    obj[i].checked = true;
                    $str = "<li id=\""+obj[i].value+"\"><label>"+obj[i].name+"</label><a href=\"javascript:void(0);\" id=\""+obj[i].value+"\" onclick=\"cancel(obj[i].id);\"> x </a></li>";
                    $("#selected").append($str);
                }
            }
        }
    })
</script>

