<?php
$this->layout->load_js('admin/plugins/jquery-validation/js/jquery.validate.js');
$this->layout->load_js("admin/modules/cropit/jquery.cropit.js");
$this->layout->placeholder('title', element('title', $collection, ''));
$this->layout->load_js('admin/modules/jsrender/jsrender.min.js');
?>
<style>

    .cropit-image-preview {
        background-size: cover;
        border: 1px solid ;
        border-radius: 3px;
        margin-top: 7px;
        width: 582px;/*375px*//*582px*//*712px*/
        height: 562px;/*200px*//*562px*//*292px*/
        cursor: move;
    }

    .col-cover-image {
        width: 100%;
        position: relative;
        min-height: 120px;
        border: 2px dotted #CACACA;
    }
    .col-cover-image img{
        max-width:100%;
        max-height:100%;
    }

    .col-cover-image span {
        top: 40%;
        width:7em;
    }

    .col-item-image {
        width: 100%;
        position: relative;
    }

    .col-item-image img {
        width: 100%;
        border:1px solid #e8e8e8;
        border-bottom-width: 0px;
    }

    .col-item-image span {
        top: 40%;
        width:6em;
    }

    .cropit-image-background {

        cursor: auto;
    }

    .image-size-label {
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

    /* Limit the background image by adding overflow: hidden */
    #image-cropper {
        overflow: hidden;
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
        opacity: .1;
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
        opacity: .3;
    }

    ul.ul-item li {
        list-style: none;
        float: left;
        width: 30%;
        /*min-height: 320px;*/
        min-height: 220px;
        margin-right: 3%;

    }

    ul.ul-item li textarea {
        width: 100%;
        padding: 17px 10px;
        border: 1px solid #E5E5E5;
    }

    ul.ul-item li.item-placeholder {
        border: 1px dashed #e5e5e5;
       /* height: 436px;*/ /* 500px */
        min-height: 155px !important;/*250px !important;*/
        position: relative;
    }


    .modal-header{
        background:#000;
    }
    .modal-header h4{
        color:#fff;
    }
    .modal-body{
        background-color: #F0F3F4;
    }
    .modal-footer {
         border-top: 0;
        background-color: #F0F3F4;
    }
    .modal-body ul li{
        width:30%;
    }
    .modal-body ul li{
        width: 28%;
        float: left;
        height: 83px;
        background: #fff;
        list-style-type: none;
        border: 1px solid #e8e8e8;
        padding: 15px;
        margin: 1% 2.3%;
    }
    .modal-body ul li span{
        font-size:12px;
        color:#1E1E1E;
    }
    .modal-body ul li input:first-child{
        display: block;
        float: left;
        width: 14px;
        margin-top: 20px;
        margin-right: 5px;
        height: 14px;
        margin-left: -38px;
    }
    .modal-body ul li label span img{
        width:50px;height:50px;
        display:block;
        float:left;
        margin-right:10px;}
    button.close{    filter: alpha(opacity=60);
        opacity: .6;
        color: #ffffff !important;
    }
    .modal-footer button{
        width:136px;
        height:46px;
    }
    .modal-body ul li label{
        font-weight: normal;
        margin-bottom: auto;
    }

    div.sel-relate-p{
        height: 42px;
        border: 1px solid #BFB6B6;
        text-align: center;
        line-height:42px;
        overflow:hidden;
        cursor:pointer;
    }
    div.rp-content{
        height:100%;
        color: #5d5d5d;
    }
    div.rp-content img{
        width:15%;
        /*height:70%;*/
    }
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
        padding-top: 10px;
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
<script>
    function seletor()
    {
        var status = $(".modal-selector").css('display');
        if ( status == 'none') {
            $(".modal-selector").show();
        } else {
            $(".modal-selector").hide();
        }
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
    function cancel(id)
    {
        $("#"+id).remove();
        $("input:checkbox[value='"+id+"']").attr('checked',false);
    }
    function cancelAll(tag)
    {
        $("input:checkbox[value^='"+tag+"']").attr('checked',false);
        $("li[id^='"+tag+"']").remove();
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
	$("#keywords").val('');
	$("input:checkbox[value^='"+s+"']").attr('checked',false);
        $("input:checkbox[value^='"+o+"']").attr('checked',false);
        $("li[id^='"+s+"']").remove();
        $("li[id^='"+o+"']").remove();
	jQuery('#sel-product-modal .modal-body').html("请先选择筛选条件...");
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
        return filter;
    }
    function submitAll()
    {
        var filter = selectAll();
	var keywords = $("#keywords").val();
	var url = '/product/filterProduct';
        $.post(url, {'rt': 'json',of:0, lm: 12, cate:filter[0], br:filter[1], pr:filter[2], dr:filter[3], 
            'userid': "<?=( $this->_data['usertype'] == USERTYPE_BUYER )?'':$responsible_userid?>", 'kw':keywords, st:filter[4], sr:filter[5]}, function (data) {
            if (parseInt(data.total) > 0) {
                var tpl = $.templates("#product-item-tpl");
                jQuery('#sel-product-modal .modal-body').html("<ul>"+tpl.render(data.list)+"</ul>");
                // 分页信息
                var ap = Math.ceil(data.total/12);
                $("#allpage").val(ap);
                $("#nowpage").val(0);
                $("#allnum").html(data.total);
                $("#pageall").html(ap);
                $("#pagenow").html(1);
            } else {
                jQuery('#sel-product-modal .modal-body').html("没有符合条件的商品！");
            }
        }, 'json');
        return false;
    }
    function showPic(sUrl,event){
        var x,y;
        x = event.clientX;
        y = event.clientY;
        document.getElementById("bigger").style.left = x;
        document.getElementById("bigger").style.top = y;
        document.getElementById("bigger").innerHTML = "<img src=\"" + sUrl + "\">";
        document.getElementById("bigger").style.display = "block";
    }
    function hiddenPic(){
        document.getElementById("bigger").innerHTML = "";
        document.getElementById("bigger").style.display = "none";
    }
</script>
<div class="m-center-right">
    <form id="col-edit-form" action="/collection/save" method="post">
        <input type="hidden" name="collection_id" value="<?= $collection['id'] ?>"/>
        <input type="hidden" name="status" value="<?=COLLECTION_STATUS_DRAFT?>"/>
        <div class="m-center-top">
            <div class="col-sm-4"><h3>专辑管理</h3></div>
            <div class="col-sm-8"><h3 class="text-right">
                    <input type="submit" class="btn btn-purple" value="保存专辑"/>
                    <input type="button" class="btn btn-light-purple" onclick="history.back()" value="返回"/>
                </h3>
            </div>
        </div>
        <div class="m-content">
            <section class="wrapper" style='width:100%;'>
                <div class="col-lg-12">
                    <section class="box ">
                        <header class="panel_header">
                            <h2 class="title pull-left">编辑专辑</h2>
                        </header>
                        <div class="content-body">
                            <div class="row">
                                <div class="col-md-5 col-xs-12">
                                    <div class="form-group">
                                        <label class="form-label" for="field-1">封面</label>
                                        <div class="col-cover-image" data-toggle="modal" data-target="#crop-image-modal">
                                            <input type="hidden" name="cover_image" value="<?= $collection['cover_image'] ? $collection['cover_image'] : '' ?>"/>
                                            <img id="col-cover-image" src="<?= $collection['cover_image'] ? $collection['cover_image'] : 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D' ?>"/>
                                            <span class="btn btn-half-black btn-corner center-btn" >+ 添加封面</span>
                                        </div>
                                        <div class="desc mt1em">封面图片 尺寸需大于710*290像素,添加后可以裁剪</div>
                                    </div>
                                </div>
                                <div class="col-md-7 col-xs-12">
                                    <div class="form-group">
                                        <label class="form-label" for="txtitle">标题</label>
                                        <div class="controls">
                                            <input type="text" placeholder="标题" class="form-control" name="title"
                                                   id="txtitle" value="<?= $collection['title'] ?>" required>
                                            <div>
                                                <span class="error"><?= form_error('title') ?></span>
                                            </div>
                                        </div>
                                    </div>
				    <div class="form-group">
                                        <label class="form-label" for="subhead">副标题</label>
                                        <div class="controls">
                                            <input type="text" placeholder="副标题" class="form-control" name="subhead"
                                                   id="subhead" value="<?= $collection['subhead'] ?>">
                                            <div>
                                                <span class="error"><?= form_error('subhead') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="field-1">描述</label>
                                        <div class="controls">
                                            <textarea name="description" style="width:100%" rows="5" id="field-1"  class="form-control"><?= $collection['description'] ?></textarea>
                                            <div>
                                                <span class="error"><?= form_error('description') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>
                </div>
            </section>
    </form>
    <section class="wrapper" style='width:100%;'>
        <div class="col-lg-12">
            <section class="box ">
                <header class="panel_header">
                    <h2 class="title pull-left">内容列表</h2>
                </header>
                <div class="content-body clearfix">
                    <ul class="ul-item">
                        <?php foreach ($collection['item_list'] as $item): ?>
                            <li>
                                <form action="/collection/album/update" method="post" class="col-item-form">
                                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>"/>
                                    <div class="form-group">
                                       <!-- <div class="col-item-image" data-toggle="modal" data-target="#crop-item-image-modal">
                                            <input type="hidden" name="content" value="<?= element('content', $item, '') ?>"/>
                                            <img src="<?= element('content', $item, '') ?>" width="266" height="220"/>
                                            <span class="btn btn-half-black btn-corner center-btn">设置图片</span>
                                        </div>-->
                                        <!--<div><textarea rows="3" name="text"><?= $item['text'] ?></textarea></div>-->
                                        <div><label for="text" style="width:100%;">备注<input name="text" style="float:right;width:86%;" value="<?= $item['text'] ?>"></label></div>
                                        <div><label for="text" style="width:100%;">排序<input name="position" style="float:right;width:86%;" value="<?php echo $item['position'] ?>" placeholder="0"></label></div>
                                        <div style="margin:9px auto 13px;">
                                            <button type="submit" class="btn btn-black btn-nocorner" style="width:48%">
                                                <i class="icon icon-save"></i>保存
                                            </button>
                                            <input type="button" value="X 删除" class="btn btn-winered btn-nocorner item-del-btn pull-right" style="width:48%;" data-item-id="<?= $item['id'] ?>" data-product-id="<?= $item['product_id'] ?>"/>
                                        </div>
                                        <div class="sel-relate-p ">
                                            <input type="hidden" value="<?= $item['product_id'] ?>" name="product_id"/>
                                            <div class="rp-ctner">
                                            <?php if(!empty($item['product_id'])):?>
                                                <div class="rp-content">
                                                <img src="<?= $item['product_cover_image']?>" height="20px" class=" col-relate-product"/>
                                                <span><?= $item['product_title'] ?></span>
                                                </div>
                                            <?php else:?>
                                                <i class="icon icon-select"></i><span>选择商品</span>
                                            <?php endif;?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </li>
                        <?php endforeach; ?>
                        <li class="item-placeholder">
                            <div class="btn item-add-btn  center-btn" style="font-size: 400%;color: #D3D3D3;top:18%;"><!--top:40%">-->
                                +
                            </div>

                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </section>
</div>
<!-- General section box modal start -->
<div class="modal" id="crop-image-modal" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
    <div class="modal-dialog animated fadeInDown modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="ultraModal-Label">专辑封面图片<span class="desc" style="margin-left:2em;color:gray;font-size:80%">尺寸710*290</span></h4>
            </div>
            <div class="modal-body">
                <div id="col-cover-image-cropper" data-cid="<?= element('id', $collection, 0) ?>" class="sub-section image-cropper image-background-border">
                    <form action="" id="cover-image-form" method="">
                        <div class="container-fluid">
                            <div class="demo">
                                <div class="column">
                                    <div class="cropit-image-preview-container">
                                        <div class="cropit-image-preview" style="">
                                            <div class="spinner">
                                                <div class="spinner-dot">
                                                </div>
                                                <div class="spinner-dot">
                                                </div>
                                                <div class="spinner-dot">
                                                </div>
                                            </div>
                                            <div class="error-msg">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slider-wrapper">
                                        <span class="glyphicon glyphicon-picture"></span>
                                        <input type="range" class="cropit-image-zoom-input custom">
                                        <span class="glyphicon glyphicon-picture picture-large"></span>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="btns">
                                        <input type="file" name="file" class="cropit-image-input custom">

                                        <div class="btn select-image-btn btn-primary">
                                            <span class="glyphicon glyphicon-picture"></span> 选择新图片
                                        </div>
                                        <div class="btn upload-btn btn-success">
                                            <span class="glyphicon glyphicon-cloud-upload"></span>
                                            上传
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="filename">
                            <input type="hidden" name="file_content">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="crop-item-image-modal" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
    <div class="modal-dialog animated fadeInDown modal-lg " role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="ultraModal-Label">专辑内容图片<span class="desc" style="margin-left:2em;color:gray;font-size:80%">尺寸650*680</span></h4>
            </div>
            <div class="modal-body">
                <div id="col-item-image-cropper" data-cid="<?= element('id', $collection, 0) ?>" class="sub-section image-cropper image-big-cropper image-background-border">
                    <form action="" id="" method="">
                        <div class="container-fluid">
                            <div class="demo">
                                <div class="column">
                                    <div class="cropit-image-preview-container">
                                        <div class="cropit-image-preview" style="width: 650px;height:680px">
                                            <div class="spinner">
                                                <div class="spinner-dot">
                                                </div>
                                                <div class="spinner-dot">
                                                </div>
                                                <div class="spinner-dot">
                                                </div>
                                            </div>
                                            <div class="error-msg">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slider-wrapper">
                                        <span class="glyphicon glyphicon-picture"></span>
                                        <input type="range" class="cropit-image-zoom-input custom">
                                        <span class="glyphicon glyphicon-picture picture-large"></span>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="btns">
                                        <input type="file" name="file" class="cropit-image-input custom">

                                        <div class="btn select-image-btn btn-primary">
                                            <span class="glyphicon glyphicon-picture"></span> 选择新图片
                                        </div>
                                        <div class="btn upload-btn btn-success">
                                            <span class="glyphicon glyphicon-cloud-upload"></span>
                                            上传
                                        </div>
                                        <div class="btn  btn-default auto-height-btn"  style="margin-top: 2em">
                                            <span class="glyphicon glyphicon-resize-small"></span>
                                            自适应高度
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="filename">
                            <input type="hidden" name="file_content">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="sel-product-modal">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <button type="button" class="selector" onclick="seletor();">筛选</button>
                <h4 class="modal-title">选择商品</h4>
            </div>
            <div class="modal-selector">
		<label for="keywords">
                     <span>搜索</span>
                     <input type="text" name="keywords" id="keywords" value="">
                </label>
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
                    <label for="clothing" class="pro-selector st-more">商城</label>
                    <a href="javascript:void(0);" onclick="more2();" id="store-more"  class="st-more">更多</a>
                    <ul name="clothing" class="pro-selector st-more">
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
                    <button onclick="clearAll('c','b','p','d','s','o');">清空</button>
                    <button onclick="submitAll();">提交</button>
                </div>
            </div>
	    <div id="bigger" style="display:none;position:fixed;z-index:100;left:0;bottom:0;"></div>
            <div class="modal-body clearfix">
                请先选择筛选条件...
                <!--loading...-->
            </div>
            <div class="modal-footer">
                <div style="float: left;padding-left: 20%;padding-top: 10px;" id="page-btn">
                    <label>结果数：<b><span id="allnum"></span></b></label>
                    <label>总页数：<b><span id="pageall"></span></b></label>
                    <label>当前页：<b><span id="pagenow"></span></b></label>
                    <button id="page-btn1">上一页</button>
                    <button id="page-btn2">下一页</button>
                    <input type="text" name="gopage" id="gopage" value="" style="width:30px;height:26px;">
                    <button id="page-btn3">GO!</button>
                    <input type="hidden" name="allpage" id="allpage" value="">
                    <input type="hidden" name="nowpage" id="nowpage" value="">
                    <input type="hidden" name="data-item" id="data-item" value="">
                </div>
                <button type="button" class="btn btn-purple" data-dismiss="modal">添加</button>
            </div>
        </div>
    </div>
</div>
<!-- 

    <span style="font-size:16px;" onmouseover="showPic('{{if cover_image}}{{:cover_image}}{{else}}data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D{{/if}}', event);" onmouseout="hiddenPic();"><a><b>[大图]</a></b></span>
-->
<!-- modal end -->
<script id="product-item-tpl" type="text/x-jsrender">
<li>
    <label>
    <input type="radio" name="p-item-checkbox" value="{{:id}}"/>
    <span>
    <img src="{{if cover_image}}{{:cover_image}}{{else}}data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D{{/if}}" alt="{{:title}}" width="50px" />
    <span>{{:title}}</span>
    </span>
    <span style="font-size:16px;" onmouseover="showPic('{{if cover_image}}{{:cover_image}}{{else}}data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D{{/if}}', event);" onmouseout="hiddenPic();"><a><b>[大图]</a></b></span>
   <span>价格: {{:price}}{{if presale_price > 0}} , <i style="text-decoration:line-through;">{{:presale_price}}</i>{{/if}}</span>
    </label>
</li>
</script>
<script id="item-add-tpl" type="text/html">
    <li>
        <form action="/collection/album/add" method="post" class="col-itemadd-form">
            <input type="hidden" name="collection_id" value="<?= $collection['id'] ?>">
            <div class="form-group">
                <!--<div class="col-item-image" data-toggle="modal" data-target="#crop-item-image-modal">
                    <input type="hidden" name="content" value=""/>
                    <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D"/>
                    <span class="btn btn-black btn-corner center-btn">设置图片</span>
                </div>
                <div>
                    <textarea rows="3" name="text" style="border:1px solid #E5E5E5;" placeholder="备注"></textarea>
                </div>-->
		<div><label for="text" style="width:100%;">备注<input name="text" style="float:right;width:86%;" value=""></label></div>
                <div><label for="text" style="width:100%;">排序<input name="position" style="float:right;width:86%;" value="" placeholder="0"></label></div>
                <div style="margin:9px auto 13px;">
                    <button type="submit" class="btn btn-black btn-nocorner" style="width:48%">
                        <i class="icon icon-save"></i>保存
                    </button>
                    <input type="button" value="X 取消" class="btn btn-winered btn-nocorner  pull-right item-cancel-btn" style="width:48%;" />
                </div>
                <div class="sel-relate-p">
                    <input type="hidden" value="" name="product_id"/>
                    <div class="rp-ctner">
                        <i class="icon icon-select"></i><span>选择商品</span>
                    </div>
                </div>
            </div>
        </form>
    </li>
</script>
<script type="text/javascript">
    function check()
    {
        var a = new Array();
        a[0] = $("#allpage").val();
        a[1] = $("#nowpage").val();
        if ( a[0] == '' || a[1] == '') {
            alert('请耐心等待商品信息加载后，再尝试！');
        }
        return a;
    }
    function checkfilter(f)
    {
        if(f[0] == '[]' && f[1] == '[]' && f[2] == '[]' && f[3] == '[]') {
            return true;
        } else {
            return true;
        }
    }
    $(function(){
        $('ul.ul-item').on('click','.sel-relate-p',function(){
            $("#gopage").val("");
            $('#sel-product-modal').modal('show', {backdrop: 'static'}).data('data-item', $(this));
            // $.post(CONFIG.product.filter_product, {'rt': 'json',of:0, lm: 12, cate:"[]", br:"[]", pr:"[]", dr:"[]",
            //     'userid': "<?=( $this->_data['usertype'] == USERTYPE_BUYER )?'':$responsible_userid?>"}, function (data) {
            //     if (parseInt(data.total) > 0) {
            //         var tpl = $.templates("#product-item-tpl");
            //         jQuery('#sel-product-modal .modal-body').html("<ul>"+tpl.render(data.list)+"</ul>");
            //         // 分页信息
            //         var ap = Math.ceil(data.total/12);
            //         $("#allpage").val(ap);
            //         $("#nowpage").val(0);
            //         $("#allnum").html(data.total);
            //         $("#pageall").html(ap);
            //         $("#pagenow").html(1);
            //     } else {
            //         jQuery('#sel-product-modal .modal-body').html("没有符合条件的商品！");
            //     }
            // }, 'json');
            return true;
        })
        $('ul.ul-item').on('click','.rp-del',function(e){
            $(this).parent().find('img').attr({'src':'','alt':''});
            var _parent = $(this).parents('div.sel-relate-p');
            _parent.find('input[name=product_id]').val('0');
            _parent.find('.rp-ctner').html($('<span>选择商品</span>'));
            return false;
        })
        $('.sel-relate-p').on('mouseenter', '.rp-ctner>.rp-content', function () {
            if ($(this).parents('.sel-relate-p').find('input[name=product_id]').val() > 0) {
                $(this).append($('<a class="glyphicon glyphicon-remove-circle rp-del" href=""></a>'));
            }
        });
        $('.sel-relate-p').on('mouseleave', '.rp-ctner>.rp-content', function () {
            $('a:last', this).remove();
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

        // 分页
        $('#page-btn1').click(function(){
            var a = check();
            if (a[1] <= 0) {
                alert('已经是第一页了！');
                return false;
            }
            var np = parseInt(a[1])-1;
            var filter = selectAll();
            if (checkfilter(filter)) {
                var url = '/product/filterProduct';
            } else {
                var url = CONFIG.product.my_list;
            }
	    var keywords = $("#keywords").val();
            $.post(url, {'rt': 'json', of:np*12, lm:12, cate:filter[0], br:filter[1], pr:filter[2], dr:filter[3]
                , 'userid': "<?=( $this->_data['usertype'] == USERTYPE_BUYER )?'':$responsible_userid?>", 'kw':keywords, st:filter[4], sr:filter[5]}, function (data) {
                if (parseInt(data.total) > 0) {
                    var tpl = $.templates("#product-item-tpl");
                    jQuery('#sel-product-modal .modal-body').html("<ul>"+tpl.render(data.list)+"</ul>");
                    // 分页信息
                    var ap = Math.ceil(data.total/12);
                    $("#allpage").val(ap);
                    $("#nowpage").val(np);
                    $("#allnum").html(data.total);
                    $("#pageall").html(ap);
                    $("#pagenow").html(parseInt(np)+1);
                } else {
                    jQuery('#sel-product-modal .modal-body').html("没有符合条件的商品！");
                }
            }, 'json');
            return false;
        });
        $('#page-btn2').click(function(){
            var a = check();
            if ((parseInt(a[0]) - parseInt(a[1])) <= 1) {
                alert('已经是最后一页了！');
                return false;
            }
            var np = parseInt(a[1])+1;
            var filter = selectAll();
            if (checkfilter(filter)) {
                var url = '/product/filterProduct';
            } else {
                var url = CONFIG.product.my_list;
            }
	    var keywords = $("#keywords").val();
            $.post(url, {'rt': 'json', of:np*12, lm: 12, cate:filter[0], br:filter[1], pr:filter[2], dr:filter[3],
             'userid': "<?=( $this->_data['usertype'] == USERTYPE_BUYER )?'':$responsible_userid?>", 'kw':keywords, st:filter[4], sr:filter[5]}, function (data) {
                if (parseInt(data.total) > 0) {
                    var tpl = $.templates("#product-item-tpl");
                    jQuery('#sel-product-modal .modal-body').html("<ul>"+tpl.render(data.list)+"</ul>");
                    // 分页信息
                    var ap = Math.ceil(data.total/12);
                    $("#allpage").val(ap);
                    $("#nowpage").val(np);
                    $("#allnum").html(data.total);
                    $("#pageall").html(ap);
                    $("#pagenow").html(parseInt(np)+1);
                } else {
                    jQuery('#sel-product-modal .modal-body').html("没有符合条件的商品！");
                }
            }, 'json');
            return false;
        });
        $('#page-btn3').click(function(){
            var a = check();
            var go = $("#gopage").val();
            if (go > a[0] || go <= 0) {
                alert('这一页不存在！');
                return false;
            }
            var filter = selectAll();
            if (checkfilter(filter)) {
                var url = '/product/filterProduct';
            } else {
                var url = CONFIG.product.my_list;
            }
	    var keywords = $("#keywords").val();
            $.post(url, {'rt': 'json', of:(parseInt(go)-1)*12, lm: 12, cate:filter[0], br:filter[1], pr:filter[2], dr:filter[3],
             'userid': "<?=( $this->_data['usertype'] == USERTYPE_BUYER )?'':$responsible_userid?>", 'kw':keywords, st:filter[4], sr:filter[5]}, function (data) {
                if (parseInt(data.total) > 0) {
                    var tpl = $.templates("#product-item-tpl");
                    jQuery('#sel-product-modal .modal-body').html("<ul>"+tpl.render(data.list)+"</ul>");
                    // 分页信息
                    var ap = Math.ceil(data.total/12);
                    $("#allpage").val(ap);
                    $("#nowpage").val(go);
                    $("#allnum").html(data.total);
                    $("#pageall").html(ap);
                    $("#pagenow").html(go);
                } else {
                    jQuery('#sel-product-modal .modal-body').html("没有符合条件的商品！");
                }
            }, 'json');
            return false;
        });
    });
</script>
