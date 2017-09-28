$('.discuss_essay_list_top_h').click(function(){
	$('.yinc').remove();
	var id=$(this).attr('reply');
	var html='<div class="discuss_essay_c_list yinc">'+
   	     	'<div class="discuss_essay_list_info">'+
   	     		'<textarea id="info_a_'+id+'"></textarea>'+
   	     		'<span onclick="tj_a('+a_id+','+id+')">提交</span>'+
   	     	'</div>'+
   	     '</div>';
	$(this).parent().parent().append(html);
});
function tj(a_id,id){//提交文章评论
    var info=$('#info').val();
	var data={
		info:info,//评论内容
		a_id:a_id,//文章id
		id:id//评论上级id
	};
	
	 $.post("http://"+host+"/admin/Reveal/Info",data,function(result){
              var obj = new Function("return" + result)();//转换后的JSON对象    
             if(obj.code==0){
              	$('#u_login').html(obj.mess);
              	$('.list_zezao').css('display','block');
              }
              if(obj.code==2){
              	$('#u_login').html(obj.mess);
              	$('.list_zezao').css('display','block');
              }
              if(obj.code==1){
              	location.reload(1);
              }          
   });	
}
function tj_a(a_id,id){//提交评论的回复
	var info=$('#info_a_'+id).val();
	var data={
		info:info,//评论内容
		a_id:a_id,//文章id
		id:id//评论上级id
	};
	
	 $.post("http://"+host+"/admin/Reveal/Info",data,function(result){
              var obj = new Function("return" + result)();//转换后的JSON对象    
            
              if(obj.code==0){
              	$('#u_login').html(obj.mess);
              	$('.list_zezao').css('display','block');
              }
              if(obj.code==2){
              	$('#u_login').html(obj.mess);
              	$('.list_zezao').css('display','block');
              }
              if(obj.code==1){
              	location.reload(1);
              }
   });
}
$('.cols span').click(function(){
	$('.list_zezao').css('display','none');
});
