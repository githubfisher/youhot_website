<div class="column">
<div class="column-title"><span>提示信息..</span></div>
<div class="column-content">
           
                   <div class="sys_msg">
								<div class="sys_msg_con"><?php echo $msg; ?></div>
                                
                                <?php if($auto): ?>
                                <script>
                                    function redirect($url)
                                    {
                                        location = $url;	
                                    }
                                    setTimeout("redirect('<?php echo $goto; ?>');", 5000);
                                    
                                    //todo 倒计时
                                </script>
                                <a href="<?php echo $goto; ?>" style="text-decoration:underline">页面正在自动转向，你也可以点此直接跳转！</a>
                                
                                <?php endif; ?>
              </div>
              
</div>
</div>


 			