function next_recom(url,end_id){

	var idc=$('#ensys_'+end_id).length;
	
	if(!idc){//判断是否存在该翻页
	 $.get("http://"+host+"/admin/System/"+url,function(result){
              var obj = new Function("return" + result)();//转换后的JSON对象  
              if(obj.cod){
              	   var tl= $('.system_cent_cr_calss table').length;
              	    
              	    var str='';
              	    for(i in obj['data']){
              	    	str=str+'<tr id=""><td>'+obj['data'][i]['id']+'</td><td id="re_name_'+obj['data'][i]['id']+'">'+obj['data'][i]['name']+'</td><td id="re_synopsis_'+obj['data'][i]['id']+'">'+obj['data'][i]['synopsis']+'</td><td><span onclick="edit_recom('+obj['data'][i]['id']+')">操作</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span onclick="edit_del('+obj['data'][i]['id']+')">删除</span></td></tr>';
              	    }
              	    var tb='<table id="ensys_'+obj['end_id']+'">'+
						'<tr><th style="width: 50px;">ID</th><th style="width: 150px;">简称</th><th style="width: 400px;">简介</th><th style="width: 80px;">操作</th></tr>'+
						
						str+
						
					'<tr><td style="text-align: center;" colspan="4"><span onclick="fornext('+tl+')">上一页</span><span   onclick="next_recom(\'Recommend_next?end_id='+obj['next']+'\','+obj['next']+')">下一页</span></td></tr>'
					'</table>';
				$('.system_cent_cr_calss table').css('display','none');	
				$('.system_cent_cr_calss').append(tb);	
              }else{
              	alert(obj.data);
              }
              
              
   });
  }else{
  	$('.system_cent_cr_calss table').css('display','none');
  	$('#ensys_'+end_id).css('display','block');
  }
}

function fornext(sid){//上一页
	var tb=$('.system_cent_cr_calss table');
	tb.css('display','none');
	$(tb[(sid-1)]).css('display','block');
}

function edit_recom(id){//编辑显示
	
	var data={
		id:id
	};
	
	 $.post("http://"+host+"/admin/System/Recommend_find",data,function(result){
              var obj = new Function("return" + result)();//转换后的JSON对象          
              
	 	      if(obj.cod){
	 	      	$('#re_idc').html(obj['data'].id);
	 	      	$('#name').val(obj['data'].name);
	 	      	$('#synopsis').val(obj['data'].synopsis);
	 	      	$('#re_id').val(obj['data'].id);
	 	      	$('.zezao_re').css('display','block');
	 	      }else{
	 	      	alert(obj.data);
	 	      }
	 	   });
}

function clos_re(){
	$('.zezao_re').css('display','none');
}
function re_eit(){
	var id=$('#re_id').val();
	var name=$('#name').val();
	var synopsis=$('#synopsis').val();
	var data={
		id:id,
		name:name,
		synopsis:synopsis
	};
	
	 $.post("http://"+host+"/admin/System/Recommend_rew",data,function(result){
              var obj = new Function("return" + result)();//转换后的JSON对象          
              console.log(obj);
	 	      if(obj.cod){
	 	      	$('#re_name_'+obj.data).html(name);
	 	      	$('#re_synopsis_'+obj.data).html(synopsis);
	 	      	clos_re();
	 	      }else{
	 	      	alert(obj.data);
	 	      }
	 	   });
}

function re_add(){
	$('#re_add_zezao').css('display','block');
}
function re_add_clos(){
	$('#re_add_zezao').css('display','none');
}
function edit_del(id){
	var data={
		id:id
	};
	 $.post("http://"+host+"/admin/System/Recommend_del",data,function(result){
              var obj = new Function("return" + result)();//转换后的JSON对象                      
	 	      if(obj.cod){
	 	      	alert(obj.data);
	 	      	$('#re_name_'+id).parent().remove();	      
	 	      }else{
	 	      	alert(obj.data);
	 	      }
	 	   });
}

