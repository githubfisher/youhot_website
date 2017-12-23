$(function(){
	// 选项卡效果
$(".count-word .count-s-word").each(function(index){
	this.index=index;
	$(this).click(function(){
		$(".count-users").css("display","none").eq(this.index).css("display","block")
	})
})





})