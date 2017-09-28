<?php
	class System  extends llpq{
		private $dir;
		private $url;
		private $www;//
		private $static;//公共文件夹web中url的static
		private $class;
		private $sysconfig_url;//系统配置文件路径
		public function __construct(){
			parent::__construct();
			$this->dir=__DIR__;
			$this->url=str_replace('controllers','vendor',$this->dir);
			$this->sysconfig_url=str_replace('controllers','sysconfig',$this->dir)."/config.config";
			$this->www="http://".HTTP_HOST."\\api\\admin\\vendor\\Index";
			$this->static="http://".HTTP_HOST."\\api\\admin\\vendor\\static";
			$this->class=0;
		}
		
		public function actionIndex(){//系统设置栏目缓存页面显示
			$this->class=1;
			include($this->url."/Index/system.html");
		}
		
		public function actionClass_file(){//系统设置栏目缓存执行方法
			
		  if(IS_POST){	
			$m=new Model();
			$re=$m->table('class')->select();
			$filestr='';
			foreach($re as $k=>$v){
				$filestr=$filestr.'|'.$v['id'].'-'.$v['name'].'-'.$v['p_id'].'-'.$v['file_name'];
			}
			$re=writeconfig($this->sysconfig_url,array('class'=>$filestr));//向系统配置文件写内容（写）
			if(!$re['cod']){
				$rer=savconfig($this->sysconfig_url,array('class'=>$filestr));//向系统配置文件写内容（写）
			}else{
				$rer=$re;
			}
			
			echo json_encode($rer);
			}
			
		}
		
		public function actionRecommend(){//加载首页推荐位列表
		  if(IS_GET){	
		  	$m=new Model();
			$all=10;//每页条数
           	$rel=$m->table('recommend')->order('`id` DESC')->limit(0,$all)->select();
           	$end=end($rel);
           	$next=$end['id']; 
           	}
           
		 include($this->url."/Index/recommend.html");
			
		}
		
		
		public function actionRecommend_next(){//推荐位列表翻页
		  if(IS_GET){	
		  	$m=new Model();
			$all=10;//每页条数
			$get=$_GET;
			(isset($get['end_id']) && !empty($get['end_id']))?$sta=$get['end_id']:$sta=0;			
           	$rel=$m->table('recommend')->where('WHERE id<'.$sta)->order('`id` DESC')->limit(0,$all)->select();
           	$end=end($rel);
           	$next=$end['id']; 
           	
           	 if($rel){
            	echo json_encode(array('cod'=>1,'data'=>$rel,'next'=>$next,'end_id'=>$sta));	
            }else{
            	echo json_encode(array('cod'=>0,'data'=>'加载失败'));
            }
           	}
           
	
			
		}
		
		
		public function actionRecommend_find(){//查出单个推荐位信息
		  if(IS_POST){	
		  	$m=new Model();
		
			$post=$_POST;
					
           	$rel=$m->table('recommend')->where(array('id'=>$post['id']))->find();
          
           	
           	 if($rel){
            	echo json_encode(array('cod'=>1,'data'=>$rel));	
            }else{
            	echo json_encode(array('cod'=>0,'data'=>'加载失败'));
            }
           	}
           
	
			
		}
		
		
		public function actionRecommend_rew(){//修改推荐位
		  if(IS_POST){	
		  	$m=new Model();
		
			$post=$_POST;
					
           	$rel=$m->table('recommend')->where(array('id'=>$post['id']))->save(array(
           	                                                                          'name'=>$post['name'],
           	                                                                          'synopsis'=>$post['synopsis']
           	                                                                          ));
          
           	
           	 if($rel){
            	echo json_encode(array('cod'=>1,'data'=>$post['id']));	
            }else{
            	echo json_encode(array('cod'=>0,'data'=>'修改失败'));
            }
           	}
		}
		
		
		public function actionRecommend_add(){//新增推荐位
		  if(IS_POST){	
		  	$m=new Model();
		
			$post=$_POST;
					
           	$rel=$m->table('recommend')->add(array(
           	                                                                          'name'=>$post['name'],
           	                                                                          'synopsis'=>$post['synopsis']
           	                                                                          ));
          
           	
           	 if($rel){
            	header("Location: http://".HTTP_HOST."/admin/System/Recommend"); 	
            }else{
            	echo '新增失败';
            }
           	}
			
		}
		
		public function actionRecommend_del(){//删除选中推荐位
		  if(IS_POST){	
		  	$m=new Model();
		
			$post=$_POST;
					
           	$rel=$m->table('recommend')->where(array('id'=>$post['id']))->delete();
          
           	
           	 if($rel){
            	echo json_encode(array('cod'=>1,'data'=>'删除成功'));	
            }else{
            	echo json_encode(array('cod'=>0,'data'=>'删除失败'));
            }
           	}
			
		}
		
		public function actionRecommendEssay(){//显示文章分类列表
		 	$m=new Model();
		 	$re=$m->table('class')->select();
		 	$c_1=array();
           foreach($re as $ks=>$vs){//将各			
				for($i=1;$i<4;$i++){
					if($vs['rate']==$i){
					   $c_1[$i][]=$vs;
				    }
				}
			}
			include($this->url."/Index/recomm_essay.html");
		}
		
		
		public function actionRecommendEssayListIndex(){//显示文章分类列表
		 	$m=new Model();
		 	$re=$m->table('class')->select();
		 	$c_1=array();
           foreach($re as $ks=>$vs){//将各			
				for($i=1;$i<4;$i++){
					if($vs['rate']==$i){
					   $c_1[$i][]=$vs;
				    }
				}
			}
			
			
			$get=$_GET;
			
			if(isset($get['c_id']) && !empty($get['c_id'])){
            $recommend=$m->table('recommend')->select();
           	$all=10;//每页条数
           	$rel=$m->table('essay')->where(array('class_c'=>$get['c_id']))->order(' `id` DESC')->limit(0,$all)->select();
           	foreach($rel as $k=>$v){
           		foreach($recommend as $k1=>$v1){
           			$exarr=explode(',',$v1['essay_id']);
           			if(in_array($v['id'],$exarr)){
           				@$rel[$k]['essay']=$rel[$k]['essay'].','.$v1['name'];
           			}else{
           				@$rel[$k]['essay']=$rel[$k]['essay'];
           			}
           		}
           	}
           	$end=end($rel);
           	$next=$end['id'];         
           }
			
			include($this->url."/Index/recomm_essay.html");
		}
		
		
		
		public function actionRecommendEssayListNext(){//显示文章分类列表翻页
		 	$m=new Model();
			$get=$_GET;
			
			if(isset($get['c_id']) && !empty($get['c_id'])){
            $recommend=$m->table('recommend')->select();
           	$all=10;//每页条数
           	$rel=$m->table('essay')->field(array('title','id','class_c'))->where('WHERE class_c='.$get['c_id'].' AND id<'.$get['end_id'])->order(' `id` DESC')->limit(0,$all)->select();
           	foreach($rel as $k=>$v){
           		foreach($recommend as $k1=>$v1){
           			$exarr=explode(',',$v1['essay_id']);
           			if(in_array($v['id'],$exarr)){
           				@$rel[$k]['essay']=$rel[$k]['essay'].','.$v1['name'];
           			}else{
           				@$rel[$k]['essay']=$rel[$k]['essay'];
           			}
           		}
           	}
           	$end=end($rel);
           	$next=$end['id'];         
           }
			
			 if($rel){
            	echo json_encode(array('cod'=>1,'data'=>$rel,'next'=>$next,'c_id'=>$get['c_id'],'end_id'=>$get['end_id']));	
            }else{
            	echo json_encode(array('cod'=>0,'data'=>'加载失败'));
            }
		}
		
		public function actionRecommendEssayEit(){//文章选中推荐位显示
		           if(IS_POST){
		           	$post=$_POST;
		           	$m=new Model();
		           	$recommend=$m->table('recommend')->where("WHERE essay_id LIKE '%,".$post['id'].",%'")->select();
		           	if($recommend){
            	                  echo json_encode(array('cod'=>1,'data'=>$recommend));	
                             }else{
            	                  echo json_encode(array('cod'=>0,'data'=>'加载失败'));
                            }
		           }
		}
		
		
		public function actionRecommendEssayEit_info(){//修改文章选择推荐位
		           if(IS_POST){
		           	$post=$_POST;
		        
		           	$post['recom']=trim($post['recom'],',');	
		           	$m=new Model();
		           	$recommend='';
		           	$cao=array();//不需要操作的id数组
		           
		           	if(!empty($post['recom'])){//不为空时查询出选中了的和之前拥有的
		           		$where="WHERE id in(".$post['recom'].") OR essay_id LIKE '%,".$post['id'].",%'";
		           		$rec=explode(',',$post['recom']);
		           	}else{//一个也没选中就是只查询出拥有的
		           		$where="WHERE essay_id LIKE '%,".$post['id'].",%'";
		           		$rec=array();
		           	}
		            $recommend=$m->table('recommend')->field(array('id','name','essay_id'))->where($where)->select();
		            
		             $re_str='';
		            foreach($recommend as $v){
		               foreach($rec as $v6){
		            	if($v6==$v['id']){
		            	   $re_str=$re_str.','.$v['name'];
		            	}
		            	}
		            }
		           	
                    if(!empty($recommend)){//处理掉不需要修改或者添加的
                    	foreach($recommend as $k=>$v){
                    		@$exarr=explode(',',$v['essay_id']);
                    		
                    		
                    		if(is_array($exarr)){
                    		if(in_array($post['id'],$exarr)){//文章id在其中就需要看该次循环的是否在选中的，如果在就删掉，不在就需要把该循环的文章id删除(之前选中里的)
                    		  if(!empty($rec)){
                    			foreach($rec as $v1){
                    				if($v1==$v['id']){//删除选中的id并且之前也选中
                    						$cao[]=$v['id'];
                    					
                    				}else{//没选中的id但是之前选中
                    					
                    					foreach($exarr as $k3=>$v3){
                    						if($v3==$post['id']){//删除没选中的id但是之前选中中的文章id
                    							unset($exarr[$k3]);
                    							$recommend[$k]['essay_id']=implode(',',$exarr);
                    					        $recommend[$k]['essay_id']=trim($recommend[$k]['essay_id'],',');
                    					        $recommend[$k]['essay_id']=','.$recommend[$k]['essay_id'].',';
                    						}
                    					}
                    					
                    					
                    				}
                    			}
                    			}else{
                    				foreach($exarr as $k3=>$v3){
                    						if($v3==$post['id']){//删除没选中的id但是之前选中中的文章id
                    							unset($exarr[$k3]);
                    							$recommend[$k]['essay_id']=implode(',',$exarr);
                    					        $recommend[$k]['essay_id']=trim($recommend[$k]['essay_id'],',');
                    					        $recommend[$k]['essay_id']=','.$recommend[$k]['essay_id'].',';
                    						}
                    					}
                    			}
                    			
                    		
                    		}else{//现在新增的选中的
                    			$recommend[$k]['essay_id']=$recommend[$k]['essay_id'].$post['id'].',';
                    			$recommend[$k]['essay_id']=trim($recommend[$k]['essay_id'],',');
                    			$recommend[$k]['essay_id']=','.$recommend[$k]['essay_id'].',';
                    		}
                    		}
                    		
                    		
                    	}
                    }
                    
                    foreach($recommend as $kl=>$vl){
                    	foreach($cao as $v){
                    		if($vl['id']==$v){
                    			unset($recommend[$kl]);
                    		}
                    	}
                    }
                   
                    foreach($recommend as $vc){
                    	$re=$m->table('recommend')->where(array('id'=>$vc['id']))->save(array('essay_id'=>$vc['essay_id']));
                    	if($re){
                    		$cod=1;
                    		
                    	}else{
                    		$cod=0;
                    		$en_name=$vc['name'];
                    		break;
                    	}
                    }

		           	 
		           	if($cod){
            	                  echo json_encode(array('cod'=>1,'data'=>'修改成功','essay'=>$re_str));	
                             }else{
            	                  echo json_encode(array('cod'=>0,'data'=>'修改到【'.$en_name.'】失败'));
                            }
		           }
		}
		
		
		
	public function actionClassPage(){//分页条数显示
		        $lmpage=redconfig($this->sysconfig_url);
		        $lmpage['class']=trim($lmpage['class'],'|');
		        $lmpage['lmpage']=trim($lmpage['lmpage'],'|');
		        $class_arr=explode('|',$lmpage['class']);
		        $classpage_arr=explode('|',$lmpage['lmpage']);
		        $re=array();//显示数据
		        foreach($class_arr as $k=>$v){
		        	$classa_arr=explode('-',$v);
		        	$classa_arr[4]=6;
		        	foreach($classpage_arr as $v1){
		        		$classp_arr=explode('-',$v1);
		        		if($classp_arr[0]==$classa_arr[0]){
		        			$classa_arr[4]=$classp_arr[1];
		        		}
		        	}
		        	if($classa_arr[2]){
		        	   $re[$k]['id']=$classa_arr[0];
		        	   $re[$k]['name']=$classa_arr[1];
		        	   $re[$k]['page']=$classa_arr[4];
		        	}
		        }
		        
		        
	         	include($this->url."/Index/class_page.html");
	}
	
	
	
	public function actionXiuClassPage(){//单条分页条数显示
		if(IS_POST){
			$post=$_POST;
	         $lmpage=redconfig($this->sysconfig_url);
	         $lmpage['lmpage']=trim($lmpage['lmpage'],'|');
		     $class_arr=explode('|',$lmpage['lmpage']); 
		     $cun=0;
		     foreach($class_arr as $k=>$v){
		     	$classa_arr=explode('-',$v);
		     	if($classa_arr[0]==$post['id']){
		     		$classa_arr[1]=$post['page'];
		     		$class_arr[$k]=$classa_arr[0].'-'.$classa_arr[1];
		     		$cun=1;
		     		break;
		     	}
		     }
		     if(!$cun){
		     	$str_in=$post['id'].'-'.$post['page'];
		     	$in_re=implode('|',$class_arr);
		     	$in_re=trim($in_re,'|');
		     	$in_re='|'.$in_re.'|'.$str_in.'|';
		     }else{
		     	$in_re=implode('|',$class_arr);
		     	$in_re=trim($in_re,'|');
		     	$in_re='|'.$in_re.'|';
		     }
		     
		     $re=savconfig($this->sysconfig_url,array('lmpage'=>$in_re));
		     echo json_encode($re);	
		    }
	}
	
	
	public function actionKeyWords(){//显示和设置网站关键字
		if(IS_GET){//显示
			$kwarr=array();
			foreach(redconfig($this->sysconfig_url) as $k=>$v){
				if($k=='syskw'){
					$kwarr=explode('|',trim($v,'|'));
				}
			}
			isset($kwarr[0])?$title=$kwarr[0]:$title='';//标题
			isset($kwarr[1])?$keywords=$kwarr[1]:$keywords='';//关键词
			isset($kwarr[2])?$description=$kwarr[2]:$description='';//介绍
			include($this->url."/Index/system_keywords.html");
		}
		if(IS_POST){
			$post=$_POST;
			
			isset($post['title'])?$title=$post['title']:$title='';//标题
			isset($post['keywords'])?$keywords=$post['keywords']:$keywords='';//关键词
			isset($post['description'])?$description=$post['description']:$description='';//介绍
			
			$info_str='|'.$title.'|'.$keywords.'|'.$description.'|';
			
			$ire=savconfig($this->sysconfig_url,array('syskw'=>$info_str));
			if($ire['cod']){
				
				include($this->url."/Index/system_keywords.html");
			}else{
				$kre=writeconfig($this->sysconfig_url,array('syskw'=>$info_str));
				if($kre['cod']){
					include($this->url."/Index/system_keywords.html");
				}else{
					print_r($kre); 
				}
				
			}
		}
		
	}
	
	
	public function actionSitemap(){//网站地图生成
		
	}
	
	
		
}
?>