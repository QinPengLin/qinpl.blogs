<?php
class Reveal  extends base{
	private $dir;
	private $lm;
	public $itp;
	public function __construct(){
			parent::__construct();
			$this->lm=parent::ClassArr();
			$this->dir=__DIR__;
			$this->itp=str_replace('controllers','indextemplet',$this->dir);
		}
		
		
	public function actionInfo(){//留言输入
		
	  if(IS_POST){
	  	date_default_timezone_set('PRC');
	  	if(parent::u_c_l()){	
	       $post=$_POST;
	       if(isset($post['info']) && !empty($post['info']) && isset($post['a_id']) && !empty($post['a_id']) && isset($post['id'])){
	              $m=new Model();
	              $f_se=$m->table('essay')->where(array('id'=>$post['a_id']))->find();
	              if($f_se){
	              	$filename='';
	              	foreach($this->lm as $v){
	              		if($v['id']==$f_se['class_c']){
	              			$filename=$v['file_name']."/".$post['a_id'].".html";
	              			break;
	              		}
	              	}
	              	if($filename){
	              		    if($post['id']==0){
	              	 	     $f_dse=1;
	              	 	     $p_name='';//父级评论者呢称
	              	 	     }else{
	              	 	   	 $f_dse=$m->table('discuss')->where(array('id'=>$post['id']))->find();
	              	 	   	 $p_name=$f_dse['name'];//父级评论者呢称
	              	 	   	 if($f_dse['u_id']==$_SESSION['u_id']){
	              	 	   	 	echo json_encode(array('code'=>2,'mess'=>'自己不能给自己回复'));
	              	 	   	 	exit;
	              	 	   	 }
	              	 	     }
	              	 	     
	              	 	     if(($_SESSION['u_ditime']+(3*60))<time()){
	              	 	     if($f_dse){
	                               if(!isset($_SESSION)){session_start();}
	                               $gl_info=parent::leach($post['info']);//过滤留言
	                               $f_ds=$m->table('discuss')->add(array(
	                                                                      'name'=>$_SESSION['u_info']['n_name'],//评论者呢称
	                                                                      'u_id'=>$_SESSION['u_id'],//评论者ID
	                                                                      'text'=>$gl_info,//评论内容
	                                                                      'essay_id'=>$post['a_id'],//文章ID
	                                                                      'p_u_id'=>$post['id'],//父级评论ID
	                                                                      'info_time'=>date('Y-m-d H:i:s',time()),//评论时间
	                                                                      'p_name'=>$p_name,//父级评论者的呢称
	                                                                      'u_haerd'=>$_SESSION['u_info']['n_url'],//评论者的头像地址
	                                                                     ));
                                  if($f_ds){
                                  	$reclass=$m->table('class')->select();
                                  	
                                  	foreach($reclass as $v1){//将模板信息传递给Indexc
				                                   if($f_se['class_c']==$v1['id']){
					                                         $f_se['reclass']=$v1['c_templet_name'];
					                                         break;
				                                         }
				                            }

                                  	$urlc = "http://".HTTP_HOST."/admin/Reveal/Essay"; //网页地址
                                    $contents=curl_post($urlc,$f_se);//获取网页类容
                                    $handle=mkdir_file($filename);

                                    fwrite($handle,$contents);
                                    fclose($handle);
                                  	 
                                  	 $_SESSION['u_ditime']=time();
                                  	 echo json_encode(array('code'=>1,'mess'=>'评论成功'));
                                  }else{
                                  	echo json_encode(array('code'=>2,'mess'=>'插入数据时失败'));
                                  }
	                             
	                           }else{
	                           	echo json_encode(array('code'=>2,'mess'=>'父级评论不存在'));
	                           }
	                           }else{
	                           	echo json_encode(array('code'=>2,'mess'=>'你评论过快'));
	                           }
	                           
	                           	                 
	               }else{
	               	 echo json_encode(array('code'=>2,'mess'=>'系统错误'));
	               }
	              }else{
	              	echo json_encode(array('code'=>2,'mess'=>'评论文章不存在'));
	              }  
	       }else{
	       	echo json_encode(array('code'=>2,'mess'=>'评论内容不能为空'));
	       }
	   }else{
	   	   echo json_encode(array('code'=>0,'mess'=>'你没有登录,请先登录才能评论'));
	   }
	  } 
	}
	
	

	
		
