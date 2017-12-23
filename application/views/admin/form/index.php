<?php
$this->layout->load_css("/admin/modules/datatables/datatables.min.css");
$this->layout->load_js('admin/modules/datatables/datatables.js');
$this->layout->load_js('admin/plugins/bootstrap/js/bootstrap-confirmation.js');
$this->layout->placeholder('title', '报表导出');
?>
<style>
    .container .row{
        width:84%;
    }
    .col-md-offset-01{
        margin-left: 2%;
    }
</style>
<div class="m-center-right" style="height:auto">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>报表导出</h3></div>
        <div class="col-sm-4" style="margin-top:30px;float:right;">
	    <span class="text-right">
		<span style="line-height:2.4;">
		    年份：
		    <select id="year" style="width:60px;">
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                        <option value="2020">2020</option>
                    </select>
		    月份：
		    <select id="month" style="width:40px;" >
			<option value="01">01</option>
			<option value="02">02</option>
			<option value="03">03</option>
			<option value="04">04</option>
			<option value="05">05</option>
			<option value="06">06</option>
			<option value="07">07</option>
			<option value="08" selected="selected">08</option>
			<option value="09">09</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="12">12</option>
		    </select>
		</span>
		<a id="export" href="/admin/form/export?date=2017-08" class="product-add btn btn-purple pull-right" >导出</a>
	    </span>
	</div>
    </div>
    <div class="m-content">
        <table id="list-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
        </table>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-01">
        </div>
    </div>
</div>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
    url = "/admin/form/export?date=";
    $("#year,#month").change(function() {
	$("#export").attr('href', url+$("#year").val()+'-'+$("#month").val());	
    });
});
function sub()
{
    url = '/admin/referer/index?start=' + $("#start-date").val() + '&end=' + $("#end-date").val();
    window.location.href = url;
}
</script>
