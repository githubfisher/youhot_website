<?php
$this->layout->load_js('admin/plugins/jquery-validation/js/jquery.validate.js');
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
$this->layout->load_js('admin/modules/jsrender/jsrender.min.js');
$this->layout->placeholder('title', '商品搜索');
?>
<style>
    .container .row{
        width:84%;
    }
    .col-md-offset-01{
        margin-left: 2%;
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
        padding-top: 30px;
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
    #brand-more, #store-more{
        float: right;
        padding-right: 20px;

    }
    .br-more, .st-more{
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
    
    function showPic(sUrl,event){
        var x,y;
        x = event.clientX;
        y = event.clientY;
        document.getElementById("www_zzjs_net").style.left = x;
        document.getElementById("www_zzjs_net").style.top = y;
        document.getElementById("www_zzjs_net").innerHTML = "<img src=\"" + sUrl + "\">";
        document.getElementById("www_zzjs_net").style.display = "block";
    }
    function hiddenPic(){
        document.getElementById("www_zzjs_net").innerHTML = "";
        document.getElementById("www_zzjs_net").style.display = "none";
    }
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
</script>
<div class="m-center-right" style="height:auto">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>商品搜索</h3></div>
        <div class="col-sm-8">
            <h3 class="text-right">
                <a href="javascript:void(0);" class="product-add btn btn-purple pull-right " onclick="seletor();">搜索 + 筛选</a>
            </h3>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-01">
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
                    <button onclick="clearAll('c','b','p','d','s','o');">清空</button>
                    <button onclick="submitAll();">提交</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="sel-product-modal" style="float:right;">
        <div class="col-md-12 col-md-offset-01">
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
            </div>
        </div>
    </div>
</div>
<script id="product-item-tpl" type="text/x-jsrender">
    <tr onmouseout="hiddenPic();" onmousemove="showPic('{{:cover_image}}', event);">
        <td>{{:id}}</td>
        <td><img src="{{:cover_image}}" width="50px" height="50px"></td>
        <td>
        {{if title.length > 40}}
                <a href="{{:m_url}}" target="_blank">{{:title.substring(0,40)}}</a>
        {{else}}
                <a href="{{:m_url}}" target="_blank">{{:title}}</a>
        {{/if}}
        </td>
        <td>￥{{:price}}</td>
        <td>{{:discount}}</td>
        {{if status == 1}}
            <td>已上架</td>
        {{else status == 0}}
            <td>待审核</td>
        {{else}}
            <td>已下架</td>
        {{/if}}
        <td>
            <table>
                <tr>
                <td width="15%"><a href="/admin/product/{{:id}}/edit">编辑</a></td>
		{{if is_commond == 1}}
		    <td width="15%"><a href="javascript:void(0);" onclick="commond('{{:id}}','0');">取消推荐</a></td>
		{{else}}		   	
		    <td width="15%"><a href="javascript:void(0);" onclick="commond('{{:id}}','1');">推荐</a></td>
		{{/if}}
                <td width="15%"><a href="/admin/order?product_id={{:id}}">订单</a></td>
<!--                
		{{if status == 1}}
                    <td width="15%"><a href="javascript:void(0);" onclick="unpublish('{{:id}}');">下架</a></td>
                {{else status == 0}}
                    <td width="15%"><a href="javascript:void(0);" onclick="sply('{{:id}}');">审核</a></td>
                {{else}}
                    <td width="15%"><a href="javascript:void(0);" onclick="publish('{{:id}}');">上架</a></td>
                {{/if}}
-->
                <td width="15%"><a href="javascript:void(0);" id="del-btn{{:id}}" style="color:red;" onclick="delete_tip('{{:id}}');">删除</a></td>
                <div class="popover fade left in" id="confirmation{{:id}}" style="top: 50px; left: 782.383px; display: none;">    <div class="arrow" style="top: 50%;"></div>
                    <h3 class="popover-title">确定删除该商品?</h3>
                    <div class="popover-content">
                        <a data-apply="confirmation" class="btn-purple btn btn-sm" href="javascript:void(0);" target="_self" onclick="delete('{{:id}}');"><i class="glyphicon glyphicon-ok"></i> 确认</a>
                        <a data-dismiss="confirmation" class="btn btn-sm btn-default" onclick="delete_untip('{{:id}}');"><i class="glyphicon glyphicon-remove"></i> 取消</a>
                    </div>
                </div>
            </tr>
        </table>
    </td>
    </tr>
</script>
<script>
    function commond(id,value)
    {
	var url = '/product/commond_product';
	var data = '{"is_commond":'+value+'}';
	$.post(url,{id:id,data:data},function(data){
	    if (data.res == 0) {
		showSuccess();	
	    } else {
		showErrorMessage(data.hint);
	    }
	},'json');
    }
    function submitAll()
    {
        var filter = selectAll();
        var keywords = $("#keywords").val();
        var url = '/product/filterProduct';
        $.post(url, {'rt': 'json',of:0, lm: 12, cate:filter[0], br:filter[1], pr:filter[2], dr:filter[3], 'kw':keywords, store:filter[4], sr:filter[5]}, function (data) {
            if (parseInt(data.total) > 0) {
                var tpl = $.templates("#product-item-tpl");
                var tab = '<div id="www_zzjs_net" style="display:none;position:fixed;z-index:1;left: 0px;bottom: 0px;"></div><table class="table text-center"><thead><tr style="font-weight:bold;"><td>ID</td><td>缩略图</td><td>商品标题</td><td>价格</td><td>折扣</td><td>状态</td><td>操作</td></tr></thead><tbody class=".table-striped">';
                var ble = '</tbody></table>';
                jQuery('#sel-product-modal .modal-body').html(tab+tpl.render(data.list)+ble);
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
        $("#keywords").val('');
	$("input:checkbox[value^='"+s+"']").attr('checked',false);
        $("input:checkbox[value^='"+o+"']").attr('checked',false);
        $("li[id^='"+s+"']").remove();
        $("li[id^='"+o+"']").remove();
        jQuery('#sel-product-modal .modal-body').html("请先选择筛选条件...");
    }
    function format_pstatus(status) {
        var str = '';
        var _class = "label-warning";
        switch (status) {
            case "<?=PRODUCT_STATUS_DRAFT?>":
                str = "草稿";
                _class = "label-info";
                break;
            case "<?=PRODUCT_STATUS_PUBLISHED?>":
                _class = "label-success";
                str = "已上架";
                break;
            case "<?=PRODUCT_STATUS_INAUDIT?>":
                str = "待审核";
                break;
            case "<?=PRODUCT_STATUS_OFFSHELF?>":
                str = "已下架";
                break;
            case "<?=PRODUCT_STATUS_AUDITREFUSE?>":
                str = "审核被拒";
                break;
            case "<?=PRODUCT_STATUS_DELETED?>":
                str = "已删除";
                break;
            default:
                str = '未知';
                _class = "label-warning";
                break;
        }
        return '<span>' + str + '</span>';
    }
    $(document).ready(function () {
        function _arrange_category(data) {
            var _ok_data = {};
            var hyphen = " - ";
            if ($.isArray(data)) {
                for (var i = 0, l = data.length; i < l; i++) {
                    var cat = data[i];
                    _ok_data[cat['id']] = cat['name'];
                    if ("sub_category" in cat) {
                        for (var key in cat['sub_category']) {
                            var _sub_cat = cat['sub_category'][key];
                            _ok_data[_sub_cat['id']] = cat['name'] + hyphen + _sub_cat['name'];
                        }
                    }
                }
            }
            return _ok_data;
        }

        function format_category(data) {
            return (data in category_meta) ? category_meta[data] : data;

        }

        var defaultConfig = {
            'submit': '确定',
            'cancel': '取消',
            'indicator': '提交中，请稍候...',
            'tooltip': '双击可进行编辑',
            'placeholder': '双击可进行编辑',
            'submitdata': {},
            'event': 'dblclick',
            'onblur': "ignore",
            ajaxoptions: {
                dataType: 'json'
            },
            'data': function (value, config) {
                var v = $('<p></p>').html(value).text();
                return v;
            },
            'onsubmit': function (config, element) {
                var key = ['', '', '', '', '', 'status'];

                $(element).data('v', $(':input:first', this).val());
                var index = (!$(element).is('td') ? $(element).parents('td').index() : $(element).index())
                //NOTE:调整位置可能会影响取值，需要注意


                config.submitdata['pid'] = oTable.fnGetData($(element).parents('tr').get(0))['pid'];
                config.submitdata['item'] = key[index];
                config.submitdata['v'] = $(':input:first', this).val();

                //增加一个扩展
                if (config.submitHandle && typeof(config.submitHandle) == 'function') {
                    config.submitHandle.apply(this, [config, element]);
                }
            },

            callback: function (value, settings) {
                var v = $('<p></p>').text($(this).data('v')).html().replace(/\n/g, '<br>\n');

                $(this).html(v);

                var td = this;
                if (!$(td).is('td')) {
                    td = $(td).parents('td').get(0);
                }

                if (value.res == 0) {
                    A.message.success();
                    var aPos = oTable.fnGetPosition(td);
                    oTable.fnUpdate($(this).data('v'), aPos[0], aPos[1]);
                } else {
                    A.message.error(value.hint);
                    $(this).html(this.revert);
                }
            }

        };


        $('#list-table').on('click', '.product-op', function (e) {
            var self = $(this);
            $.post(self.attr('href'), {rt: 'json', product_id: $(this).attr("data-pid"), presale_days: $(this).attr('data-presale-days')}, function (data) {
                if (data.res == 0) {
                    var hint;
                    if (self.attr('href') == CONFIG.product.publish_url) {
                        hint = '已上架';
                    } else if (self.attr('href') == CONFIG.product.unpublish_url) {
                        hint = '已下架';
                    } else if (self.attr('href') == CONFIG.product.audit_url) {
                        hint = '已提交审核';
                    }
                    self.parent('td').prev().html(hint);
                    self.fadeOut();
                    showSuccess();
                } else {
                    showErrorMessage(data.hint);
                }

            }, 'json');
            return false;

        });

        function op_del_product() {
            var self = this;
            $.post($(self).data('href'), {rt: 'json'}, function (data) {
                if (data.res == 0) {
                    showSuccess();
                    $(self).parents('tr').fadeOut();
                } else {
                    showErrorMessage(data.hint);
                }

            }, 'json');
            return false;
        }

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
            $.post(url, {'rt': 'json', of:np*12, lm:12, cate:filter[0], br:filter[1], pr:filter[2], dr:filter[3], 'kw':keywords, store:filter[4], sr:filter[5]}, function (data) {
                if (parseInt(data.total) > 0) {
                    var tpl = $.templates("#product-item-tpl");
                    var tab = '<div id="www_zzjs_net" style="display:none;position:fixed;z-index:1;left: 0px;bottom: 0px;"></div><table class="table text-center"><thead><tr style="font-weight:bold;"><td>ID</td><td>缩略图</td><td>商品标题</td><td>价格</td><td>折扣</td><td>状态</td><td>操作</td></tr></thead><tbody class=".table-striped">';
                    var ble = '</tbody></table>';
                    jQuery('#sel-product-modal .modal-body').html(tab+tpl.render(data.list)+ble);
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
            $.post(url, {'rt': 'json', of:np*12, lm: 12, cate:filter[0], br:filter[1], pr:filter[2], dr:filter[3], 'kw':keywords, store:filter[4], sr:filter[5]}, function (data) {
                if (parseInt(data.total) > 0) {
                    var tpl = $.templates("#product-item-tpl");
                    var tab = '<div id="www_zzjs_net" style="display:none;position:fixed;z-index:1;left: 0px;bottom: 0px;"></div><table class="table text-center"><thead><tr style="font-weight:bold;"><td>ID</td><td>缩略图</td><td>商品标题</td><td>价格</td><td>折扣</td><td>状态</td><td>操作</td></tr></thead><tbody class=".table-striped">';
                    var ble = '</tbody></table>';
                    jQuery('#sel-product-modal .modal-body').html(tab+tpl.render(data.list)+ble);
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
            var max = parseInt(a[0]);
            if (go > max || go <= 0) {
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
            $.post(url, {'rt': 'json', of:(parseInt(go)-1)*12, lm: 12, cate:filter[0], br:filter[1], pr:filter[2], dr:filter[3], 'kw':keywords, store:filter[4], sr:filter[5]}, function (data) {
                if (parseInt(data.total) > 0) {
                    var tpl = $.templates("#product-item-tpl");
                    var tab = '<div id="www_zzjs_net" style="display:none;position:fixed;z-index:1;left: 0px;bottom: 0px;"></div><table class="table text-center"><thead><tr style="font-weight:bold;"><td>ID</td><td>缩略图</td><td>商品标题</td><td>价格</td><td>折扣</td><td>状态</td><td>操作</td></tr></thead><tbody class=".table-striped">';
                    var ble = '</tbody></table>';
                    jQuery('#sel-product-modal .modal-body').html(tab+tpl.render(data.list)+ble);
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

    // 写到这里了 ，加弹出框， 写ajax请求
    function delete_tip(id)
    {
        var top= $("#del-btn"+id).offset().top;   //获取div的高度
        var left = $("#del-btn"+id).offset().left;       //获取div的宽度
        var d_top = 274;
        if (window.screen.width >= 1920) {
            var d_left = 565;
        }  else if (window.screen.width >= 1280) {
            var d_left = 460;
        } else if (window.screen.width >= 1180) {
            var d_left = 440;
        } else {
            var d_left = 380;
        }
        var c_top = top-d_top;
        var c_left = left-d_left;
        $("#confirmation"+id).css("top", c_top+'px');
        $("#confirmation"+id).css("left", c_left+'px');
        $("#confirmation"+id).fadeIn(100);
    }
    function delete_untip(id)
    {
        $("#confirmation"+id).fadeOut(100);
    }
    function sply(id)
    {
        $.post("/admin/product/to_audit", {rt: 'json',product_id:id}, function (data) {
            if (data.res == 0) {
                showSuccess();
                $(self).parents('tr').fadeOut();
            } else {
                showErrorMessage(data.hint);
            }

        }, 'json');
        return false;
    }
    function unpublish(id)
    {
        $.post("/admin/product/unpublish", {rt: 'json',product_id:id}, function (data) {
            if (data.res == 0) {
                showSuccess();
                $(self).parents('tr').fadeOut();
            } else {
                showErrorMessage(data.hint);
            }

        }, 'json');
        return false;
    }
    function publish(id)
    {
        $.post("/admin/product/publish", {rt: 'json', product_id:id, presale_days:0}, function (data) {
            if (data.res == 0) {
                showSuccess();
                $(self).parents('tr').fadeOut();
            } else {
                showErrorMessage(data.hint);
            }

        }, 'json');
        return false;
    }

</script>