	 public function actionEssay(){//内容页
	    	date_default_timezone_set("PRC");//设置时区
	    	$lm=$this->lm;//获取栏目数组
	    	
	    	
	    	$c_templet_name='';//内容页模板
	    	$m=new Model();
	    	if(IS_GET){
			
			 $re=$m->table('essay')->where(array('id'=>$_GET['id']))->find();
			 $reclass=$m->table('class')->select();
			 foreach($reclass as $k=>$v){
				if($re['class_c']==$v['id']){
					$parent_name_url=$v['file_name'];
					$c_templet_name=$v['c_templet_name'];
				}
			}
		
			}
			if(IS_POST){

				$re=$_POST;
				$c_templet_name=$_POST['reclass'];
			}
				
			$parent_name='';//父级分类名称
			$parent_name_url='';
			$p_parent_name='';//父级栏目
			$p_parent_name_url='';
			$c_p_id='';
			foreach($lm as $v){
				if($v['id']==$re['class_c']){
					
					$parent_name=$v['name'];
					$parent_name_url=$v['file_name'];
					foreach($lm as $v1){
						if($v['p_id']==$v1['id']){
							$p_parent_name=$v1['name'];
							$p_parent_name_url=$v1['file_name'];
							$c_p_id=$v1['id'];
						}
					}
				}
				
			}
			
			$re_discuss=$m->table('discuss')->where(array('essay_id'=>$re['id']))->select();
			$re_discuss=json_encode($re_discuss);
			$content=$re['content'];
			$essay_time=date('Y-m-d h',$re['create_time']);
			$tpurl=str_replace('controllers','indextemplet',$this->dir).$c_templet_name;
			//替换example内容，并获取内容赋值给$str
			include($tpurl);
			}
			
