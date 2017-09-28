<?php
class llpq{
	private $sysconfig_url='';
	private $dir='';
	public function __construct(){
		 define('HTTP_HOST',$_SERVER['HTTP_HOST']);
		 define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
		 define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
		 define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
		 $this->dir=__DIR__;
		 $dir_ex=explode("/",$this->dir);
		
		 if(in_array('runtime',$dir_ex)){//判断是否开启缓存
		    $this->sysconfig_url=str_replace('lib/runtime/lib','api/admin/sysconfig',$this->dir)."/config.config";
		 }else{
		 	$this->sysconfig_url=str_replace('lib/web','api/admin/sysconfig',$this->dir)."/config.config";
		 }
		$this->verifLogin();
	}
	
	public function verifLogin(){//验证登录
		Session_Start();
		
		
		if(isset($_SESSION['logo']) &&  $_SESSION['logo']=='ok'){
			return true;
		}else{//跳转登录页面
			 $dir_ex=explode("/",$this->dir);
		
		 if(in_array('runtime',$dir_ex)){//判断是否开启缓存
		    
		    $dir_login=str_replace('lib/runtime/lib','api/admin/vendor/Index/login.html',$this->dir);
		 }else{
		 	
		 	$dir_login=str_replace('lib/web','api/admin/vendor/Index/login.html',$this->dir);
		 }
			
			include($dir_login);	
			exit;
		}
		
	}
	
	public function outLogin(){//验证登录
	     
		
		
		if(isset($_SESSION['logo']) &&  $_SESSION['logo']=='ok'){
			$_SESSION['logo']=null;
			if(empty($_SESSION['logo'])){
				 $dir_ex=explode("/",$this->dir);
		
		 if(in_array('runtime',$dir_ex)){//判断是否开启缓存
		    
		    $dir_login=str_replace('lib/runtime/lib','api/admin/vendor/Index/login.html',$this->dir);
		 }else{
		 	
		 	$dir_login=str_replace('lib/web','api/admin/vendor/Index/login.html',$this->dir);
		 }
			    include($dir_login);	
			    exit;
			}
		}
	}
	
	
}
?>