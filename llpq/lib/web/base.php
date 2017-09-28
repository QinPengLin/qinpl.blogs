<?php
class base{
	public $class_arr='';//栏目及其分类数组
	private $sysconfig_url='';
	private $sysdict_url='';
	private $dir='';
	public $sys_arr='';


	public function __construct(){
		 define('HTTP_HOST',$_SERVER['HTTP_HOST']);
		 define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
		 define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
		 define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
		 define('STATICL',"http://".HTTP_HOST."\\api\\admin\\vendor\\static");
		 define('SYSCONFIG',  $this->sysconfig_url);
		
		 $this->dir=__DIR__;
		 
		 $dir_ex=explode("/",$this->dir);
		 if(in_array('runtime',$dir_ex)){//判断是否开启缓存
		    $this->sysconfig_url=str_replace('lib/runtime/lib','api/admin/sysconfig',$this->dir)."/config.config";
		    $this->sysdict_url=str_replace('lib/runtime/lib','api/admin/sysconfig',$this->dir)."/dictionary.txt";		    
		 }else{
		 	$this->sysconfig_url=str_replace('lib/web','api/admin/sysconfig',$this->dir)."/config.config";
		 	$this->sysdict_url=str_replace('lib/web','api/admin/sysconfig',$this->dir)."/dictionary.txt";
		 }
		 $this->sys_arr=lmpage($this->sysconfig_url);
		 $this->GetKw();
	}
	public function GetKw(){//获取
		foreach(redconfig($this->sysconfig_url) as $k=>$v){
				if($k=='syskw'){
					$kwarr=explode('|',trim($v,'|'));
				}
			}
			
			isset($kwarr[0])?$sys_kw['title']=$kwarr[0]:$sys_kw['title']='';//标题
			isset($kwarr[1])?$sys_kw['keywords']=$kwarr[1]:$sys_kw['keywords']='';//关键词
			isset($kwarr[2])?$sys_kw['description']=$kwarr[2]:$sys_kw['description']='';//介绍
			return $sys_kw;
	}
	public function ClassArr(){//将缓存中的栏目信息取出来解析成可用数组		
		$class_str=redconfig($this->sysconfig_url)['class'];	

		$class_str=trim($class_str,'|');
		$class_arr=explode('|',$class_str);
		$class_a=array();
		foreach($class_arr as $k=>$v){
			if(!empty($v)){
			     $va=explode('-',$v);
			     $class_a[$k]['id']=$va[0];
			     $class_a[$k]['name']=$va[1];
			     $class_a[$k]['p_id']=$va[2];
			     $class_a[$k]['file_name']=$va[3];
			}
		}
		return $class_a;
	}
	
	public function TpIndex(){			
		return redconfig($this->sysconfig_url)['templet_index'];
	}
	
	public function recommend($id,$sl=3){//获取指定推荐位列表
		
		date_default_timezone_set("PRC");//设置时区
		
	$m=new Model();
	$re=$m->table('recommend')->field(array('id','essay_id'))->where(array('id'=>$id))->find();
	$exarr=trim($re['essay_id'],',');
	if($exarr){
	$re_ess=$m->table('essay')->field(array('id','outline','author','outline_img','class_c','title','create_time'))->where('WHERE id in('.$exarr.')')->order(' `id` DESC')->limit(0,$sl)->select();	
	foreach($re_ess as $k=>$v){
		foreach($this->ClassArr() as $v1){
			if($v['class_c']==$v1['id']){
				$re_ess[$k]['class_c']=$v1['name'];
				$re_ess[$k]['html_url']="http://".HTTP_HOST."/".$v1['file_name']."/".$v['id'].".html";
				$re_ess[$k]['class_url']="http://".HTTP_HOST."/".$v1['file_name']."/";				
			}
		}
		$re_ess[$k]['create_time']=date('Y-m-d',$re_ess[$k]['create_time']);
	}
	}else{
		$re_ess=array();
	}

	return $re_ess;	            
}

   public function essayNew($count=10){//获取最新文章
	   $m=new Model(); 
	   $re_ess=$m->table('essay')->field(array('id','class_c','title'))->order(' `create_time` DESC')->limit(0,$count)->select();
	   if($re_ess){
	   foreach($re_ess as $k=>$v){
	   	   foreach($this->ClassArr() as $v1){
			if($v['class_c']==$v1['id']){
				$re_ess[$k]['html_url']="http://".HTTP_HOST."/".$v1['file_name']."/".$v['id'].".html";	
			}
		}
	   }
	   }else{
		$re_ess=array();
	}
	   
	return $re_ess;	
   }
   
   
   public function classNew($calss,$count=10){//分类获取最新文章
	   $m=new Model(); 
	   $re_ess=$m->table('essay')->field(array('id','class_c','title'))->where(array('class_c'=>$calss))->order(' `create_time` DESC')->limit(0,$count)->select();
	  if($re_ess){
	   foreach($re_ess as $k=>$v){
	   	   foreach($this->ClassArr() as $v1){
			if($v['class_c']==$v1['id']){
				$re_ess[$k]['html_url']="http://".HTTP_HOST."/".$v1['file_name']."/".$v['id'].".html";	
			}
		}
	   }
	    }else{
		$re_ess=array();
	}
	return $re_ess;	
   }
   
   
   public function leach($info){//过滤输入
   	
   	   return perk($info,$this->sysdict_url);
   }
   
   public function u_c_l(){
   	     Session_Start();
		if(isset($_SESSION['u_logo']) &&  $_SESSION['u_logo']=='ok'){
			return true;
		}else{
			return false;
		}
   }
   
}
?>