	 public function actionColumn(){//栏目页
	 	date_default_timezone_set("PRC");//设置时区
	 	    $lmp=$this->sys_arr;//栏目每页显示条数数组
	 	
	 	  
	    	
	    	$lm=$this->lm;//获取栏目数组
	    	
	    	$m=new Model();
	    	$c_templet_name='';//栏目页模板
	    	
	    	if(IS_GET){
			
			 $re=$m->table('class')->where(array('id'=>$_GET['id']))->find();			
					$c_templet_name=$re['templet_name'];
					$c_id=$_GET['id'];
					
					$page=$_GET['page'];
					
			}
			if(IS_POST){//如果是网站更新就会用post把所需数据传递过来
				$re=$_POST;
				$c_templet_name=$re['templet_name'];
				$c_id=$re['id'];
				$page=$re['page'];
				if(isset($re['all'])){
					$pageall=$re['all'];
				}else{
					$pageall='';
				}
				
				$thtml=1;
			}
			
			
			
			
			
			
			
			
				$all=6;
					foreach($lmp as $lv){
					
						if($lv['id']==$c_id){
							$all=$lv['page'];
						}
					}
					
					if(isset($page) && !empty($page) && is_numeric($page) && $page>1){
						$sta=($page-1)*$all;
						if(isset($thtml) && !empty($thtml)){//静态分页
							if(($page-1)==1){
								if($page<$pageall-1 || $page==$pageall-1){
								  $page_str='<a href="index.html">&lt;&lt;</a><a href="index_'.($page+1).'.html">&gt;&gt;</a>';
								}else{
								  $page_str='<a href="index.html">&lt;&lt;</a>';
								}
							}else{
								
								if($page<$pageall-1 || $page==$pageall-1){
								$page_str='<a href="index_'.($page-1).'.html">&lt;&lt;</a><a href="index_'.($page+1).'.html">&gt;&gt;</a>';
								}else{
								$page_str='<a href="index_'.($page-1).'.html">&lt;&lt;</a>';
								}
							} 
						}else{//动态分页
								$page_str='<a href="Column?id='.$c_id.'&page='.($page-1).'">&lt;&lt;</a><a href="Column?id='.$c_id.'&page='.($page+1).'">&gt;&gt;</a>';	
						}
					
					}else{
						$sta=0;
						if(isset($thtml) && !empty($thtml)){//静态分页
						   if($pageall>1){
						 	$page_str='<a href="index_2.html">&gt;&gt;</a>'; //有问题
						 }else{
						 	$page_str='';
						 }
						}else{//动态分页
							$page=1;
							$page_str='<a href="Column?id='.$c_id.'&page=2">&gt;&gt;</a>';
						}
					}
					if($page){
			           $re_ess=$m->table('essay')->field(array(
			                                                'id','outline','author','outline_img','class_c','title','create_time'
			                                                ))->where('WHERE class_c='.$c_id)->order(' `id` DESC')->limit($sta,$all)->select();
			        
			         }else{
			         	$re_ess=array();
			         }                    
			     
			      if(!$re_ess){
			      	$page_str='';
			      }
				
			
			
			//$re_ess为分页列表数组  $page_str为分页显示
			
			foreach($re_ess as $k=>$v){
				$re_ess[$k]['create_time']=date('Y-m-d',$v['create_time']);
				foreach($lm as $vse){
					if($vse['id']==$v['class_c']){
					   $re_ess[$k]['class_c']=$vse['name'];
					   $re_ess[$k]['html_url']="http://".HTTP_HOST."/".$vse['file_name']."/".$v['id'].".html";;
					}
				}
			}
			
			
			
			
			$tpurl=str_replace('controllers','indextemplet',$this->dir).$c_templet_name;
			$c_name='';
			$c_p_id='';
            foreach($lm as $v){
            	if($v['id']==$c_id){
            		$c_name=$v['name'];
            		if($v['p_id']==0){
            		    $c_p_id=$c_id;
            		}else{
            			$c_p_id=$v['p_id'];
            		}
            	}
            }
			//替换example内容，并获取内容赋值给$str
			include($tpurl);
			}
	
			
	public function actionIndex(){//网站首页
//ob_start();

		
	       $lm=$this->lm;//获取栏目数组
	    	
	    
	    
	    	$c_templet_name='';//首页模板
	    	
	    	if(IS_GET){    		    			 		
					$c_templet_name=parent::TpIndex();
			}
			if(IS_POST){//如果是网站更新就会用post把所需数据传递过来
				$re=$_POST;
				$c_templet_name=$re['tp_index'];
			}
			
			
		
			
			$tpurl=str_replace('controllers','indextemplet',$this->dir).$c_templet_name;
			
			
			//替换example内容，并获取内容赋值给$str
			include($tpurl);
			
			//$content = ob_get_contents();
			
			
			//echo $content;
	}
	
	
	
