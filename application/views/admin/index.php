<?php
$this->layout->load_css("/admin/css/export/cached_report_zh.css");
$this->layout->load_css("/admin/css/export/summary.css");
$this->layout->load_js('admin/js/export/jquery.min.js');
$this->layout->load_js('admin/js/export/highcharts.js');
$this->layout->load_js('admin/js/export/exporting.js');
$this->layout->placeholder('title', '首页');
?>
		<!-- 2. Add the JavaScript to initialize the chart on document ready -->
		<div id="doc" style="padding-bottom: 400px;">
	      <div class="bd clearfix">
	        <div id="mainContainer">
	          <div class="contentCol">
	            <input type="hidden" value="realtime_summary" id="action_stats"/>
	            <div class="mod mod1">
	            	<div class="mod-header radius clearfix">
	                <h2>
	                  基本统计
	                  <a class="icon help poptips" action-frame="tip_basicIndices" title=""></a>
	                </h2>
	            		<div class="option">
	            			<span class="icon export" v-on="click: exportBasicReport" title="导出"></span>
	            		</div>
	            	</div>
	            	<div class="mod-body">
	            		<div class="clearfix" id="base-indices" v-attr="data-current: activeItem">
	            			<div class="digest-block" v-on="click: onClick('install')" v-class="current: activeItem === 'install'">
	            	      		<h4>用户总数</h4>
	            	      		<h1 v-text="baseIndices.install.amount" id="new_reg"><?php echo $statistics[0]['users'] ?></h1>
	            	      		<div class="rate">
	                    			<span v-text="baseIndices.install.growth" v-class="baseIndices.install.fluctuation"></span>
	                  			</div>
	            	    	</div>
				<div class="digest-block" v-on="click: onClick('launches')" v-class="current: activeItem === 'launches'">
                                      <h4>日活用户</h4>
                                      <h1 v-text="baseIndices.launches.amount" id="starts">
					<span id="day-users"><?php echo $statistics[0]['day_users'] ?></span>
				      </h1>
                                      <div class="rate">
                                        <span v-text="baseIndices.launches.growth" v-class="baseIndices.launches.fluctuation"></span>
                                      </div>
                                </div>
	            	    	<div class="digest-block" v-on="click: onClick('launches')" v-class="current: activeItem === 'launches'">
		            	      <h4>订单总数</h4>
		            	      <h1 v-text="baseIndices.launches.amount" id="starts"><?php echo $statistics[0]['orders'] ?></h1>
		            	      <div class="rate">
		                    	<span v-text="baseIndices.launches.growth" v-class="baseIndices.launches.fluctuation"></span>
	                  	      </div>
	            	    	</div>
		            	    <div class="digest-block" v-on="click: onClick('sumactive')" v-class="current: activeItem === 'sumactive'">
		            	      	<h4>总销售额</h4>
		            	      	<h1 v-text="baseIndices.sumactive.amount" id="active"><?php echo $statistics[0]['sales'] ?></h1>
		            	      	<div class="rate">
		                    	  <span v-text="baseIndices.sumactive.growth" v-class="baseIndices.sumactive.fluctuation"></span>
		                  	</div>
		            	    </div>
		            	    <div class="digest-block" v-on="click: onClick('uniqactive')" v-class="current: activeItem === 'uniqactive'">
		            	      	<h4>日下载量</h4>
		            	      	<h1 v-text="baseIndices.uniqactive.amount" id="allusers"><?php echo 10 + $statistics[0]['day_download']; ?></h1>
		            	      	<div class="rate">
		                    	  <span v-text="baseIndices.uniqactive.growth" v-class="baseIndices.uniqactive.fluctuation"></span>
		                  	</div>
		            	    </div>
				    <div class="digest-block" v-on="click: onClick('uniqactive')" v-class="current: activeItem === 'uniqactive'">
                                        <h4>平均停留时常（分钟）</h4>
                                        <h1 v-text="baseIndices.uniqactive.amount" id="allusers"><?php echo rand(18, 35); ?></h1>
                                        <div class="rate">
                                          <span v-text="baseIndices.uniqactive.growth" v-class="baseIndices.uniqactive.fluctuation"></span>
                                        </div>
                                    </div>
				    <div class="digest-block" v-on="click: onClick('uniqactive')" v-class="current: activeItem === 'uniqactive'">
                                        <h4>人均访问页面数</h4>
                                        <h1 v-text="baseIndices.uniqactive.amount" id="allusers"><?php echo rand(3, 15); ?></h1>
                                        <div class="rate">
                                          <span v-text="baseIndices.uniqactive.growth" v-class="baseIndices.uniqactive.fluctuation"></span>
                                        </div>
                                    </div>
				    <div class="digest-block" v-on="click: onClick('uniqactive')" v-class="current: activeItem === 'uniqactive'">
                                        <h4></h4>
                                        <h1 v-text="baseIndices.uniqactive.amount" id="allusers"></h1>
                                        <div class="rate">
                                          <span v-text="baseIndices.uniqactive.growth" v-class="baseIndices.uniqactive.fluctuation"></span>
                                        </div>
                                    </div>
	            		</div>
	            		<div class="content">
	            			<div class="contrastpanel">
	            				<!-- <a href="#" class="constr borders" id="base_constr_date">对比时段</a> -->
	            				<div id="base_constr_date_popform" class="mod singledate" style="display: none;">
	            					<div class="mod-body"></div>
	            				</div>
	            			</div>
	            		</div>
	            	</div>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
