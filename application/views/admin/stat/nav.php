<style type="text/css">
    .nav-on{border-bottom:2px solid #000}
</style>
<div class="count-word">
    <div class="count-s-word <?=$nav_current=='product'?'nav-on':''?>"> <a href="/admin/stat" >商品浏览统计</a></div>
    <div class="count-s-word <?=$nav_current=='collection'?'nav-on':''?>"> <a href="/admin/stat/collection" >专辑统计</a></div>
    <div class="count-s-word <?=$nav_current=='user'?'nav-on':''?>"> <a href="/admin/stat/user" onclick="javascript:alert('未实现');return false;">用户统计</a></div>
    <div class="count-s-word <?=$nav_current=='order'?'nav-on':''?>" > <a href="/admin/stat/order" >订单统计</a></div>
</div>
