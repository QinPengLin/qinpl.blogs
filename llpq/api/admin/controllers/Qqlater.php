<?php
class Qqlater  extends base{
	private $dir;
	private $lm;
	public $itp;
	public function __construct(){
			parent::__construct();
			$this->lm=parent::ClassArr();
			$this->dir=__DIR__;
			$this->itp=str_replace('controllers','indextemplet',$this->dir);
		}
		
	public function actionIndex(){//QQ登录的回调地址
		$qc = new QC();
		$qq_callback=$qc->qq_callback();
		$qq_openid=$qc->get_openid();
		$qc = new QC($qq_callback,$qq_openid);
		if(isset($qq_openid) && !empty($qq_openid) && isset($qq_callback) && !empty($qq_callback)){
			$qq_info=$qc->get_user_info();//获取用户信息
			if(!isset($_SESSION)){session_start();}
            $re_url=$_SESSION['lsurl'];
            $m=new Model();
            $re=$m->table('user_type_q')->where(array('open_id'=>$qq_openid))->find();
            if($re){//存在就登录记录session并且进行修改用户信息表数据
            	$re_add=$m->table('user_type_q')->where(array('open_id'=>$qq_openid))->save(array(
            	                                             'open_id'=>$qq_openid,//openid
            	                                             'n_name'=>$qq_info['nickname'],//呢称
            	                                             'n_url'=>$qq_info['figureurl_2'],//头像路径
            	                                             'n_sex'=>$qq_info['gender'],//性别
            	                                             'n_year'=>$qq_info['year'],//出身年份
            	                                             'n_city'=>$qq_info['city']//城市
            	                                             ));
            	        $re_u=$m->table('user')->where(array('t_id'=>$re['id']))->find();
            	        unset($_SESSION['lsurl']);
            	    	
            	    	$_SESSION['u_logo']='ok';
            	    	$_SESSION['u_id']=$re_u['id'];
            	    	$_SESSION['u_info']=$re;//用户信息
            	    	header("Location:$re_url");
            }else{//不存在就创建并且记录登陆的session
            	$re_add=$m->table('user_type_q')->add(array(
            	                                             'open_id'=>$qq_openid,//openid
            	                                             'n_name'=>$qq_info['nickname'],//呢称
            	                                             'n_url'=>$qq_info['figureurl_2'],//头像路径
            	                                             'n_sex'=>$qq_info['gender'],//性别
            	                                             'n_year'=>$qq_info['year'],//出身年份
            	                                             'n_city'=>$qq_info['city']//城市
            	                                             ));
            	if($re_add){
            		$re_add_u=$m->table('user')->add(array(
            	                                             'name'=>$qq_info['nickname'],//用户名
            	                                             'type'=>1,//
            	                                             't_id'=>$re_add
            	                                             ));
            	    if($re_add_u){
            	    	unset($_SESSION['lsurl']);
            	    	
            	    	$_SESSION['u_logo']='ok';
            	    	$_SESSION['u_id']=$re_add_u;
            	    	$_SESSION['u_info']=array(
            	                                             'open_id'=>$qq_openid,//openid
            	                                             'n_name'=>$qq_info['nickname'],//呢称
            	                                             'n_url'=>$qq_info['figureurl_2'],//头像路径
            	                                             'n_sex'=>$qq_info['gender'],//性别
            	                                             'n_year'=>$qq_info['year'],//出身年份
            	                                             'n_city'=>$qq_info['city']//城市
            	                                             );//用户信息
            	    	header("Location:$re_url");
            	    }else{
            	    	    $m->table('user_type_q')->where(array('id'=>$re_add))->delete();
            	    	    echo '插入数据失败';
         	                exit;
            	    }
            	}else{
            		         echo '插入数据失败';
         	                exit;
            	}
            }
		}else{
			                echo '获取QQ登录信息错误';
         	                exit;
		}
	}
	public function actionLogin(){//QQ登录方法//开启SESSION临时保存上级页面用于登录成功后跳转
		if(!parent::u_c_l()){
			$get=$_GET;
			if(isset($get['qurl']) && !empty($get['qurl'])){
				         $get=$_GET;
				         $qurl=$get['qurl'];
				         $qurl_arr=explode('/',$qurl);
				         $qurl_arr[0]=php_decode($qurl_arr[0]);//解密
		                 if($this->jccshl($qurl_arr[0])){
		                    if(!isset($_SESSION)){session_start();}
		                    $_SESSION['lsurl']='http://'.HTTP_HOST.'/'.$qurl_arr[0].'/'.$qurl_arr[1].'.html';
			                $qc = new QC();
                            $qc->qq_login();
                            
                         }else{
                         	echo '参数不合法';
         	                exit;
                         }
                 }else{
       	                 echo '参数不存在或者为空';
         	             exit;
                  }    
         }else{
         	$get=$_GET;
         	if(isset($get['qurl']) && !empty($get['qurl'])){
				         $get=$_GET;
				         $qurl=$get['qurl'];
				         $qurl_arr=explode('/',$qurl);
				         $qurl_arr[0]=php_decode($qurl_arr[0]);//解密
				        }
         	$url='http://'.HTTP_HOST.'/'.$qurl_arr[0].'/'.$qurl_arr[1].'.html';
         	$ts='已经登录';
         	$dir_login=str_replace('controllers','vendor/Index/ts.html',__DIR__);
			include($dir_login);
         }  
	}
	
	public function jccshl($c){//检测传入加密参数是否存在
		$p=0;
		foreach($this->lm as $v){
			if($v['file_name']==$c){
				$p=1;
				break;
			}
			
		}
		return $p;
	}

	

	
}
?>