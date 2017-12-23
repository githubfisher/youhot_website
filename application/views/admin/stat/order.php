<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/1/28
 * Time: 下午3:54
 */

$this->layout->load_js('admin/modules/highcharts/highcharts.js');
$this->layout->load_js('admin/modules/highcharts/modules/exporting.js');

$this->layout->load_css("admin/css/count.css");
?>
<style>
    .count-m button.active{
        background:#0B97D4;
        border:none;


    }
    .count-m button{width:50px;height:50px;font-size:12px;}
</style>
<!-- 顶部开始 -->
<div class="m-center-right" style="float:left;padding-bottom:30px">
    <div class="count-box">
        <?php $this->load->view('admin/stat/nav',array('nav_current'=>'order'));?>

        <!-- 表单部分内容 -->

        <div class="count-big-box">

            <!-- 商品浏览统计第一栏 -->
            <div class="count-qiehuan  count-users" style="display:block;background:#fff">
                <div class="count-select">
                    <div class="count-top" style="border-bottom:1px solid #B3B3B3;">
                        <form action="" id="count-form">
                            订单时间： 从
                            <input type="date" name="s"> 至 <input type="date" name="e">
                            <input type="submit" value="确定">
                        </form>
                    </div>

                    <div class="count-top count-m">
                        周期：
                        <button id="btn-day" class="active to-day">按天</button>
                        <button id="btn-month" class="">按月</button>
                    </div>
                </div>
                <!-- 表格部分开始 -->


                <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

            </div>


        </div>

    </div>
</div>




<script>

    var stat_data = <?=json_encode($stat_data)?>;
//{"day":{"columns":["2016-01-22","2016-01-23","2016-01-24","2016-01-25","2016-01-26","2016-01-27","2016-01-28","2016-01-29"],"series":[{"name":"init","data":[0,0,0,2,0,0,3,0]},{"name":"complete","data":[9,0,0,8,0,0,0,0]}]},"month":{"columns":["2016-01"],"series":[{"name":"init","data":[5]},{"name":"complete","data":[17]}]}};
    //参数初始化
    var date_begin = get_param_value('s');
    var date_end = get_param_value('e');
    if(date_begin == null) date_begin = "";
    if(date_end == null) date_end= "";

    $("input[name=s]").val(date_begin);
    $("input[name=e]").val(date_end);


//    var g_source_url = "/admin_api/get_performance_list?p="+follower+"&begin="+date_begin+"&end="+date_end;



    //修改时间刷新数据

    //获取url参数值
    function get_param_value(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]); return null;
    }
    $(function () {
        $('#container').highcharts({
            chart:{
                type:'area'
            },
            title: {
                text: '订单统计'
            },
            subtitle: {
                text: '按天'
            },

            xAxis: {
                categories: <?=json_encode($stat_data['day']['columns'])?>,
                tickmarkPlacement: 'on',
                title: {
                    enabled: false
                }
            },
            yAxis: {
                title: {
                    text: '个'
                },
                labels: {
                    formatter: function () {
                        return this.value;
                    }
                }
            },
            tooltip: {
                shared: true,
                valueSuffix: ' 个'
            },
            plotOptions: {
                area: {
                    stacking: 'normal',
                    lineColor: '#666666',
                    lineWidth: 1,
                    marker: {
                        lineWidth: 1,
                        lineColor: '#666666'
                    }
                }
            },
            series: <?=json_encode(array_values($stat_data['day']['series']))?>
        });

        $('#btn-day').on('click',function(e){
            $(this).addClass('active').siblings().removeClass('active');
            var chart = $('#container').highcharts();
            chart.setTitle({},{'text':'按天'});
            chart.series[0].setData(stat_data['day']['series'][0]['data']);
            chart.series[1].setData(stat_data['day']['series'][1]['data']);
            chart.xAxis[0].setCategories(stat_data['day']['columns']);
        });
        $('#btn-month').on('click',function(e){
            $(this).addClass('active').siblings().removeClass('active');
            var chart = $('#container').highcharts();
            chart.setTitle({},{'text':'按月'});

            chart.series[0].setData(stat_data['month']['series'][0]['data']);
            chart.series[1].setData(stat_data['month']['series'][1]['data']);
            chart.xAxis[0].setCategories(stat_data['month']['columns']);

        });
    });
</script>
