
/////////////////栏目管理js////////////
$(".class_cent_c1_l ul li:first").css('background','#f9fad1');
$('#r_'+$(".class_cent_c1_l ul li:first").attr('i')).css('display','block');
$('.class_cent_c1_l ul li').click(function(){
	var id=$(this).attr('i');
	$('.class_cent_c1_rx').css('display','none');
	$('.class_cent_c1_l ul li').css('background','#ffffff');
	$(this).css('background','#f9fad1');
	$('#r_'+id).css('display','block');
});


function add_add(){
	$(".class_cent_c1_l ul li:first").css('background','#f9fad1');
$('#r_'+$(".class_cent_c1_l ul li:first").attr('i')).css('display','block');
$('.class_cent_c1_l ul li').click(function(){
	var id=$(this).attr('i');
	$('.class_cent_c1_rx').css('display','none');
	$('.class_cent_c1_l ul li').css('background','#ffffff');
	$(this).css('background','#f9fad1');
	$('#r_'+id).css('display','block');
});
}

function clos(){
	$('.zezao').css('display','none');
	$('.edit_cent').css('display','none');
}
function clos1(){
	$('.zezao1').css('display','none');
}
function edit(id){
	$('.zezao').css('display','block');
	$('#edit_'+id).css('display','block');
}
function edit_add(id){//点击添加按钮函数
	$('.zezao').css('display','block');
	$('#edit_add').css('display','block');
	//清空值
	$('#edit_add_name').val('');
	$('#edit_add_file').val('');
	$('#edit_add_tem').val('');
	
	$('#edit_add_pid').val(id);//将父级id赋值给隐藏表单
}
function tijiao(id){//修改
	var name=$('#edit_name_'+id).val();
	var files=$('#edit_file_'+id).val();
	var tem=$('#edit_tem_'+id).val();//
	var id=$('#edit_id_'+id).val();
	var c_tem=$('#edit_c_tem_'+id).val();
	var data={
		p_name:name,
		p_file:files,
		p_tem:tem,
		p_id:id,
		p_c_tem:c_tem
	};
	
	 $.post("http://"+host+"/admin/Index/Edit",data,function(result){
              var obj = new Function("return" + result)();//转换后的JSON对象             
	 	      if(obj.cod){
	 	      	$('#edit_name_'+id).val();
	            $('#edit_file_'+id).val(obj['data'].p_file);
	            $('#edit_tem_'+id).val(obj['data'].p_tem);
	            $('#edit_c_tem_'+id).val(obj['data'].p_c_tem);
	            $('#li_'+id).html(obj['data'].p_name);
	            
	            clos();
	 	      }else{
	 	      	alert('修改失败或者没有修改');
	 	      }
              
   });
}
function tijiao_add(){//添加
	var name=$('#edit_add_name').val();
	var files=$('#edit_add_file').val();
	var tem=$('#edit_add_tem').val();
	var c_tem=$('#edit_add_c_tem').val();
	var pid=$('#edit_add_pid').val();
	var data={
		a_name:name,
		a_file:files,
		a_tem:tem,
		a_c_tem:c_tem,
		a_pid:pid
	};
	
	 $.post("http://"+host+"/admin/Index/Add",data,function(result){
	 	
              var obj = new Function("return" + result)();//转换后的JSON对象             
	 	      if(obj.cod){
	 	      	if(obj['data'].rate==1){//
	 	      		var html=$('.class_cent_c1_l ul').html();
	 	      		html=html+'<li  i="'+obj['data'].id+'"><i id="li_'+obj['data'].id+'">'+obj['data'].a_name+'</i><span><em onclick="edit('+obj['data'].id+')">编辑</em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em onclick="dlet('+obj['data'].id+')">删除</em></span></li>';
	 	      		var hob=$(html);
	 	      		var len=hob.length;
	 	      		//新增对象前置
	 	      		var endob=hob[len-1];
	 	      		hob[len-1]=hob[len-2];
	 	      		hob[len-2]=hob[len-3];
	 	      		hob[len-3]=endob;
	 	      		
	 	      		$('.class_cent_c1_l ul').html(hob);
	 	      		add_add();//重新绑定函数
	 	      		
	 	      		
	 	      		var str_rr='<div class="class_cent_c1_rx" id="r_'+obj['data'].id+'">'+
					'<ul>'+
							'<li style="text-align: center;" onclick="edit_add('+obj['data'].id+')">+</li>'+
				  '</ul>'+
				'</div>';
				$(".class_cent_c1_r").append(str_rr);
	 	      		
	 	      		//创建修改窗口
	 	      		var strs='';
	 	      		var cstrs='';
	 	      		var pkv=1;
	 	      		var cpkv=1;
	 	      		for(i in obj['data']['tp_file']){
	 	      			if(obj['data']['tp_file'][i]==obj['data'].a_tem){
                        strs=strs+'<option value="'+obj['data']['tp_file'][i]+'"  selected = "selected">'+obj['data']['tp_file'][i]+'</option>';
                        pkv=0;
                       }else{
                       	strs=strs+'<option value="'+obj['data']['tp_file'][i]+'" >'+obj['data']['tp_file'][i]+'</option>';
                       }
                            }
	 	      		if(!pkv){
	 	      			strs=strs+'<option value="'+obj['data'].a_tem+'" >'+obj['data'].a_tem+'</option>';
	 	      		}
	 	      		
	 	      		
	 	      		for(i in obj['data']['tp_file']){
	 	      			if(obj['data']['tp_file'][i]==obj['data'].a_c_tem){
                        cstrs=cstrs+'<option value="'+obj['data']['tp_file'][i]+'"  selected = "selected">'+obj['data']['tp_file'][i]+'</option>';
                        cpkv=0;
                       }else{
                       	cstrs=cstrs+'<option value="'+obj['data']['tp_file'][i]+'" >'+obj['data']['tp_file'][i]+'</option>';
                       }
                            }
	 	      		if(!cpkv){
	 	      			cstrs=cstrs+'<option value="'+obj['data'].a_c_tem+'" >'+obj['data'].a_c_tem+'</option>';
	 	      		}
	 	      		
	 	      		var app='<div class="edit_cent" id="edit_'+obj['data'].id+'"><ul>'+
	 	      		'<li>名称：<input id="edit_name_'+obj['data'].id+'" type="text" value="'+obj['data'].a_name+'"/></li>'+
	 	      		'<li>静态页面存放文件夹：<input id="edit_file_'+obj['data'].id+'" type="text" value="'+obj['data'].a_file+'"/></li>'+
	 	      		'<li> 栏目或者分类模板：<select id="edit_tem_'+obj['data'].id+'">'+
				       	strs+
				     '</select></li>'+
				     '<li>内容页模板：<select id="edit_c_tem_'+obj['data'].id+'">'+
				       cstrs+
				     '</select></li>'+
	 	      		'<input id="edit_id_'+obj['data'].id+'" type="hidden" value="'+obj['data'].id+'" />'+
	 	      		'<li style="float: right;margin-right: 20px;"><input onclick="tijiao('+obj['data'].id+')" type="button" value="提交"/></li>'+
	 	      		'</ul></div>';
	 	      		
	 	      		$(".edit").append(app);
	 	      		
	 	      		clos();
	 	      	}else{//当添加子级分类时
	 	      			var html=$('#r_'+obj['data'].a_pid+' ul').html();
	 	      			html=html+'<li><i id="li_'+obj['data'].id+'">'+obj['data'].a_name+'</i><span><em onclick="edit('+obj['data'].id+')">编辑</em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em onclick="dlet('+obj['data'].id+')">删除</em></span></li>';
	 	      		
	 	      		var hob=$(html);
	 	      		var len=hob.length;
	 	      		//新增对象前置
	 	      		if(len>2){
	 	      		var endob=hob[len-1];
	 	      		hob[len-1]=hob[len-2];
	 	      		hob[len-2]=hob[len-3];
	 	      		hob[len-3]=endob;
	 	      		}else{
	 	      		var endob=hob[len-2];
	 	      		hob[len-2]=hob[len-1];
	 	      		hob[len-1]=endob;
	 	      		}
	 	      		
	 	      		$('#r_'+obj['data'].a_pid+' ul').html(hob);
	 	      		//创建修改窗口
	 	      		var strs='';
	 	      		var cstrs='';
	 	      		var pkv=1;
	 	      		var cpkv=1;
	 	      		for(i in obj['data']['tp_file']){
	 	      			if(obj['data']['tp_file'][i]==obj['data'].a_tem){
                        strs=strs+'<option value="'+obj['data']['tp_file'][i]+'"  selected = "selected">'+obj['data']['tp_file'][i]+'</option>';
                         pkv=0;
                       }else{
                       	strs=strs+'<option value="'+obj['data']['tp_file'][i]+'" >'+obj['data']['tp_file'][i]+'</option>';
                       }
                            }
	 	      		
	 	      		if(!pkv){
	 	      			strs=strs+'<option value="'+obj['data'].a_tem+'" >'+obj['data'].a_tem+'</option>';
	 	      		}
	 	      		
	 	      		
	 	      		for(i in obj['data']['tp_file']){
	 	      			if(obj['data']['tp_file'][i]==obj['data'].a_c_tem){
                        cstrs=cstrs+'<option value="'+obj['data']['tp_file'][i]+'"  selected = "selected">'+obj['data']['tp_file'][i]+'</option>';
                        cpkv=0;
                       }else{
                       	cstrs=cstrs+'<option value="'+obj['data']['tp_file'][i]+'" >'+obj['data']['tp_file'][i]+'</option>';
                       }
                            }
	 	      		if(!cpkv){
	 	      			cstrs=cstrs+'<option value="'+obj['data'].a_c_tem+'" >'+obj['data'].a_c_tem+'</option>';
	 	      		}
	 	      		
	 	      		var app='<div class="edit_cent" id="edit_'+obj['data'].id+'"><ul>'+
	 	      		'<li>名称：<input id="edit_name_'+obj['data'].id+'" type="text" value="'+obj['data'].a_name+'"/></li>'+
	 	      		'<li>静态页面存放文件夹：<input id="edit_file_'+obj['data'].id+'" type="text" value="'+obj['data'].a_file+'"/></li>'+
	 	      		'<li> 栏目或者分类模板：<select id="edit_tem_'+obj['data'].id+'">'+
				       	strs+
				     '</select></li>'+
				      '<li>内容页模板：<select id="edit_c_tem_'+obj['data'].id+'">'+
				       cstrs+
				     '</select></li>'+
	 	      		'<input id="edit_id_'+obj['data'].id+'" type="hidden" value="'+obj['data'].id+'" />'+
	 	      		'<li style="float: right;margin-right: 20px;"><input onclick="tijiao('+obj['data'].id+')" type="button" value="提交"/></li>'+
	 	      		'</ul></div>';
	 	      		
	 	      		$(".edit").append(app);
	 	      		
	 	      		clos();
	 	      	}            
	 	      }else{
	 	      	alert('新增失败');
	 	      }
              
   });
}

