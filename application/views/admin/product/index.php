<?php
//$this->layout->load_js('admin/plugins/jquery-validation/js/jquery.validate.min.js');
//$this->layout->load_js('admin//plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js');
$this->layout->load_js('admin/js/form-validation.js');
?>
<section class="wrapper" style='margin-top:60px;display:inline-block;width:100%;padding:15px 0 0 15px;'
         ng-app="product_module">

    <div class="col-lg-12">
        <section class="box ">
            <header class="panel_header">
                <h2 class="title pull-left">发布商品</h2>

            </header>
            <div class="content-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">

                        <form id="commentForm" name="product_form" ng-controller="product_controller">


                            <div class="form-group">

                                <div class="controls">
                                    <input type="text" placeholder="商品名称" class="form-control" name="title" id="txtitle"
                                           ng-model="product.title" required maxlength="10">

                                    <div ng-show="product_form.$submitted || product_form.title.$touched">
                                        <span ng-show="product_form.title.$error.required">请填写商品名称</span>
                                    </div>
                                </div>
                            </div>

                            <ul class="pager wizard">
                                <li class="next"><a href="javascript:;" ng-click="create_product(product_form)">创建</a>
                                </li>

                            </ul>


                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

</section>
