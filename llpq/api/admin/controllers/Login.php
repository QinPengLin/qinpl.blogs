<?php
	class Login{
		 public function actionVewLogin(){
		 	$dir_login=str_replace('controllers','vendor/Index/login.html',__DIR__);
			include($dir_login);	
			exit;
		 }
	     public function actionLogin(){
	     	$name=$_POST['name'];
	     	$pass=$_POST['pass'];
		if(!empty($name) && !empty($pass)){
			Session_Start();
			$sysconfig_url=str_replace('controllers','sysconfig',__DIR__)."/config.config";
			$user=redconfig($sysconfig_url)['user'];
			
			$user=trim($user,'|');
			$user_arr=explode('|',$user);
			$user_d=array();
			$p=0;
			
			foreach($user_arr as $k=>$v){
				$user_v=explode('-',$v);
				if($user_v[1]==$name){
					$user_d=$user_v;
					$p=1;
					break;
				}
			}
			
			if(!$p){//用户名不存在
			    $url='http://'.$_SERVER['HTTP_HOST'].'/admin/Login/VewLogin';
				$ts="用户名不存在";
			    $dir_login=str_replace('controllers','vendor/Index/ts.html',__DIR__);
			    include($dir_login);
			}else{
				if($user_d[2]==$pass){
					               
                    $_SESSION['logo'] ='ok';  
				    $url='http://'.$_SERVER['HTTP_HOST'].'/admin/Index/SaveIndex';
				    $ts="登陆成功";
			        $dir_login=str_replace('controllers','vendor/Index/ts.html',__DIR__);
			        include($dir_login);
					
				}else{
					$url='http://'.$_SERVER['HTTP_HOST'].'/admin/Login/VewLogin';
			        $ts="密码错误";
			        $dir_login=str_replace('controllers','vendor/Index/ts.html',__DIR__);
			        include($dir_login);
				}
			}
			
			
		}else{
			$url='http://'.$_SERVER['HTTP_HOST'].'/admin/Login/VewLogin';
			$ts="登录名或者密码为空";
			$dir_login=str_replace('controllers','vendor/Index/ts.html',__DIR__);
			include($dir_login);
		}
	
	     }
	     
	     
	   
	   
	    }
?>