function dlet(id){

	var data={
		id:id,
	};
	
	 $.post("http://"+host+"/admin/Index/Dele",data,function(result){
              var obj = new Function("return" + result)();//转换后的JSON对象             
	 	      if(obj.cod){	
	           $('#li_'+id).parent().remove();
	           $('#edit_'+id).remove();
               alert(obj.data);
	 	      }else{
	 	      	alert(obj.data);
	 	      }
              
   });
	
}

/////////////////栏目管理js////////////

////////////////内容管理js/////////
function zcdxs(id){
	var  d=$('#cl_'+id).css('display');
	if(d=='none'){
		$('#cl_'+id).css('display','block');
	}else{
		$('#cl_'+id).css('display','none');
	}
}
var huoqu=$('#huoqu').val();
$('#ccc_'+huoqu).parent().parent().css('display','block');
$('#ccc_'+huoqu).children().css('color','#FFFFFF').css('background','#666666');
function next(url,end_id){

	var idc=$('#en_'+end_id).length;
	
	if(!idc){//判断是否存在该翻页
	 $.get("http://"+host+"/admin/Index/"+url,function(result){
              var obj = new Function("return" + result)();//转换后的JSON对象  
              if(obj.cod){
              	   var tl= $('.content_cen_r table').length;
              	    
              	    var str='';
              	    for(i in obj['data']){
              	    	str=str+'<tr id="tr_'+obj['data'][i]['id']+'"><td>'+obj['data'][i]['id']+'</td><td id="title_'+obj['data'][i]['id']+'">'+obj['data'][i]['title']+'</td><td id="outline_'+obj['data'][i]['id']+'">'+obj['data'][i]['outline']+'</td><td><span onclick="xis(CKEDITOR,'+obj['data'][i]['id']+')">操作</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span onclick="dlet_essay('+obj['data'][i]['id']+')">删除</span></td></tr>';
              	    }
              	    var tb='<table id="en_'+obj['end_id']+'">'+
						'<tr><th style="width: 50px;">ID</th><th style="width: 150px;">标题</th><th style="width: 400px;">简介</th><th style="width: 80px;">操作</th></tr>'+
						
						str+
						
					'<tr><td style="text-align: center;" colspan="4"><span onclick="fornext('+tl+')">上一页</span><span   onclick="next(\'Content_list_next?c_id='+obj['c_id']+'&end_id='+obj['next']+'\','+obj['next']+')">下一页</span></td></tr>'
					'</table>';
				$('.content_cen_r table').css('display','none');	
				$('.content_cen_r').append(tb);	
              }else{
              	alert(obj.data);
              }
              
              
   });
  }else{
  	$('.content_cen_r table').css('display','none');
  	$('#en_'+end_id).css('display','block');
  }
}

