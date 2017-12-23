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
    .message{
       /* border-left: 2px solid #3c3c3c;
        padding-left: 7px;
        line-height: 10px;
        margin-top: 17px;
        margin-bottom: 10px;
        font-weight:bold;
        font-size:14px;
    }
    .line{
        height: 1px;
        border-top: 1px solid #e8e8e8;
        margin: 14px -23px 18px;
    }
    .dingdan-middle .row{font-size:12px;margin-top:15px}
</style>
<div class="m-center-right">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>管理售后单</h3></div>
    </div>
    <div class="m-content" style="">
        <section class="wrapper" style='width:100%;'>
            <div class="col-lg-12">
                <section class="box ">
                    <div class=" tab-pane fade in active" id="home-2">
                        <form id="product_form" name="product_form" class="form-horizontal">
                            <input type="hidden" id="id" value="<?= $order['id'] ?>"/>
                            <input type="hidden" id="type_id" value="<?= $order['type'] ?>"/>
                            <input type="hidden" id="status_id" value="<?= $order['status'] ?>"/>
                            <input type="hidden" name="rt" value="json"/>
                            <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">类型</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="type" id="type" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="0">退货</option>
                                            <option value="1">退款</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">订单号</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="订单号" style="" class="form-control" name="order_id"
                                               id="order_id" value="<?= $order['order_id'] ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">退款金额</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="显示名称" style="" class="form-control" name="price"
                                               id="price" value="<?= $order['price'] ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">用户ID</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="用户" style="" class="form-control" name="userid"
                                               id="userid" value="<?= $order['userid'] ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">用户信息</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <input type="text" placeholder="用户" style="" class="form-control" name="username"
                                               id="username" value="<?= $order['username'].' / '.$order['nickname'] ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">图片</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <img src="<?= $order['img'] ?>">
                                    </div>
                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">理由</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <textarea id="reason"> <?= $order['reason'] ?> </textarea>
                                    </div>
                                </div>
                            </div>

                            <!--分类一栏-->
                            <div class="form-group clearfix">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-2 require" for="field-1">操作</label>
                                    <div class="col-sm-8 col-md-8 col-xs-8">
                                        <select name="status" id="status" class="form-control" data-pid="" aria-invalid="false">
                                            <option value="4">同意退货</option>
                                            <option value="3">同意退款</option>
                                            <option value="2">拒绝</option>
                                            <option value="1">完成</option>
                                            <option value="0">待处理</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <!--商品名称一栏-->
                            <div class="form-group">
                                <div class="controls">
                                    <label class="control-label col-sm-2 col-md-2 col-xs-12 require" for="field-1">回复</label>
                                    <div class="col-sm-8 col-md-8 col-xs-12">
                                        <textarea id="reply"> <?= $order['reply'] ?> </textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group  clearfix">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <button type="button" class="btn btn-purple" style="width:8em" onclick="sub();">保存</button>
				    <button type="button" class="btn btn-purple" style="width:8em" onclick="bak();">返回</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </section>

        <div class="line"></div>
        <div class="message">退款（货）商品信息</div>
        <table class="table-box" cellspacing=0;cellpadding=0; width="100%">
            <tr style="background:#F4F5F9;height:40px;border:1px solid #ccc">
                <td width="20%">商品</td>
                <td width="15%">ID</td>
                <td width="15%">颜色</td>
                <td width="15%">尺码</td>
                <td width="15%">单价</td>
                <td width="12%">数量</td>
                <td width="18%">状态</td>
            </tr>
            <?php foreach($order['products'] as $od) { ?>
                <tr id="tdbox">
                    <td>
                        <a href="<?= $od['m_url'] ?>" target="_blank"><img src="<?= $od['cover_image'] ?>" width="50px"></a>
                    </td>
                    <td><?= $od['id'] ?></td>
                    <td><span><?= $od['color'] ?></span></td>
                    <td><span><?= $od['size'] ?></span></td>
                    <td><?= (int)$od['price'] ?></td>
                    <td><?= $od['sum'] ?></td>
                    <?php if ($od['status'] == 1) { ?>
                        <td>上架</td>
                    <?php } else { ?>
                        <td>下架</td>
                    <?php } ?>
                </tr>
            <?php } ?>

        </table>
    </div>
</div>
<script>
    function bak()
    {
        window.history.back();
    }
    function sub()
    {
        var id = $("#id").val();
        var order_id = $("#order_id").val();
        var price = $("#price").val();
        var reason = $("#reason").val();
        var status = $("#status").val();
        var type = $("#type").val();
        var reply = $("#reply").val();
        var update_url = '/admin/returnback/update';
        $.post(update_url,{id,order_id,price,reason,status,type,reply},function (data) {
            if (data.res == 0) {
                showSuccess();
            } else {
                showErrorMessage(data.hint);
            }
        }, 'json');
    }
    $(function () {
        var type = $("#type_id").val();        
        var status = $("#status_id").val();
        $("#type").val(type);
        $("#status").val(status);
    })
</script>