	public function actionCesia(){//线上测试页面，搜狐最新资讯通过get方式访问http://news.sohu.com/_scroll_newslist/20170609/news.inc地址获取文章链接地址
	
	 
	 ini_set("display_errors", "On");
     error_reporting(E_ALL | E_STRICT);
	 
	 
	 
	   $urlarray=array(
	                  // array("url"=>"http://news.qq.com/a/20170608/046581.htm"),//腾讯新闻
	                   array("url"=>"http://www.qq.com/","params"=>array()),//腾讯新闻
	                   //array("url"=>"http://news.sina.com.cn/"),//新浪新闻
	                  // array("url"=>"http://news.sohu.com/scroll/"),//搜狐全部资讯
	                   );
	   $data=mfetch($urlarray,'get');
	   $rehrefarray=array();

	   foreach($data as $data_v){
	   $html = $data_v['content'];
	   
	   
       
	                            $html = str_replace("\r\n", '', $html); //清除换行符 
                                $html = str_replace("\n", '', $html); //清除换行符 
                                $html = str_replace("\t", '', $html); //清除制表符 
                                $html = str_replace(" ", '', $html); //清除空格符 
	   
	   preg_match_all('/href="([^"]*)"([^"]*)>([^"]*)<\/a>/',$html,$array);
	 
	   
	   
	   $t=time();
	   $str=date('Ymd',$t);//腾讯新闻
	   $strt=date('Y-m-d',$t);//新浪新闻
	   $strf=date('Y-m/d',$t);//360新闻
	   $str_arr=array($str,$strt,$strf);//日期筛选条件数组（可配置）
	   $str_arr_h=array('.html','.htm','.shtml','</a>');//后缀筛选条件（可配置）
	   $str_arr_a=array('</a>');//后缀筛选条件（可配置）
	   $str_arr_n=array('#','?','&');//不能存在字符（可配置）
	   $hrefarray=array();
	   if(count($array) == count($array,1)){//是否是一维数组
	   	foreach($array as $vw){
	   	       if((strpos_qin($str_arr,$vw) && strpos_qin($str_arr_h,$vw)) && !strpos_qin($str_arr_n,$vw) && strpos_qin($str_arr_a,$vw)){
	   	       	$bm=mb_detect_encoding($vw, array('ASCII','GB2312','GBK','UTF-8'));
	   	       	if($bm!='UTF-8'){
	   	       		$keytitle = iconv($bm,"UTF-8",$vw); 
	   	       	    $hrefarray[]=$keytitle;
	   	         }
	   	         }
	   	        
	   	    }
	   }else{//不是一维数组（只实现二位数组）
	   	
	   	  foreach($array as $v){
            foreach($v as $vw){           	
	   	       if((strpos_qin($str_arr,$vw) && strpos_qin($str_arr_h,$vw)) && !strpos_qin($str_arr_n,$vw) && strpos_qin($str_arr_a,$vw)){
	   	       	$bm=mb_detect_encoding($vw, array('ASCII','GB2312','GBK','UTF-8'));
	   	       	if($bm!='UTF-8'){
	   	       		$keytitle = iconv($bm,"UTF-8",$vw); 
	   	       	    $hrefarray[]=$keytitle;
	   	         }
	   	         } 
	   	    }
	       }
	   }
	  // $rehrefarray[]=$hrefarray;//去重放入数组
	   $rehrefarray[]=array_unique($hrefarray);//去重放入数组
	   }
	   
	   

$h_url=array();
foreach($rehrefarray as $b_v){
foreach($b_v as $v_v){
	
		$ex_arr=explode('"',$v_v);
		if(in_array('href=',$ex_arr)){//如果存在href就需要去除href
	   	       	    	foreach($ex_arr as $ex_v){
	   	       	    		if(strpos_qin($str_arr,$ex_v)){
	   	       	    			foreach($ex_arr as $kx_v){
	   	       	    				if(strpos_qin(array('</a>'),$kx_v)){
	   	       	    			    $h_url[]=array("url"=>$ex_v,"params"=>array(),'title'=>$kx_v); 
	   	       	    			    }
	   	       	    			   } 
	   	       	    			}
	   	       	    		}
	   	       	    	
		}else{
		$h_url[]=array("url"=>$v_v,"params"=>array());
		}
	
}
}

$h_url=array_slice($h_url,0,30);
	   $contents=array();
	   foreach($h_url as $k=>$v_u){
	   	 $contents[$k]['c']=curl_get($v_u['url']);//获取网页类容
	   	 $contents[$k]['t']=$v_u['title'];//获取网页类容
	   }
	   
	
	print_r($contents);
	exit;
	
	$str_html=array(); 
	
	foreach($contents as $all_v){
	
	$stry=(string)$all_v['c'];
	
                                
                                // 新建一个Dom实例
$html = new simple_html_dom();
		   // 从字符串中加载
$html->load($stry);
$a = $html->find('div');
foreach($a as $k=>$e){ 
	if($e->class=='Cnt-Main-Article-QQ'){
		 $str_html[$k]['c']=$e->innertext;
		 $str_html[$k]['t']=$all_v['t'];
	}
}
}
	   
	   
	 print_r($str_html);
	   
	   
	   
	}
	
}

function strpos_qin($str_array=array(),$str){//简化判断语句，适用于或运算
		$re=0;
		foreach($str_array as $v){
			if(strpos($str,$v)){
			  $re=1;
			  break;	
			}
		}
		return $re;
		
	}
?>