function dlet_essay(id){
	var data={
		id:id,
	};
	
	 $.post("http://"+host+"/admin/Index/DeleEssay",data,function(result){
              var obj = new Function("return" + result)();//转换后的JSON对象             
	 	      if(obj.cod){		           
	           $('#tr_'+id).remove();
               alert(obj.data);
	 	      }else{
	 	      	alert(obj.data);
	 	      }
              
   });
}

function fornext(sid){//上一页
	var tb=$('.content_cen_r table');
	tb.css('display','none');
	$(tb[(sid-1)]).css('display','block');
}

function xis(obck,id){
	
	var data={
		id:id
	};
	 $.post("http://"+host+"/admin/Index/Find_content",data,function(result){
	 	 var obj = new Function("return" + result)();//转换后的JSON对象  
	 	 if(obj.cod){
	 	 	$('#list_title').val(obj['data'].title);
	 	 	$('#list_id').val(obj['data'].id);
	 	 	$('#class_c_'+obj['data'].class_c).prop("checked", "checked");
	 	 	$('#od_class').val(obj['data'].class_c);
	 	 	$('#list_author').val(obj['data'].author);
	 	 	$('#list_outline').val(obj['data'].outline);
	 	 	$('#outline_img').val(obj['data'].outline_img);
	 	 	$('#list_im').attr('src',obj['data'].outline_img);
	 	 	obck.instances.editorl.setData(obj['data'].content);
	 	 	$('.zezao').css('display','block');
	 	 }
	 	
	 });
}

