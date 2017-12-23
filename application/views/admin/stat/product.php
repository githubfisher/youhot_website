<?php
$this->layout->load_css("admin/css/count.css");
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
?>

<style>
    tbody.row>td:last-child{color:red;}
    tbody>tr>.dt-head-center>a{font-size:12px;}
    tbody>tr>.dt-center{font-size:12px;}
</style>
<!-- 顶部开始 -->
<div class="m-center-right" style="float:left;padding-bottom:30px">
    <div class="count-box">

        <?php $this->load->view('admin/stat/nav',array('nav_current'=>'product'));?>

        <!-- 表单部分内容 -->

        <div class="count-big-box">

            <!-- 商品浏览统计第一栏 -->
            <div class="count-qiehuan  count-users" style="display:block;background:#fff">
<!--
                <!-- 表格部分开始 -->
                <table id="list-table" class="table table-striped table-bordered" cellspacing="0" width="100%">

                </table>

            </div>
            <!-- 商品浏览统计 -->
        </div>
    </div>
</div>

<script>

    function format_pstatus(status) {
        var str = '未知';
        switch (status) {
            case "<?=PRODUCT_STATUS_DRAFT?>":
                str = "待上架";
                break;
            case "<?=PRODUCT_STATUS_PUBLISHED?>":
                str = "已上架";
                break;

            default:
                break;
        }
        return str;
    }
    $(document).ready(function () {
        var g_source_url = CONFIG.product.my_list + "?rt=json&userid=<?=$responsible_userid?>";

        var oTable = $('#list-table').DataTable({
            "processing": true,
//            "serverSide": true,   //@todo 多的时候需要实现翻页
            "pageLength": 20,
            "dom":
            "<'row'<'col-sm-6'f><'col-sm-6'l>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "lengthChange": false,
            "ajax": {
                "url": g_source_url
                , "dataSrc": "list"
                , "data": {
                    "lm": 200
                }
            },
            "columns": [
                {"data": "id", 'title': 'id'},
                {"data": null, 'title': '商品描述', className: "product-desc-td dt-head-center",width:"200px"},
                {"data": "view_count", 'title': '浏览数', width: "50px", className: "dt-center"},
                {"data": "presold_count", 'title': '预定数', width: "50px", className: "dt-center"},
                {"data": "status", 'title': '状态', width: "70px", className: "dt-center"},
            ],
            "order": [[0, "desc"]],
            "columnDefs": [
//username add link
                {
                    'render': function (data, type, row) {
                        var str = "<input type=checkbox name='pid' value='" + data.id + "'>";
                        str += '<a href="' + CONFIG.product.preview_url + '?product_id=' + data.id + '"  target="_blank" class="btn btn-link">';
                        if (data.cover_image != null) {
                            str += '<img class="img-thumbnail" src="' + data.cover_image + '@50h_1wh" >';
                        }
                        str += data.title;

                        str += '</a>';

                        return str;
                    },
                    "targets": [1]
                },

                {
                    'render': function (data, type, row) {
                        str = format_pstatus(data);
                        return str;
                    },
                    "targets": [4]
                },

                {
                    "visible": false, "targets": [0]
                }


            ],
            "language": {
                "zeroRecords": "暂无数据",
                "info": " 记录：_START_ - _END_ ，共 _TOTAL_ 条 ",
                "infoEmpty": "搜索无结果",
                "infoFiltered": "( 共 _MAX_ 条)",
                "search": "",
                "sSearchPlaceholder":"商品名称"
            },

        });
    });

</script>
