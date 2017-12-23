
<style>
    table.dataTable th {
        text-align: left;
    }

    .editable {
        min-height: 24px;
        min-width: 30px;
        border: 1px dashed #efefef;
        cursor: pointer;
    }

    .to-extend {
        height: 75px;
        overflow: hidden;
        cursor: pointer;
        text-overflow: ellipsis;
    }

    .img-thumbnail {
        margin-right: 16px
    }

    .dt-head-center.btn-link {
        color: #1E1E1E
    }

    .dt-head-center {
        width: 50%;
    }

    .dt-head-center a {
        color: #1E1E1E !important;
        font-size: 12px;
    }

    .product-delete {
        color: #EC1379;
    }

    .product-publish, .glyphicon-class {
        color: #0B97D4
    }

    div.dataTables_wrapper div div.dataTables_filter {
        text-align: left;
    }

    #userfile{
    	display:none;
    }
    #sub-label{
    	display:none;
    }
    .upload-info{
        margin:0 auto;
        width:50%;
        height:100%;
        text-align: center;
        padding-bottom: 24%;
        padding-top: 10%;
    }
    .info-win{
        width:100%;
        text-align: center;
    }
</style>
<div class="m-center-right" style="height:auto">
    <div class="m-center-top">
        <div class="col-sm-4"><h3>码图管理</h3></div>
    </div>
    <div class="m-content">
        <div class="upload-info">
            <div class="info-win">
                <h3><?= $info ?></h3>
            </div>
            <div class="col-sm-7">
                <h3 class="text-right">
                    <label for="guan" class="product-add btn btn-purple pull-right">
                        <a href="<?=$this->config->item('url')['admin_size']?>" name="guan" style="color: rgba(255,255,255,1);">返回管理</a>
                    </label>
                </h3>
            </div>
        </div>
    </div>
</div>