function list_et(obck){
	var id=$('#list_id').val();
	var content=obck.instances.editorl.getData();
	var title=$('#list_title').val();
	var class_c=$('input[name="class_c"]:checked').val();
	var author=$('#list_author').val();
	var outline=$('#list_outline').val();
	var outline_img=$('#outline_img').val();
	var od=$('#od_class').val();
	
	var data={
		id:id,
		content:content,
		title:title,
		class_c:class_c,
		author:author,
		outline:outline,
		outline_img:outline_img
	};
	 $.post("http://"+host+"/admin/Index/PostInfo",data,function(result){
	 	 var obj = new Function("return" + result)();//转换后的JSON对象  
	 	 if(obj.cod){
	 	 	$('#title_'+obj['data'].id).html(obj['data'].title);
	 	 	$('#outline_'+obj['data'].id).html(obj['data'].outline); 
	 	 	if(obj['data'].class_c!=od){
	 	 		$('#tr_'+obj['data'].id).remove(); 
	 	 	}
	 	 	clos();
	 	 }else{
	 	 	alert('没有任何修改');
	 	 }
	 });
}

////////////////内容管理js/////////

////////////////系统更新js/////////
function system_save(){
	var data={
		id:''
	};
	$.post("http://"+host+"/admin/System/Class_file",data,function(result){
		 var obj = new Function("return" + result)();//转换后的JSON对象 
		 if(obj.cod){
		 	alert(obj.data)
		 }else{
		 	alert(obj.data)
		 }
	});
}
////////////////系统更新js/////////
function add_essay(){
	$('.zezao').css('display','block');
}
function add_essay1(){
	$('.zezao1').css('display','block');
}
function nextsys(url,end_id){

	var idc=$('#en_'+end_id).length;
	
	if(!idc){//判断是否存在该翻页
	 $.get("http://"+host+"/admin/System/"+url,function(result){
              var obj = new Function("return" + result)();//转换后的JSON对象  
              if(obj.cod){
              	   var tl= $('.reess_r table').length;
              	    
              	    var str='';
              	    for(i in obj['data']){
              	    	str=str+'<tr id="tr_'+obj['data'][i]['id']+'"><td>'+obj['data'][i]['id']+'</td><td id="title_'+obj['data'][i]['id']+'">'+obj['data'][i]['title']+'</td><td id="essay_'+obj['data'][i]['id']+'">'+obj['data'][i]['essay']+'</td><td><span onclick="syscz('+obj['data'][i]['id']+')">操作</span></td></tr>';
              	    }
              	    var tb='<table id="en_'+obj['end_id']+'">'+
						'<tr><th style="width: 50px;">ID</th><th style="width: 150px;">标题</th><th style="width: 400px;">所选推荐位</th><th style="width: 80px;">操作</th></tr>'+
						
						str+
						
					'<tr><td style="text-align: center;" colspan="4"><span onclick="fornextsys('+tl+')">上一页</span><span   onclick="nextsys(\'RecommendEssayListNext?c_id='+obj['c_id']+'&end_id='+obj['next']+'\','+obj['next']+')">下一页</span></td></tr>'
					'</table>';
				$('.reess_r table').css('display','none');	
				$('.reess_r').append(tb);	
              }else{
              	alert(obj.data);
              }
              
              
   });
  }else{
  	$('.reess_r table').css('display','none');
  	$('#en_'+end_id).css('display','block');
  }
}

function fornextsys(sid){
	var tb=$('.reess_r table');
	tb.css('display','none');
	$(tb[(sid-1)]).css('display','block');
}
function syscz(id){
	var data={
		id:id
	};
	 $('#essay_recom input').attr('checked',false);
	$.post("http://"+host+"/admin/System/RecommendEssayEit",data,function(result){
		 var obj = new Function("return" + result)();//转换后的JSON对象 
		 if(obj.cod){
		 	for(i in obj['data']){
		 		$('#recom_'+obj['data'][i]['id']).attr('checked','checked');
		 	}
		 }
		 var tv=$('#title_'+id).html();
		 $('#essay_title').html(tv);
		 $('#essay_id').val(id);
		 $('.sys_essay_zezao').css('display','block');
	});
	
}

function sys_clos(){
	$('.sys_essay_zezao').css('display','none');
}

function re_essay_eit(){
	var str=''; 
	$('#essay_recom input:checkbox').each(function() {
        if($(this).attr('checked') =='checked') {
              str=str+','+$(this).val();
        }
     });
    var id= $('#essay_id').val();
   var data={
		id:id,
		recom:str
	};
	 
	$.post("http://"+host+"/admin/System/RecommendEssayEit_info",data,function(result){
		 var obj = new Function("return" + result)();//转换后的JSON对象 
		 console.log(obj);
		 if(obj.cod){
		 	$('#essay_'+id).html(obj['essay']);
		 	sys_clos();
		 	alert(obj['data']);
		 }else{
		 	alert(obj['data']);
		 }
		 
	});
	
}

function clos_page(){
	$('.zezao_page').css('display','none');
}
function eit_page(){//分页条数修改
	var id=$('#page_id').val();
	var page=$('#page_page').val();
	var data={
		id:id,
		page:page
	};
	$.post("http://"+host+"/admin/System/XiuClassPage",data,function(result){
		 var obj = new Function("return" + result)();//转换后的JSON对象 

		 if(obj.cod){
		 	$('#page_page_'+id).html(page);
		 	clos_page()
		 	alert(obj['data']);
		 }else{
		 	alert(obj['data']);
		 }
		 
	});
}
function vew_page(id){//单条分页显示
	var name=$('#page_name_'+id).html();
	var page=$('#page_page_'+id).html();
	
	$('#page_id').val(id);
	$('#page_name').val(name);
	$('#page_page').val(page);
	$('.zezao_page').css('display','block');
}
